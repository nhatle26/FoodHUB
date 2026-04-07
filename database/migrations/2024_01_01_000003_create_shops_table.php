<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->string('phone', 15);
            $table->text('address');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('logo')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->enum('status', ['pending', 'active', 'banned'])->default('pending');
            $table->text('reject_reason')->nullable();
            $table->decimal('rating_avg', 2, 1)->default(0.0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
