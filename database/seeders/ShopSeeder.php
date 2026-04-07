<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // user_id 2-7 là shop owners (từ UserSeeder)
        // category_id 1-18 (từ CategorySeeder)
        $shops = [
            [
                'user_id'     => 2,
                'category_id' => 1, // Trà sữa
                'name'        => 'Trà Sữa Hoa Đào',
                'phone'       => '0901111111',
                'address'     => '12 Lê Lợi, Quận 1, TP.HCM',
                'description' => 'Trà sữa tươi ngon, pha chế mỗi ngày. Hơn 50 loại topping đặc biệt. Cam kết 100% nguyên liệu sạch, không phẩm màu.',
                'open_time'   => '07:00:00',
                'close_time'  => '22:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.8,
                'total_orders'=> 320,
                'total_reviews'=> 45,
            ],
            [
                'user_id'     => 2,
                'category_id' => 3, // Fast food
                'name'        => 'Burger Hoa - Fast Food',
                'phone'       => '0901111112',
                'address'     => '34 Nguyễn Huệ, Quận 1, TP.HCM',
                'description' => 'Burger thủ công từ thịt bò tươi, không dùng thịt đông lạnh. Khoai tây chiên giòn, sauce đặc biệt.',
                'open_time'   => '09:00:00',
                'close_time'  => '21:30:00',
                'status'      => 'active',
                'rating_avg'  => 4.5,
                'total_orders'=> 180,
                'total_reviews'=> 32,
            ],
            [
                'user_id'     => 3,
                'category_id' => 4, // Cơm văn phòng
                'name'        => 'Cơm Tấm Sài Gòn Tuấn',
                'phone'       => '0902222222',
                'address'     => '45 Nguyễn Trãi, Quận 5, TP.HCM',
                'description' => 'Cơm tấm sườn nướng đặc biệt, bì chả thơm ngon. Mở cửa từ sáng sớm, phục vụ cả ngày. Bữa ăn ngon mỗi ngày.',
                'open_time'   => '06:00:00',
                'close_time'  => '21:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.9,
                'total_orders'=> 560,
                'total_reviews'=> 88,
            ],
            [
                'user_id'     => 3,
                'category_id' => 9, // Cà phê
                'name'        => 'Cà Phê Tuấn House',
                'phone'       => '0902222223',
                'address'     => '78 Lê Thánh Tôn, Quận 1, TP.HCM',
                'description' => 'Cà phê rang xay tại chỗ. Không gian yên tĩnh, wifi miễn phí. Phù hợp làm việc và học bài.',
                'open_time'   => '06:30:00',
                'close_time'  => '22:30:00',
                'status'      => 'active',
                'rating_avg'  => 4.6,
                'total_orders'=> 240,
                'total_reviews'=> 41,
            ],
            [
                'user_id'     => 4,
                'category_id' => 2, // Đồ ăn vặt
                'name'        => 'Ăn Vặt Mai Béo',
                'phone'       => '0903333333',
                'address'     => '78 Trần Hưng Đạo, Quận 1, TP.HCM',
                'description' => 'Đủ các loại đồ ăn vặt: bánh tráng trộn, bắp xào, trứng cút, chả cá, xiên que... Siêu ngon siêu rẻ!',
                'open_time'   => '14:00:00',
                'close_time'  => '23:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.7,
                'total_orders'=> 410,
                'total_reviews'=> 67,
            ],
            [
                'user_id'     => 4,
                'category_id' => 6, // Tráng miệng
                'name'        => 'Chè Mai - Tráng Miệng',
                'phone'       => '0903333334',
                'address'     => '90 Đinh Tiên Hoàng, Quận 1, TP.HCM',
                'description' => 'Chè 3 miền, kem trái cây, pudding matcha. Tất cả làm thủ công từ nguyên liệu tự nhiên mỗi ngày.',
                'open_time'   => '10:00:00',
                'close_time'  => '22:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.8,
                'total_orders'=> 290,
                'total_reviews'=> 52,
            ],
            [
                'user_id'     => 5,
                'category_id' => 5, // Bánh mì
                'name'        => 'Bánh Mì Phượng Bảo',
                'phone'       => '0904444444',
                'address'     => '23 Hai Bà Trưng, Quận 3, TP.HCM',
                'description' => 'Bánh mì ổ giòn rụm, nhân đa dạng: thịt nguội, patê, trứng ốp, xíu mại. Sốt đặc biệt gia truyền 20 năm.',
                'open_time'   => '06:00:00',
                'close_time'  => '11:00:00',
                'status'      => 'active',
                'rating_avg'  => 5.0,
                'total_orders'=> 750,
                'total_reviews'=> 120,
            ],
            [
                'user_id'     => 5,
                'category_id' => 7, // Bún phở
                'name'        => 'Phở Bắc Bảo Ngon',
                'phone'       => '0904444445',
                'address'     => '56 Cống Quỳnh, Quận 1, TP.HCM',
                'description' => 'Phở bò tái chín nước dùng hầm xương 12 tiếng. Bún bò Huế cay đậm đà. Mì Quảng thơm ngon đặc trưng.',
                'open_time'   => '06:00:00',
                'close_time'  => '14:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.7,
                'total_orders'=> 380,
                'total_reviews'=> 61,
            ],
            [
                'user_id'     => 6,
                'category_id' => 8, // Pizza
                'name'        => 'Pizza Thanh House',
                'phone'       => '0905555555',
                'address'     => '99 Cách Mạng Tháng 8, Quận 10, TP.HCM',
                'description' => 'Pizza kiểu Ý đế mỏng giòn. Nguyên liệu nhập khẩu: phô mai mozzarella, salami, pepperoni. Giao hàng trong 30 phút.',
                'open_time'   => '10:00:00',
                'close_time'  => '22:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.4,
                'total_orders'=> 210,
                'total_reviews'=> 38,
            ],
            [
                'user_id'     => 6,
                'category_id' => 12, // Chay
                'name'        => 'Quán Chay Thanh Tâm',
                'phone'       => '0905555556',
                'address'     => '14 Nguyễn Đình Chiểu, Quận 3, TP.HCM',
                'description' => 'Thức ăn chay thuần túy: cơm chay, bún chay, bánh cuốn chay, nem chay. Tốt cho sức khỏe, giảm cân hiệu quả.',
                'open_time'   => '07:00:00',
                'close_time'  => '20:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.6,
                'total_orders'=> 175,
                'total_reviews'=> 29,
            ],
            [
                'user_id'     => 7,
                'category_id' => 1, // Trà sữa
                'name'        => 'The Long Tea House',
                'phone'       => '0906666666',
                'address'     => '56 Đinh Tiên Hoàng, Quận Bình Thạnh, TP.HCM',
                'description' => 'Trà sữa Đài Loan chính gốc. Hơn 60 loại thức uống. Trân châu tươi mỗi ngày, không để qua đêm. Giá sinh viên thân thiện.',
                'open_time'   => '08:00:00',
                'close_time'  => '23:00:00',
                'status'      => 'active',
                'rating_avg'  => 4.9,
                'total_orders'=> 490,
                'total_reviews'=> 78,
            ],
            [
                'user_id'     => 7,
                'category_id' => 17, // Gà rán
                'name'        => 'Gà Rán Long Vàng',
                'phone'       => '0906666667',
                'address'     => '200 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
                'description' => 'Gà rán giòn tẩm bí quyết gia truyền, không dầu ứ. Combo cơm gà, cánh gà, đùi gà siêu giòn. Freeship nội thành.',
                'open_time'   => '10:00:00',
                'close_time'  => '22:30:00',
                'status'      => 'active',
                'rating_avg'  => 4.5,
                'total_orders'=> 280,
                'total_reviews'=> 45,
            ],
            // Shop đang chờ duyệt
            [
                'user_id'     => 2,
                'category_id' => 10, // Hải sản
                'name'        => 'Hải Sản Tươi Sống Hoa',
                'phone'       => '0901111113',
                'address'     => '88 Võ Văn Tần, Quận 3, TP.HCM',
                'description' => 'Hải sản tươi sống nhập từ Vũng Tàu mỗi ngày. Tôm hùm, cua, mực, cá...',
                'open_time'   => '10:00:00',
                'close_time'  => '22:00:00',
                'status'      => 'pending',
                'rating_avg'  => 0.0,
                'total_orders'=> 0,
                'total_reviews'=> 0,
            ],
            [
                'user_id'     => 4,
                'category_id' => 15, // Bánh ngọt
                'name'        => 'Bánh Ngọt Mai Bakery',
                'phone'       => '0903333335',
                'address'     => '12 Ngô Đức Kế, Quận 1, TP.HCM',
                'description' => 'Bánh ngọt Âu châu: croissant, tiramisu, mousse cake. Đặt bánh sinh nhật theo yêu cầu.',
                'open_time'   => '08:00:00',
                'close_time'  => '21:00:00',
                'status'      => 'pending',
                'rating_avg'  => 0.0,
                'total_orders'=> 0,
                'total_reviews'=> 0,
            ],
            // Shop bị khoá
            [
                'user_id'     => 3,
                'category_id' => 16, // Nướng
                'name'        => 'Quán Nướng Vỉa Hè',
                'phone'       => '0902222224',
                'address'     => '33 Trần Quý Cáp, Quận 3, TP.HCM',
                'description' => 'Thịt nướng vỉa hè, không gian ngoài trời.',
                'open_time'   => '17:00:00',
                'close_time'  => '00:00:00',
                'status'      => 'banned',
                'reject_reason' => 'Vi phạm quy định vệ sinh an toàn thực phẩm lần 2',
                'rating_avg'  => 3.2,
                'total_orders'=> 45,
                'total_reviews'=> 10,
            ],
        ];

        foreach ($shops as $shop) {
            Shop::create(array_merge($shop, [
                'slug' => Str::slug($shop['name']),
            ]));
        }

        $this->command->info('✅ ShopSeeder: Đã tạo ' . count($shops) . ' shops');
    }
}
