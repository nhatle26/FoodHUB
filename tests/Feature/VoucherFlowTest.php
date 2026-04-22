<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected Shop $shop;

    protected User $owner;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Do an',
            'slug' => 'do-an',
            'is_active' => true,
        ]);

        $this->owner = User::factory()->create([
            'role' => 'shop',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->shop = Shop::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'name' => 'Voucher Test Shop',
            'slug' => 'voucher-test-shop',
            'phone' => '0900000000',
            'address' => '1 Test Street',
            'status' => 'active',
        ]);
    }

    public function test_preview_returns_discount_for_eligible_voucher(): void
    {
        $product = $this->createProduct([
            'name' => 'Pizza hai lop',
            'price' => 100000,
            'product_group' => 'Pizza',
        ]);

        $voucher = $this->createVoucher([
            'code' => 'PIZZA25',
            'type' => 'fixed',
            'value' => 25000,
            'min_order_amount' => 150000,
            'conditions' => [
                'required_product_groups' => ['Pizza'],
                'required_product_groups_match' => 'any',
            ],
        ]);

        $response = $this->postJson('/api/vouchers/preview', [
            'shop_id' => $this->shop->id,
            'user_id' => $this->customer->id,
            'voucher_code' => $voucher->code,
            'shipping_fee' => 15000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('discount', 25000)
            ->assertJsonPath('total', 190000)
            ->assertJsonPath('voucher_data.code', 'PIZZA25');
    }

    public function test_preview_rejects_new_customer_only_voucher_for_returning_customer(): void
    {
        $product = $this->createProduct([
            'price' => 60000,
        ]);

        $voucher = $this->createVoucher([
            'code' => 'HOAMOI',
            'type' => 'fixed',
            'value' => 15000,
            'min_order_amount' => 50000,
            'conditions' => [
                'new_customer_only' => true,
                'new_customer_scope' => 'shop',
                'max_uses_per_user' => 1,
            ],
        ]);

        Order::create([
            'order_code' => 'FH-240101-AAAAAA',
            'user_id' => $this->customer->id,
            'shop_id' => $this->shop->id,
            'subtotal' => 60000,
            'shipping_fee' => 15000,
            'discount' => 0,
            'total' => 75000,
            'delivery_address' => 'Old address',
            'customer_phone' => '0911111111',
            'payment_method' => 'cod',
            'status' => 'delivered',
        ]);

        $response = $this->postJson('/api/vouchers/preview', [
            'shop_id' => $this->shop->id,
            'user_id' => $this->customer->id,
            'voucher_code' => $voucher->code,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('errors.voucher_code.0', 'Voucher này chỉ áp dụng cho khách mới.');
    }

    public function test_preview_requires_student_verification_when_voucher_has_student_condition(): void
    {
        $product = $this->createProduct([
            'price' => 50000,
        ]);

        $voucher = $this->createVoucher([
            'code' => 'STUDENT',
            'type' => 'fixed',
            'value' => 10000,
            'min_order_amount' => 40000,
            'conditions' => [
                'requires_student_verification' => true,
            ],
        ]);

        $response = $this->postJson('/api/vouchers/preview', [
            'shop_id' => $this->shop->id,
            'user_id' => $this->customer->id,
            'voucher_code' => $voucher->code,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('errors.voucher_code.0', 'Voucher này yêu cầu xác minh sinh viên.');
    }

    public function test_store_order_applies_voucher_and_increments_usage(): void
    {
        $product = $this->createProduct([
            'price' => 60000,
        ]);

        $voucher = $this->createVoucher([
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'min_order_amount' => 100000,
            'max_discount' => 15000,
        ]);

        $response = $this->postJson('/api/orders', [
            'user_id' => $this->customer->id,
            'shop_id' => $this->shop->id,
            'delivery_address' => '123 Test Street',
            'customer_phone' => '0912345678',
            'payment_method' => 'cod',
            'shipping_fee' => 15000,
            'voucher_code' => $voucher->code,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.order.discount', 12000)
            ->assertJsonPath('data.order.total', 123000)
            ->assertJsonPath('data.voucher.code', 'SAVE10');

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'user_id' => $this->customer->id,
            'discount' => 12000,
            'total' => 123000,
            'voucher_code' => 'SAVE10',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_group' => 'Combo',
            'quantity' => 2,
            'subtotal' => 120000,
        ]);

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'used_count' => 1,
        ]);
    }

    protected function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'shop_id' => $this->shop->id,
            'name' => 'Mon test',
            'description' => 'Mo ta',
            'price' => 50000,
            'product_group' => 'Combo',
            'is_available' => true,
        ], $overrides));
    }

    protected function createVoucher(array $overrides = []): Voucher
    {
        return Voucher::create(array_merge([
            'shop_id' => $this->shop->id,
            'code' => 'TESTCODE',
            'description' => 'Voucher test',
            'type' => 'fixed',
            'value' => 10000,
            'min_order_amount' => 0,
            'max_discount' => null,
            'max_uses' => 10,
            'used_count' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
            'conditions' => null,
        ], $overrides));
    }
}
