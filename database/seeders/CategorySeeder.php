<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Trà sữa',       'icon' => 'icons/trasua.png',    'sort_order' => 1,  'is_active' => true],
            ['name' => 'Đồ ăn vặt',     'icon' => 'icons/doanvan.png',   'sort_order' => 2,  'is_active' => true],
            ['name' => 'Fast food',      'icon' => 'icons/fastfood.png',  'sort_order' => 3,  'is_active' => true],
            ['name' => 'Cơm văn phòng', 'icon' => 'icons/com.png',       'sort_order' => 4,  'is_active' => true],
            ['name' => 'Bánh mì',        'icon' => 'icons/banhmi.png',    'sort_order' => 5,  'is_active' => true],
            ['name' => 'Tráng miệng',    'icon' => 'icons/trangmieng.png','sort_order' => 6,  'is_active' => true],
            ['name' => 'Bún phở',        'icon' => 'icons/bunpho.png',    'sort_order' => 7,  'is_active' => true],
            ['name' => 'Pizza',          'icon' => 'icons/pizza.png',     'sort_order' => 8,  'is_active' => true],
            ['name' => 'Cà phê',         'icon' => 'icons/cafe.png',      'sort_order' => 9,  'is_active' => true],
            ['name' => 'Hải sản',        'icon' => 'icons/haisan.png',    'sort_order' => 10, 'is_active' => true],
            ['name' => 'Cơm tấm',        'icon' => 'icons/comtam.png',    'sort_order' => 11, 'is_active' => true],
            ['name' => 'Chay',           'icon' => 'icons/chay.png',      'sort_order' => 12, 'is_active' => true],
            ['name' => 'Nước ép',        'icon' => 'icons/nuocep.png',    'sort_order' => 13, 'is_active' => true],
            ['name' => 'Mì',             'icon' => 'icons/mi.png',        'sort_order' => 14, 'is_active' => true],
            ['name' => 'Bánh ngọt',      'icon' => 'icons/banhngot.png',  'sort_order' => 15, 'is_active' => true],
            ['name' => 'Nướng',          'icon' => 'icons/nuong.png',     'sort_order' => 16, 'is_active' => true],
            ['name' => 'Gà rán',         'icon' => 'icons/garan.png',     'sort_order' => 17, 'is_active' => true],
            ['name' => 'Salad',          'icon' => 'icons/salad.png',     'sort_order' => 18, 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'       => $cat['name'],
                'slug'       => Str::slug($cat['name']),
                'icon'       => $cat['icon'],
                'sort_order' => $cat['sort_order'],
                'is_active'  => $cat['is_active'],
            ]);
        }

        $this->command->info('CategorySeeder: Đã tạo ' . count($categories) . ' categories');
    }
}
