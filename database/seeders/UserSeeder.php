<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // ----- ADMIN -----
            [
                'name'      => 'Super Admin',
                'email'     => 'admin@foodhub.vn',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'phone'     => '0900000001',
                'address'   => '1 Nguyễn Huệ, Quận 1, TP.HCM',
                'is_active' => true,
            ],

            // ----- SHOP OWNERS (6 người) -----
            [
                'name'      => 'Trần Thị Hoa',
                'email'     => 'hoa.tran@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0901111111',
                'address'   => '12 Lê Lợi, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Nguyễn Minh Tuấn',
                'email'     => 'tuan.nguyen@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0902222222',
                'address'   => '45 Nguyễn Trãi, Quận 5, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Lê Thị Mai',
                'email'     => 'mai.le@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0903333333',
                'address'   => '78 Trần Hưng Đạo, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Phạm Quốc Bảo',
                'email'     => 'bao.pham@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0904444444',
                'address'   => '23 Hai Bà Trưng, Quận 3, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Võ Thị Thanh',
                'email'     => 'thanh.vo@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0905555555',
                'address'   => '99 Cách Mạng Tháng 8, Quận 10, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Đặng Văn Long',
                'email'     => 'long.dang@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'shop',
                'phone'     => '0906666666',
                'address'   => '56 Đinh Tiên Hoàng, Quận Bình Thạnh, TP.HCM',
                'is_active' => true,
            ],

            // ----- CUSTOMERS (13 người) -----
            [
                'name'      => 'Nguyễn Văn An',
                'email'     => 'an.nguyen@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0911111111',
                'address'   => '34 Lý Tự Trọng, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Trần Thị Bình',
                'email'     => 'binh.tran@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0912222222',
                'address'   => '88 Nguyễn Đình Chiểu, Quận 3, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Lê Hoàng Khải',
                'email'     => 'khai.le@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0913333333',
                'address'   => '15 Pasteur, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Phạm Thị Lan',
                'email'     => 'lan.pham@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0914444444',
                'address'   => '67 Lê Văn Sỹ, Quận Phú Nhuận, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Vũ Đình Hùng',
                'email'     => 'hung.vu@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0915555555',
                'address'   => '200 Phan Văn Trị, Quận Bình Thạnh, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Hoàng Thị Ngọc',
                'email'     => 'ngoc.hoang@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0916666666',
                'address'   => '42 Trường Chinh, Quận Tân Bình, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Ngô Thành Đạt',
                'email'     => 'dat.ngo@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0917777777',
                'address'   => '11 Nguyễn Thái Học, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Bùi Thị Hương',
                'email'     => 'huong.bui@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0918888888',
                'address'   => '300 Lê Hồng Phong, Quận 10, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Đinh Quang Vinh',
                'email'     => 'vinh.dinh@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0919999999',
                'address'   => '7 Nguyễn Cư Trinh, Quận 1, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Đỗ Thị Kim Anh',
                'email'     => 'kimanh.do@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0920000000',
                'address'   => '55 Bà Huyện Thanh Quan, Quận 3, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Trương Văn Minh',
                'email'     => 'minh.truong@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0921111111',
                'address'   => '120 Nguyễn Thị Minh Khai, Quận 3, TP.HCM',
                'is_active' => true,
            ],
            [
                'name'      => 'Phan Thị Thu',
                'email'     => 'thu.phan@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'phone'     => '0922222222',
                'address'   => '88 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ UserSeeder: Đã tạo ' . count($users) . ' users');
    }
}
