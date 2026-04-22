<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    private const SESSION_KEY = 'checkout_order';

    public function index(Request $request, VoucherService $voucherService)
    {
        $draft = $this->draft();
        $shop = null;
        $orderItems = collect();
        $subtotal = 0;

        if ($draft !== null && ! empty($draft['items'])) {
            $summary = $voucherService->buildCartSummary((int) $draft['shop_id'], $draft['items']);
            $orderItems = $summary['items'];
            $subtotal = (int) $summary['subtotal'];
            $shop = Shop::find($draft['shop_id']);
        }

        $shippingFee = 0;
        $discount = 0;
        $total = $subtotal;
        $appliedVoucher = null;
        $voucherFeedbackError = null;
        $voucherCode = trim((string) $request->query('voucher_code', old('voucher_code', '')));

        if ($voucherCode !== '' && $orderItems->isNotEmpty()) {
            try {
                $quote = $voucherService->preview(
                    $voucherCode,
                    (int) $draft['shop_id'],
                    (int) Auth::id(),
                    [
                        'items' => $orderItems,
                        'subtotal' => $subtotal,
                    ],
                    $shippingFee
                );

                $voucherCode = $quote['voucher_data']['code'];
                $appliedVoucher = $quote['voucher_data'];
                $discount = (int) $quote['discount'];
                $total = (int) $quote['total'];
            } catch (ValidationException $exception) {
                $voucherFeedbackError = collect($exception->errors())->flatten()->first();
            }
        }

        return view('customer.checkout.index', compact(
            'shop',
            'orderItems',
            'subtotal',
            'shippingFee',
            'voucherCode',
            'appliedVoucher',
            'voucherFeedbackError',
            'discount',
            'total'
        ));
    }

    public function prepare(Request $request, VoucherService $voucherService): JsonResponse
    {
        $validated = $request->validate([
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $draft = $this->draft();
        $shopId = (int) $validated['shop_id'];

        if ($draft !== null && ! empty($draft['items']) && (int) $draft['shop_id'] !== $shopId) {
            return response()->json([
                'message' => 'Ban chi co the dat mon tu 1 shop trong mot don hang.',
            ], 422);
        }

        $summary = $voucherService->buildCartSummary($shopId, $validated['items']);

        $this->storeDraft(
            $shopId,
            collect($summary['items'])
                ->map(fn (array $item) => [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ])
                ->values()
                ->all()
        );

        return response()->json([
            'message' => 'Da chuyen sang trang thanh toan.',
            'redirect' => route('checkout.index'),
            'count' => $this->countDraftItems(),
        ]);
    }

    public function updateQuantity(Request $request, int $productId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $draft = $this->draft();

        if ($draft === null || empty($draft['items'])) {
            return redirect()->route('checkout.index')->with('error', 'Khong tim thay don tam.');
        }

        $found = false;
        $items = collect($draft['items'])
            ->map(function (array $item) use ($productId, $validated, &$found): array {
                if ((int) $item['product_id'] === $productId) {
                    $item['quantity'] = (int) $validated['quantity'];
                    $found = true;
                }

                return $item;
            })
            ->values()
            ->all();

        if (! $found) {
            return redirect()->route('checkout.index')->with('error', 'Khong tim thay mon trong don tam.');
        }

        $this->storeDraft((int) $draft['shop_id'], $items);

        return back()->with('success', 'Da cap nhat so luong.');
    }

    public function remove(int $productId)
    {
        $draft = $this->draft();

        if ($draft === null || empty($draft['items'])) {
            return redirect()->route('checkout.index')->with('error', 'Khong tim thay don tam.');
        }

        $items = collect($draft['items'])
            ->reject(fn (array $item) => (int) $item['product_id'] === $productId)
            ->values()
            ->all();

        if (empty($items)) {
            $this->clearDraft();
        } else {
            $this->storeDraft((int) $draft['shop_id'], $items);
        }

        return back()->with('success', 'Da xoa mon khoi don tam.');
    }

    public function processCheckout(Request $request, VoucherService $voucherService)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string',
            'note' => 'nullable|string',
            'payment_method' => 'nullable|in:cod,bank',
            'voucher_code' => 'nullable|string|max:30',
        ]);

        $draft = $this->draft();

        if ($draft === null || empty($draft['items'])) {
            return redirect()->route('checkout.index')->with('error', 'Don tam dang trong.');
        }

        $shippingFee = 0;
        $quote = null;

        try {
            $orderDraft = $voucherService->buildCartSummary((int) $draft['shop_id'], $draft['items']);

            if (! empty($validated['voucher_code'])) {
                $quote = $voucherService->preview(
                    $validated['voucher_code'],
                    (int) $draft['shop_id'],
                    (int) auth()->id(),
                    $orderDraft,
                    $shippingFee
                );
            }

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => (int) auth()->id(),
                'shop_id' => (int) $draft['shop_id'],
                'order_code' => 'FH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'subtotal' => (int) $orderDraft['subtotal'],
                'shipping_fee' => $shippingFee,
                'discount' => (int) ($quote['discount'] ?? 0),
                'total' => (int) ($quote['total'] ?? $orderDraft['subtotal']),
                'payment_method' => $validated['payment_method'] ?? 'cod',
                'status' => 'pending',
                'voucher_code' => $quote['voucher_data']['code'] ?? null,
            ]);

            \App\Models\OrderDelivery::create([
                'order_id' => $order->id,
                'customer_phone' => optional(auth()->user()->customer)->phone ?? auth()->user()->phone ?? '0123456789',
                'delivery_address' => $validated['delivery_address'],
                'note' => $validated['note'] ?? null,
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

            DB::commit();
            $this->clearDraft();

            return redirect()->route('checkout.index')->with('success', 'Dat don thanh cong!');
        } catch (ValidationException $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return redirect()
                ->route('checkout.index', ['voucher_code' => $validated['voucher_code'] ?? null])
                ->withErrors($exception->errors())
                ->withInput();
        } catch (\Exception $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return back()
                ->withInput()
                ->with('error', 'Loi: ' . $exception->getMessage());
        }
    }

    protected function draft(): ?array
    {
        $draft = session(self::SESSION_KEY);

        return is_array($draft) ? $draft : null;
    }

    protected function storeDraft(int $shopId, array $items): void
    {
        session([
            self::SESSION_KEY => [
                'shop_id' => $shopId,
                'items' => array_values($items),
            ],
        ]);
    }

    protected function clearDraft(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    protected function countDraftItems(): int
    {
        return (int) collect(session(self::SESSION_KEY . '.items', []))->sum('quantity');
    }
}
