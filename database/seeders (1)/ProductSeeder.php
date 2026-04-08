<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ---- Shop 1: Trà Sữa Hoa Đào (shop_id = 1) ----
            ['shop_id'=>1,'name'=>'Trà sữa trân châu đường đen','description'=>'Trà sữa trân châu nâu đường đen thơm ngon, béo ngậy','price'=>45000,'product_group'=>'Trà sữa','is_available'=>true,'total_sold'=>180],
            ['shop_id'=>1,'name'=>'Matcha latte trân châu','description'=>'Matcha Nhật Bản kết hợp sữa tươi béo ngậy, topping trân châu trắng','price'=>50000,'product_group'=>'Trà sữa','is_available'=>true,'total_sold'=>145],
            ['shop_id'=>1,'name'=>'Hồng trà sữa','description'=>'Hồng trà Đài Loan kết hợp sữa tươi nhập khẩu, không đường hoặc ít đường','price'=>40000,'product_group'=>'Trà sữa','is_available'=>true,'total_sold'=>210],
            ['shop_id'=>1,'name'=>'Trà đào cam sả','description'=>'Thanh mát vị đào tươi, cam vắt, sả thơm. Topping thạch trái cây','price'=>45000,'product_group'=>'Trà trái cây','is_available'=>true,'total_sold'=>165],
            ['shop_id'=>1,'name'=>'Cà phê sữa đá cuộn','description'=>'Cà phê sữa đặc pha đá viên cuộn mịn. Đậm đà thơm béo.','price'=>35000,'product_group'=>'Cà phê','is_available'=>true,'total_sold'=>98],

            // ---- Shop 3: Cơm Tấm Sài Gòn Tuấn (shop_id = 3) ----
            ['shop_id'=>3,'name'=>'Cơm tấm sườn bì chả','description'=>'Sườn nướng mật ong, bì sợi, chả trứng. Kèm dưa leo, cà chua, nước mắm pha.','price'=>55000,'product_group'=>'Cơm tấm','is_available'=>true,'total_sold'=>520],
            ['shop_id'=>3,'name'=>'Cơm tấm sườn nướng đặc biệt','description'=>'Sườn non nướng than hoa, ướp gia vị đặc biệt. Size lớn ăn no.','price'=>65000,'product_group'=>'Cơm tấm','is_available'=>true,'total_sold'=>380],
            ['shop_id'=>3,'name'=>'Cơm gà rau sống','description'=>'Cơm trắng dẻo với gà luộc mềm ngọt, rau sống, nước mắm gừng.','price'=>50000,'product_group'=>'Cơm gà','is_available'=>true,'total_sold'=>290],
            ['shop_id'=>3,'name'=>'Canh chua cá lóc','description'=>'Canh chua miền Nam truyền thống. Cá lóc tươi, cà chua, giá đỗ, bạc hà.','price'=>35000,'product_group'=>'Canh','is_available'=>true,'total_sold'=>145],

            // ---- Shop 5: Ăn Vặt Mai Béo (shop_id = 5) ----
            ['shop_id'=>5,'name'=>'Bánh tráng trộn đặc biệt','description'=>'Bánh tráng trộn tôm khô, xoài, sốt tương, hành phi, đậu phộng rang.','price'=>25000,'product_group'=>'Bánh tráng','is_available'=>true,'total_sold'=>450],
            ['shop_id'=>5,'name'=>'Xúc xích nướng 5 cái','description'=>'Xúc xích Đức nướng than, kẹp bánh mì nho nhỏ. Sốt cà chua + tương ớt.','price'=>30000,'product_group'=>'Xiên que','is_available'=>true,'total_sold'=>380],
            ['shop_id'=>5,'name'=>'Trứng cút lắc muối tắc','description'=>'Trứng cút chiên giòn lắc muối tắc ớt. Cay cay chua chua hấp dẫn.','price'=>20000,'product_group'=>'Trứng cút','is_available'=>true,'total_sold'=>310],
            ['shop_id'=>5,'name'=>'Khoai tây lắc phô mai','description'=>'Khoai tây chiên giòn, tẩm phô mai và gia vị bí truyền. Size M đủ no.','price'=>35000,'product_group'=>'Khoai tây','is_available'=>true,'total_sold'=>275],
            ['shop_id'=>5,'name'=>'Bắp xào bơ đặc biệt','description'=>'Bắp Mỹ xào bơ, muối, đường. Thêm phô mai tan chảy. Thơm béo ngậy.','price'=>25000,'product_group'=>'Bắp','is_available'=>true,'total_sold'=>220],

            // ---- Shop 7: Bánh Mì Phượng Bảo (shop_id = 7) ----
            ['shop_id'=>7,'name'=>'Bánh mì thịt nguội đặc biệt','description'=>'Bánh mì ổ giòn nhân pate, giăm bông, chả lụa, dưa chuột, rau mùi. Sốt gia truyền.','price'=>25000,'product_group'=>'Bánh mì','is_available'=>true,'total_sold'=>680],
            ['shop_id'=>7,'name'=>'Bánh mì trứng ốp la','description'=>'Bánh mì giòn, trứng ốp la chảy lòng đào, pate, bơ, xì dầu.','price'=>20000,'product_group'=>'Bánh mì','is_available'=>true,'total_sold'=>520],
            ['shop_id'=>7,'name'=>'Bánh mì xíu mại','description'=>'Xíu mại thịt heo sốt cà chua đậm đà. Ổ bánh nóng hổi giòn tan.','price'=>22000,'product_group'=>'Bánh mì','is_available'=>true,'total_sold'=>440],

            // ---- Shop 11: The Long Tea House (shop_id = 11) ----
            ['shop_id'=>11,'name'=>'Trà sữa oolong topping đặc biệt','description'=>'Trà oolong Đài Loan thơm hoa quê, sữa tươi nhập khẩu. Topping: trân châu nâu + pudding trứng + thạch cà phê.','price'=>55000,'product_group'=>'Trà sữa cao cấp','is_available'=>true,'total_sold'=>390],
            ['shop_id'=>11,'name'=>'Brown sugar milk tea','description'=>'Trà sữa đường đen Đài Loan trứ danh. Trân châu đen dẻo dai siêu ngon.','price'=>50000,'product_group'=>'Trà sữa cao cấp','is_available'=>true,'total_sold'=>420],
            ['shop_id'=>11,'name'=>'Yakult dứa ổi','description'=>'Yakult xay dứa ổi thanh mát. Topping thạch trái cây đầy màu sắc.','price'=>45000,'product_group'=>'Trà trái cây','is_available'=>true,'total_sold'=>280],
        ];

        foreach ($products as $index => $product) {
            Product::create(array_merge($product, [
                'sort_order' => $index,
            ]));
        }

        $this->command->info('✅ ProductSeeder: Đã tạo ' . count($products) . ' products');
    }
}
