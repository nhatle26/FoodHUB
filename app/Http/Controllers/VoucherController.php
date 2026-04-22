<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    public function __construct(
        protected VoucherService $voucherService
    ) {}

    public function index(Request $request, Shop $shop): JsonResponse
    {
        $includeInactive = $request->boolean('include_inactive');

        $vouchers = Voucher::query()
            ->where('shop_id', $shop->id)
            ->when(! $includeInactive, fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_active')
            ->orderBy('expires_at')
            ->get()
            ->map(fn (Voucher $voucher) => $this->voucherService->serializeVoucher($voucher))
            ->values();

        return response()->json([
            'shop' => [
                'id' => $shop->id,
                'name' => $shop->name,
            ],
            'data' => $vouchers,
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'voucher_code' => ['required', 'string', 'max:30'],
            'shipping_fee' => ['nullable', 'integer', 'min:0'],
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
                'user_id' => 'Can dang nhap de xem truoc voucher.',
            ]);
        }

        $orderDraft = $this->voucherService->buildCartSummary(
            (int) $validated['shop_id'],
            $validated['items']
        );

        $quote = $this->voucherService->preview(
            $validated['voucher_code'],
            (int) $validated['shop_id'],
            $userId,
            $orderDraft,
            (int) ($validated['shipping_fee'] ?? 15000),
            $validated['context'] ?? []
        );

        return response()->json($quote);
    }
}
