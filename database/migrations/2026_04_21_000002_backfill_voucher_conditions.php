<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $conditionsByCode = [
            'HOAMOI' => [
                'new_customer_only' => true,
                'new_customer_scope' => 'shop',
                'max_uses_per_user' => 1,
            ],
            'COMTAM20' => [
                'min_completed_orders' => 2,
                'completed_orders_scope' => 'shop',
            ],
            'SNACK30' => [
                'valid_weekdays' => [6, 0],
            ],
            'LONGVIP' => [
                'min_customer_total_spent' => 500000,
                'customer_spend_scope' => 'shop',
            ],
            'PIZZA25' => [
                'required_product_groups' => ['Pizza'],
                'required_product_groups_match' => 'any',
            ],
            'GARAN10' => [
                'valid_weekdays' => [5, 6, 0],
            ],
            'STUDENT' => [
                'requires_student_verification' => true,
            ],
        ];

        foreach ($conditionsByCode as $code => $conditions) {
            DB::table('vouchers')
                ->where('code', $code)
                ->update([
                    'conditions' => json_encode($conditions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('vouchers')
            ->whereIn('code', [
                'HOAMOI',
                'COMTAM20',
                'SNACK30',
                'LONGVIP',
                'PIZZA25',
                'GARAN10',
                'STUDENT',
            ])
            ->update([
                'conditions' => null,
            ]);
    }
};
