<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        protected VoucherService $voucherService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'delivery_address' => ['required', 'string'],
            'customer_phone' => ['required', 'string', 'max:15'],
            'note' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'in:cod,bank'],
            'shipping_fee' => ['nullable', 'integer', 'min:0'],
            'voucher_code' => ['nullable', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'context' => ['nullable', 'array'],
            'context.placed_at' => ['nullable', 'date'],
            'context.is_student_verified' => ['nullable', 'boolean'],
        ]);

        $userId = auth()->id() ?? (int) ($validated['user_id'] ?? 0);

        if ($userId === 0) {
            throw ValidationException::withMessages([
                'user_id' => 'Can dang nhap de tao don hang.',
            ]);
        }

        $orderDraft = $this->voucherService->buildCartSummary(
            (int) $validated['shop_id'],
            $validated['items']
        );

        $shippingFee = (int) ($validated['shipping_fee'] ?? 15000);
        $quote = null;

        if (! empty($validated['voucher_code'])) {
            $quote = $this->voucherService->preview(
                $validated['voucher_code'],
                (int) $validated['shop_id'],
                $userId,
                $orderDraft,
                $shippingFee,
                $validated['context'] ?? []
            );
        }

        $order = DB::transaction(function () use ($validated, $orderDraft, $quote, $shippingFee, $userId) {
            $order = Order::create([
                'order_code' => $this->generateOrderCode(),
                'user_id' => $userId,
                'shop_id' => (int) $validated['shop_id'],
                'subtotal' => (int) $orderDraft['subtotal'],
                'shipping_fee' => $shippingFee,
                'discount' => (int) ($quote['discount'] ?? 0),
                'total' => (int) ($quote['total'] ?? ((int) $orderDraft['subtotal'] + $shippingFee)),
                'delivery_address' => $validated['delivery_address'],
                'customer_phone' => $validated['customer_phone'],
                'note' => $validated['note'] ?? null,
                'voucher_code' => $quote['voucher_data']['code'] ?? null,
                'payment_method' => $validated['payment_method'] ?? 'cod',
                'status' => 'pending',
            ]);

            foreach ($orderDraft['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_group' => $item['product_group'] ?? null,
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($quote) {
                $quote['voucher']->increment('used_count');
            }

            return $order->load('items');
        });

        return response()->json([
            'message' => 'Tao don hang thanh cong.',
            'data' => [
                'order' => $order,
                'voucher' => $quote['voucher_data'] ?? null,
            ],
        ], 201);
    }

    protected function generateOrderCode(): string
    {
        do {
            $code = 'FH-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }
}
