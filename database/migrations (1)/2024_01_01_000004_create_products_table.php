<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 0);
            $table->string('image')->nullable();
            $table->string('product_group', 100)->nullable(); // nhóm: Khai vị, Món chính...
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('total_sold')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
