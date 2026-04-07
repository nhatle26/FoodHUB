<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique(); // VD: FH-20240115-001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->decimal('subtotal', 12, 0);       // tổng tiền món
            $table->decimal('shipping_fee', 10, 0)->default(15000);
            $table->decimal('discount', 10, 0)->default(0);
            $table->decimal('total', 12, 0);           // subtotal + shipping - discount
            $table->text('delivery_address');
            $table->string('customer_phone', 15)->nullable();
            $table->text('note')->nullable();
            $table->string('voucher_code', 50)->nullable();
            $table->enum('payment_method', ['cod', 'bank'])->default('cod');
            $table->enum('status', [
                'pending',      // chờ shop xác nhận
                'confirmed',    // shop đã xác nhận
                'preparing',    // đang chuẩn bị
                'delivering',   // đang giao
                'delivered',    // đã giao - hoàn thành
                'cancelled'     // đã huỷ
            ])->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('product_name', 150); // snapshot tên lúc đặt
            $table->decimal('product_price', 10, 0); // snapshot giá lúc đặt
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 0); // price * quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
