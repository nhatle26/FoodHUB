<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Bỏ unique constraint trên (email, role)
            $table->dropUnique(['email', 'role']);
            
            // Thêm lại unique constraint chỉ trên email
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Bỏ unique constraint trên email
            $table->dropUnique(['email']);
            
            // Thêm lại unique constraint trên (email, role)
            $table->unique(['email', 'role']);
        });
    }
};
