<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu seeding FoodHub database...');
        $this->command->info('');

        // Thứ tự quan trọng: phải theo đúng FK dependency
        $this->call([
            UserSeeder::class,          // 1. Users (không có FK)
            CategorySeeder::class,      // 2. Categories (không có FK)
            ShopSeeder::class,          // 3. Shops (FK: users, categories)
            ProductSeeder::class,       // 4. Products (FK: shops)
            OrderSeeder::class,         // 5. Orders + OrderItems (FK: users, shops, products)
            ReviewVoucherSeeder::class, // 6. Reviews + Vouchers + Wishlists (FK: users, shops, orders)
        ]);

        $this->command->info('');
        $this->command->info(' Seeding hoàn tất! Tổng kết:');
        $this->command->info('   - Users:       20 records (1 admin, 6 shop, 13 customer)');
        $this->command->info('   - Categories:  18 records');
        $this->command->info('   - Shops:       15 records (12 active, 2 pending, 1 banned)');
        $this->command->info('   - Products:    20 records');
        $this->command->info('   - Orders:      18 records (kèm order_items)');
        $this->command->info('   - Reviews:     16 records');
        $this->command->info('   - Vouchers:    16 records');
        $this->command->info('   - Wishlists:   20 records');
        $this->command->info('');
        $this->command->info('Tài khoản test:');
        $this->command->info('   Admin:    admin@foodhub.vn / password');
        $this->command->info('   Shop:     hoa.tran@gmail.com / password');
        $this->command->info('   Customer: an.nguyen@gmail.com / password');
    }
}
