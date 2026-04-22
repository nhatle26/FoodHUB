<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Voucher;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReviewVoucherSeeder extends Seeder
{
    public function run(): void
    {
        // ===================== REVIEWS =====================
        // Chỉ review những đơn đã delivered
        // order_id tương ứng với OrderSeeder (1,2,3,4,5,13,14,15,16,17,18)
        $reviews = [
            ['user_id' => 8,  'shop_id' => 1,  'order_id' => 1,  'rating' => 5, 'comment' => 'Trà sữa ngon tuyệt, trân châu dẻo không bị cứng. Shipper giao nhanh, đóng gói cẩn thận. Sẽ order lại!'],
            ['user_id' => 9,  'shop_id' => 3,  'order_id' => 2,  'rating' => 5, 'comment' => 'Cơm tấm ngon nhất Sài Gòn! Sườn nướng thơm đậm đà, nước mắm chua ngọt vừa miệng. Rất đáng tiền!'],
            ['user_id' => 10, 'shop_id' => 7,  'order_id' => 3,  'rating' => 5, 'comment' => 'Bánh mì ngon hết sảy. Ổ bánh giòn, nhân đầy. Sốt gia truyền đặc biệt, không nơi nào có. 5 sao full!'],
            ['user_id' => 11, 'shop_id' => 5,  'order_id' => 4,  'rating' => 4, 'comment' => 'Bánh tráng trộn ngon, đậu phộng rang thơm, xoài chua vừa. Khoai tây hơi nguội khi đến nơi, trừ 1 sao.'],
            ['user_id' => 12, 'shop_id' => 11, 'order_id' => 5,  'rating' => 5, 'comment' => 'Trà oolong thơm, sữa béo vừa. Topping đầy đặn. Yakult dứa ổi tươi mát siêu ngon. Quán top 1 trà sữa!'],
            ['user_id' => 20, 'shop_id' => 5,  'order_id' => 13, 'rating' => 5, 'comment' => 'Bánh tráng trộn siêu ngon! Phần nhiều topping, gia vị đậm đà. Giá rất hợp lý cho sinh viên.'],
            ['user_id' => 8,  'shop_id' => 11, 'order_id' => 14, 'rating' => 5, 'comment' => 'Oolong thơm đặc biệt, trân châu dẻo dai cực phẩm. Yakult chua ngọt thanh mát. Order lần thứ 5 rồi!'],
            ['user_id' => 9,  'shop_id' => 7,  'order_id' => 15, 'rating' => 4, 'comment' => 'Bánh mì ổ giòn tan. Trứng ốp la chín tới, xíu mại thơm ngon. Chỉ hơi mặn một chút với mình.'],
            ['user_id' => 10, 'shop_id' => 1,  'order_id' => 16, 'rating' => 4, 'comment' => 'Hồng trà thơm nhẹ, ít ngọt theo yêu cầu. Cà phê sữa đá cuộn đậm đà. Giao hàng đúng giờ.'],
            ['user_id' => 11, 'shop_id' => 3,  'order_id' => 17, 'rating' => 5, 'comment' => 'Order 3 suất cho cả nhóm, ai cũng khen ngon! Sườn mềm, bì giòn, chả trứng vừa. Tuyệt vời!'],
            ['user_id' => 12, 'shop_id' => 5,  'order_id' => 18, 'rating' => 4, 'comment' => 'Xúc xích nướng thơm giòn. Bắp xào bơ béo thơm. Phần hơi ít so với giá, nhưng chất lượng ổn.'],
            ['user_id' => 19, 'shop_id' => 3,  'order_id' => 2,  'rating' => 3, 'comment' => 'Cơm tấm ngon nhưng hôm nay shop hết sườn bì chả phải đổi sang sườn thường. Mong lần sau đủ hàng.'],
            ['user_id' => 13, 'shop_id' => 3,  'order_id' => 6,  'rating' => 5, 'comment' => '2 suất cơm đóng hộp cẩn thận, không bị đổ. Sườn nướng thơm từ xa. Giá hợp lý, sẽ quay lại.'],
            ['user_id' => 15, 'shop_id' => 7,  'order_id' => 8,  'rating' => 5, 'comment' => 'Bánh mì giao trước 7h đúng hẹn! Ổ bánh còn ấm, giòn tan. Nhân đầy đặn. Quán mình order mỗi sáng!'],
            ['user_id' => 18, 'shop_id' => 1,  'order_id' => 11, 'rating' => 2, 'comment' => 'Huỷ đơn do bận nhưng được shop xử lý nhanh, không tính phí. Lần sau sẽ order lại bù nhé!'],
            ['user_id' => 14, 'shop_id' => 1,  'order_id' => 7,  'rating' => 5, 'comment' => 'Trà sữa và trà đào đều ngon! Đóng gói 2 lớp bọc kín, đá không tan trên đường giao. Xuất sắc!'],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
        $this->command->info('✅ ReviewSeeder: Đã tạo '.count($reviews).' reviews');

        // ===================== VOUCHERS =====================
        $vouchers = [
            [
                'shop_id' => 1,
                'code' => 'HOADAO10',
                'description' => 'Giảm 10% cho đơn từ 100k',
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 100000,
                'max_discount' => 30000,
                'max_uses' => 100,
                'used_count' => 38,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
            ],
            [
                'shop_id' => 1,
                'code' => 'HOAMOI',
                'description' => 'Tặng 15k cho khách mới - Chỉ dùng 1 lần',
                'type' => 'fixed',
                'value' => 15000,
                'min_order_amount' => 50000,
                'max_discount' => null,
                'max_uses' => 200,
                'used_count' => 72,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
                'conditions' => [
                    'new_customer_only' => true,
                    'new_customer_scope' => 'shop',
                    'max_uses_per_user' => 1,
                ],
            ],
            [
                'shop_id' => 3,
                'code' => 'SALE15K',
                'description' => 'Giảm 15k cho đơn từ 80k',
                'type' => 'fixed',
                'value' => 15000,
                'min_order_amount' => 80000,
                'max_discount' => null,
                'max_uses' => 150,
                'used_count' => 45,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-06-30'),
            ],
            [
                'shop_id' => 3,
                'code' => 'COMTAM20',
                'description' => 'Giảm 20% cho đơn hàng thứ 3 trở đi',
                'type' => 'percent',
                'value' => 20,
                'min_order_amount' => 100000,
                'max_discount' => 50000,
                'max_uses' => 50,
                'used_count' => 12,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-09-30'),
                'conditions' => [
                    'min_completed_orders' => 2,
                    'completed_orders_scope' => 'shop',
                ],
            ],
            [
                'shop_id' => 5,
                'code' => 'SNACK30',
                'description' => 'Giảm 30% - Flash sale cuối tuần',
                'type' => 'percent',
                'value' => 30,
                'min_order_amount' => 60000,
                'max_discount' => 25000,
                'max_uses' => 30,
                'used_count' => 30,
                'is_active' => false, // hết lượt
                'expires_at' => Carbon::parse('2024-03-03'),
                'conditions' => [
                    'valid_weekdays' => [6, 0],
                ],
            ],
            [
                'shop_id' => 7,
                'code' => 'BANHMI5K',
                'description' => 'Giảm 5k bất kỳ đơn nào - Không điều kiện',
                'type' => 'fixed',
                'value' => 5000,
                'min_order_amount' => 0,
                'max_discount' => null,
                'max_uses' => 999,
                'used_count' => 210,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
            ],
            [
                'shop_id' => 11,
                'code' => 'LONGTEN20K',
                'description' => 'Giảm 20k cho đơn từ 120k',
                'type' => 'fixed',
                'value' => 20000,
                'min_order_amount' => 120000,
                'max_discount' => null,
                'max_uses' => 80,
                'used_count' => 33,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-08-31'),
            ],
            [
                'shop_id' => 11,
                'code' => 'LONGVIP',
                'description' => 'Giảm 15% cho VIP - Đơn từ 200k',
                'type' => 'percent',
                'value' => 15,
                'min_order_amount' => 200000,
                'max_discount' => 60000,
                'max_uses' => 20,
                'used_count' => 5,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
                'conditions' => [
                    'min_customer_total_spent' => 500000,
                    'customer_spend_scope' => 'shop',
                ],
            ],
            [
                'shop_id' => 9,
                'code' => 'PIZZA25',
                'description' => 'Mừng khai trương - Giảm 25k đơn Pizza',
                'type' => 'fixed',
                'value' => 25000,
                'min_order_amount' => 150000,
                'max_discount' => null,
                'max_uses' => 100,
                'used_count' => 18,
                'is_active' => true,
                'starts_at' => Carbon::parse('2024-01-01'),
                'expires_at' => Carbon::parse('2024-12-31'),
                'conditions' => [
                    'required_product_groups' => ['Pizza'],
                    'required_product_groups_match' => 'any',
                ],
            ],
            [
                'shop_id' => 4,
                'code' => 'CAFE10K',
                'description' => 'Giảm 10k - Uống cà phê buổi sáng',
                'type' => 'fixed',
                'value' => 10000,
                'min_order_amount' => 50000,
                'max_discount' => null,
                'max_uses' => 200,
                'used_count' => 67,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
            ],
            [
                'shop_id' => 6,
                'code' => 'CHE15',
                'description' => 'Giảm 15% chè các loại - Đơn từ 50k',
                'type' => 'percent',
                'value' => 15,
                'min_order_amount' => 50000,
                'max_discount' => 20000,
                'max_uses' => 60,
                'used_count' => 22,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-07-31'),
            ],
            [
                'shop_id' => 8,
                'code' => 'PHO20K',
                'description' => 'Giảm 20k cho đơn bún phở từ 100k',
                'type' => 'fixed',
                'value' => 20000,
                'min_order_amount' => 100000,
                'max_discount' => null,
                'max_uses' => 40,
                'used_count' => 14,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
            ],
            [
                'shop_id' => 12,
                'code' => 'GARAN10',
                'description' => 'Giảm 10% gà rán - Áp dụng thứ 6, 7, CN',
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 100000,
                'max_discount' => 35000,
                'max_uses' => 120,
                'used_count' => 41,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
                'conditions' => [
                    'valid_weekdays' => [5, 6, 0],
                ],
            ],
            [
                'shop_id' => 3,
                'code' => 'TETHOLIDAY',
                'description' => 'Voucher Tết - Giảm 50k đơn từ 200k',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_amount' => 200000,
                'max_discount' => null,
                'max_uses' => 50,
                'used_count' => 50,
                'is_active' => false, // hết hạn
                'starts_at' => Carbon::parse('2024-02-08'),
                'expires_at' => Carbon::parse('2024-02-14'),
            ],
            [
                'shop_id' => 1,
                'code' => 'SUMMER30',
                'description' => 'Sale hè - Giảm 30% đồ uống, tối đa 40k',
                'type' => 'percent',
                'value' => 30,
                'min_order_amount' => 80000,
                'max_discount' => 40000,
                'max_uses' => 200,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => Carbon::parse('2024-06-01'),
                'expires_at' => Carbon::parse('2024-08-31'),
            ],
            [
                'shop_id' => 5,
                'code' => 'STUDENT',
                'description' => 'Ưu đãi sinh viên - Giảm 10k với thẻ SV',
                'type' => 'fixed',
                'value' => 10000,
                'min_order_amount' => 40000,
                'max_discount' => null,
                'max_uses' => 500,
                'used_count' => 128,
                'is_active' => true,
                'expires_at' => Carbon::parse('2024-12-31'),
                'conditions' => [
                    'requires_student_verification' => true,
                ],
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
        $this->command->info('✅ VoucherSeeder: Đã tạo '.count($vouchers).' vouchers');

        // ===================== WISHLISTS =====================
        $wishlists = [
            ['user_id' => 8,  'shop_id' => 1],
            ['user_id' => 8,  'shop_id' => 3],
            ['user_id' => 8,  'shop_id' => 11],
            ['user_id' => 9,  'shop_id' => 3],
            ['user_id' => 9,  'shop_id' => 7],
            ['user_id' => 10, 'shop_id' => 7],
            ['user_id' => 10, 'shop_id' => 1],
            ['user_id' => 11, 'shop_id' => 5],
            ['user_id' => 11, 'shop_id' => 3],
            ['user_id' => 12, 'shop_id' => 11],
            ['user_id' => 12, 'shop_id' => 5],
            ['user_id' => 13, 'shop_id' => 3],
            ['user_id' => 14, 'shop_id' => 1],
            ['user_id' => 15, 'shop_id' => 7],
            ['user_id' => 16, 'shop_id' => 5],
            ['user_id' => 17, 'shop_id' => 11],
            ['user_id' => 18, 'shop_id' => 1],
            ['user_id' => 19, 'shop_id' => 7],
            ['user_id' => 20, 'shop_id' => 5],
            ['user_id' => 20, 'shop_id' => 11],
        ];

        foreach ($wishlists as $w) {
            Wishlist::create($w);
        }
        $this->command->info('✅ WishlistSeeder: Đã tạo '.count($wishlists).' wishlists');
    }
}
