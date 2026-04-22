<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('order_items', 'product_group')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('product_group', 100)->nullable();
            });
        }

        $this->backfillProductGroups();
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'product_group')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('product_group');
            });
        }
    }

    protected function backfillProductGroups(): void
    {
        DB::table('order_items')
            ->select('id', 'product_id')
            ->whereNull('product_group')
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                $productGroups = DB::table('products')
                    ->whereIn('id', $items->pluck('product_id')->unique()->all())
                    ->pluck('product_group', 'id');

                foreach ($items as $item) {
                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update([
                            'product_group' => $productGroups[$item->product_id] ?? null,
                        ]);
                }
            });
    }
};
