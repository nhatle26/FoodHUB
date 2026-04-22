<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    public function buildCartSummary(int $shopId, array $items): array
    {
        $normalizedItems = collect($items)
            ->map(function (array $item): array {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->filter(fn (array $item) => $item['quantity'] > 0)
            ->values();

        if ($normalizedItems->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Giỏ hàng phải có ít nhất 1 sản phẩm hợp lệ.',
            ]);
        }

        $products = Product::query()
            ->where('shop_id', $shopId)
            ->where('is_available', true)
            ->whereIn('id', $normalizedItems->pluck('product_id'))
            ->get()
            ->keyBy('id');

        if ($products->count() !== $normalizedItems->count()) {
            throw ValidationException::withMessages([
                'items' => 'Có sản phẩm không thuộc shop hoặc hiện không khả dụng.',
            ]);
        }

        $lineItems = $normalizedItems->map(function (array $item) use ($products): array {
            /** @var Product $product */
            $product = $products->get($item['product_id']);
            $lineSubtotal = $product->price * $item['quantity'];

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'product_price' => (int) $product->price,
                'product_group' => $product->product_group,
                'quantity' => $item['quantity'],
                'subtotal' => $lineSubtotal,
            ];
        });

        return [
            'items' => $lineItems,
            'subtotal' => (int) $lineItems->sum('subtotal'),
        ];
    }

    public function preview(
        string $voucherCode,
        int $shopId,
        int $userId,
        array $cart,
        int $shippingFee = 15000,
        array $context = []
    ): array {
        $voucher = Voucher::query()
            ->where('shop_id', $shopId)
            ->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($voucherCode))])
            ->first();

        if (! $voucher) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Mã voucher không tồn tại cho shop này.',
            ]);
        }

        $placedAt = isset($context['placed_at'])
            ? Carbon::parse($context['placed_at'])
            : now();

        $this->assertVoucherIsApplicable(
            $voucher,
            $userId,
            $cart['items'],
            (int) $cart['subtotal'],
            $placedAt,
            $context
        );

        $discount = $this->calculateDiscount($voucher, (int) $cart['subtotal']);
        $total = max(0, (int) $cart['subtotal'] + $shippingFee - $discount);

        return [
            'voucher' => $voucher,
            'voucher_data' => $this->serializeVoucher($voucher),
            'subtotal' => (int) $cart['subtotal'],
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    public function serializeVoucher(Voucher $voucher): array
    {
        return [
            'id' => $voucher->id,
            'shop_id' => $voucher->shop_id,
            'code' => $voucher->code,
            'description' => $voucher->description,
            'type' => $voucher->type,
            'value' => (int) $voucher->value,
            'min_order_amount' => (int) $voucher->min_order_amount,
            'max_discount' => $voucher->max_discount ? (int) $voucher->max_discount : null,
            'max_uses' => (int) $voucher->max_uses,
            'used_count' => (int) $voucher->used_count,
            'is_active' => (bool) $voucher->is_active,
            'starts_at' => optional($voucher->starts_at)->toIso8601String(),
            'expires_at' => optional($voucher->expires_at)->toIso8601String(),
            'conditions' => $voucher->conditions ?? [],
            'condition_labels' => $this->conditionLabels($voucher),
        ];
    }

    public function conditionLabels(Voucher $voucher): array
    {
        $labels = [];
        $conditions = $voucher->conditions ?? [];

        if ($voucher->min_order_amount > 0) {
            $labels[] = 'Đơn tối thiểu '.number_format((int) $voucher->min_order_amount, 0, ',', '.').'đ';
        }

        if ($voucher->type === 'percent') {
            $labels[] = 'Giảm '.$voucher->value.'%';
        } else {
            $labels[] = 'Giảm '.number_format((int) $voucher->value, 0, ',', '.').'đ';
        }

        if ($voucher->max_discount) {
            $labels[] = 'Giảm tối đa '.number_format((int) $voucher->max_discount, 0, ',', '.').'đ';
        }

        if (! empty($conditions['new_customer_only'])) {
            $scope = $this->scopeLabel($conditions['new_customer_scope'] ?? 'shop');
            $labels[] = 'Chỉ áp dụng cho khách mới ở phạm vi '.$scope;
        }

        if (! empty($conditions['max_uses_per_user'])) {
            $labels[] = 'Mỗi khách dùng tối đa '.(int) $conditions['max_uses_per_user'].' lần';
        }

        if (! empty($conditions['min_completed_orders'])) {
            $scope = $this->scopeLabel($conditions['completed_orders_scope'] ?? 'shop');
            $labels[] = 'Cần tối thiểu '.(int) $conditions['min_completed_orders'].' đơn hoàn tất ở phạm vi '.$scope;
        }

        if (! empty($conditions['min_customer_total_spent'])) {
            $scope = $this->scopeLabel($conditions['customer_spend_scope'] ?? 'shop');
            $labels[] = 'Tổng chi tiêu tối thiểu '.number_format((int) $conditions['min_customer_total_spent'], 0, ',', '.').'đ ở phạm vi '.$scope;
        }

        if (! empty($conditions['valid_weekdays'])) {
            $labels[] = 'Chỉ áp dụng vào '.implode(', ', $this->mapWeekdayLabels($conditions['valid_weekdays']));
        }

        if (! empty($conditions['required_product_groups'])) {
            $labels[] = 'Giỏ hàng cần chứa nhóm món: '.implode(', ', $conditions['required_product_groups']);
        }

        if (! empty($conditions['required_product_keywords'])) {
            $labels[] = 'Giỏ hàng cần chứa sản phẩm có từ khóa: '.implode(', ', $conditions['required_product_keywords']);
        }

        if (! empty($conditions['requires_student_verification'])) {
            $labels[] = 'Cần xác nhận sinh viên';
        }

        return $labels;
    }

    protected function assertVoucherIsApplicable(
        Voucher $voucher,
        int $userId,
        Collection $items,
        int $subtotal,
        Carbon $placedAt,
        array $context
    ): void {
        if (! $voucher->is_active) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher hiện đang bị khóa hoặc đã ngừng áp dụng.',
            ]);
        }

        if ($voucher->starts_at && $placedAt->lt($voucher->starts_at)) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher chưa tới thời gian áp dụng.',
            ]);
        }

        if ($voucher->expires_at && $placedAt->gt($voucher->expires_at)) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher đã hết hạn.',
            ]);
        }

        if ($voucher->used_count >= $voucher->max_uses) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher đã hết lượt sử dụng.',
            ]);
        }

        if ($subtotal < $voucher->min_order_amount) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Đơn hàng chưa đạt giá trị tối thiểu để dùng voucher này.',
            ]);
        }

        $this->assertCustomConditions($voucher, $userId, $items, $placedAt, $context);
    }

    protected function assertCustomConditions(
        Voucher $voucher,
        int $userId,
        Collection $items,
        Carbon $placedAt,
        array $context
    ): void {
        $conditions = $voucher->conditions ?? [];

        if (! empty($conditions['new_customer_only'])) {
            $scope = $conditions['new_customer_scope'] ?? 'shop';
            $ordersQuery = Order::query()
                ->where('user_id', $userId)
                ->where('status', '!=', 'cancelled');

            if ($scope === 'shop') {
                $ordersQuery->where('shop_id', $voucher->shop_id);
            }

            if ($ordersQuery->exists()) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Voucher này chỉ áp dụng cho khách mới.',
                ]);
            }
        }

        if (! empty($conditions['max_uses_per_user'])) {
            $usedByUser = Order::query()
                ->where('user_id', $userId)
                ->where('voucher_code', $voucher->code)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($usedByUser >= (int) $conditions['max_uses_per_user']) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Bạn đã dùng hết số lượt cho voucher này.',
                ]);
            }
        }

        if (! empty($conditions['min_completed_orders'])) {
            $scope = $conditions['completed_orders_scope'] ?? 'shop';
            $completedOrders = Order::query()
                ->where('user_id', $userId)
                ->where('status', 'delivered');

            if ($scope === 'shop') {
                $completedOrders->where('shop_id', $voucher->shop_id);
            }

            if ($completedOrders->count() < (int) $conditions['min_completed_orders']) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Bạn chưa đủ số đơn hoàn tất để dùng voucher này.',
                ]);
            }
        }

        if (! empty($conditions['min_customer_total_spent'])) {
            $scope = $conditions['customer_spend_scope'] ?? 'shop';
            $spentOrders = Order::query()
                ->where('user_id', $userId)
                ->where('status', 'delivered');

            if ($scope === 'shop') {
                $spentOrders->where('shop_id', $voucher->shop_id);
            }

            $spent = (int) $spentOrders->sum('total');

            if ($spent < (int) $conditions['min_customer_total_spent']) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Tổng chi tiêu của bạn chưa đủ để dùng voucher này.',
                ]);
            }
        }

        if (! empty($conditions['valid_weekdays'])) {
            $allowedDays = array_map('intval', $conditions['valid_weekdays']);

            if (! in_array($placedAt->dayOfWeek, $allowedDays, true)) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Voucher chỉ áp dụng vào một số ngày trong tuần.',
                ]);
            }
        }

        if (! empty($conditions['required_product_groups'])) {
            $this->assertRequiredProductGroups($conditions, $items);
        }

        if (! empty($conditions['required_product_keywords'])) {
            $this->assertRequiredProductKeywords($conditions, $items);
        }

        if (! empty($conditions['requires_student_verification']) && empty($context['is_student_verified'])) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher này yêu cầu xác minh sinh viên.',
            ]);
        }
    }

    protected function assertRequiredProductGroups(array $conditions, Collection $items): void
    {
        $requiredGroups = collect($conditions['required_product_groups'])
            ->filter()
            ->map(fn (string $group) => mb_strtolower($group));

        $cartGroups = $items
            ->pluck('product_group')
            ->filter()
            ->map(fn (?string $group) => mb_strtolower((string) $group));

        $matchMode = $conditions['required_product_groups_match'] ?? 'any';
        $matchedCount = $requiredGroups->intersect($cartGroups)->count();
        $isValid = $matchMode === 'all'
            ? $matchedCount === $requiredGroups->count()
            : $matchedCount > 0;

        if (! $isValid) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher chỉ áp dụng cho một số nhóm món nhất định.',
            ]);
        }
    }

    protected function assertRequiredProductKeywords(array $conditions, Collection $items): void
    {
        $keywords = collect($conditions['required_product_keywords'])
            ->filter()
            ->map(fn (string $keyword) => mb_strtolower($keyword))
            ->values();

        $productNames = $items
            ->pluck('product_name')
            ->map(fn (string $name) => mb_strtolower($name));

        $matchMode = $conditions['required_product_keywords_match'] ?? 'any';
        $matched = $keywords->filter(function (string $keyword) use ($productNames) {
            return $productNames->contains(fn (string $name) => str_contains($name, $keyword));
        });

        $isValid = $matchMode === 'all'
            ? $matched->count() === $keywords->count()
            : $matched->isNotEmpty();

        if (! $isValid) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher chỉ áp dụng cho một số món nhất định.',
            ]);
        }
    }

    protected function calculateDiscount(Voucher $voucher, int $subtotal): int
    {
        $discount = $voucher->type === 'percent'
            ? (int) floor($subtotal * ((int) $voucher->value) / 100)
            : (int) $voucher->value;

        if ($voucher->max_discount !== null) {
            $discount = min($discount, (int) $voucher->max_discount);
        }

        return min($discount, $subtotal);
    }

    protected function scopeLabel(string $scope): string
    {
        return $scope === 'global' ? 'toàn hệ thống' : 'shop hiện tại';
    }

    protected function mapWeekdayLabels(array $weekdays): array
    {
        $labels = [
            0 => 'Chủ nhật',
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
        ];

        return collect($weekdays)
            ->map(fn ($day) => $labels[(int) $day] ?? 'Không xác định')
            ->values()
            ->all();
    }
}
