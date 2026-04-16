<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        $products = \App\Models\Product::where('shop_id', $shop->id)
                        ->where('is_available', true)
                        ->orderBy('sort_order')
                        ->get();

        $menuCategories = [];
        foreach($products as $product) {
            $groupName = $product->product_group ?? 'Khác';
            if (!isset($menuCategories[$groupName])) {
                $menuCategories[$groupName] = [];
            }
            $menuCategories[$groupName][] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                // Replace storage path logic as needed, assuming image exists
                'image' => $product->image ? asset('storage/'.$product->image) : 'https://images.unsplash.com/photo-1544681280-d2dc2c07a3c3?w=300&q=80',
            ];
        }

        // Nếu shop chưa có món nào, hiển thị rỗng
        if(empty($menuCategories)) {
            $menuCategories['Đang cập nhật'] = [];
        }

        $shopData = [
            'name' => $shop->name,
            'status' => $shop->status === 'active' || $shop->status === 'approved' ? 'Đang mở cửa' : 'Đóng cửa',
            'rating' => 4.8,
            'reviews_count' => 124,
            'category' => 'Món ăn', // Can be optimized if relationship is loaded
            'address' => $shop->address,
            'hours' => $shop->open_time && $shop->close_time 
                ? \Carbon\Carbon::parse($shop->open_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($shop->close_time)->format('H:i') 
                : '07:00 - 22:00',
            'banner' => $shop->cover_image ? asset('storage/'.$shop->cover_image) : asset('images/shop_hero.png'), 
            'logo' => $shop->logo ? asset('storage/'.$shop->logo) : 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=200&q=80'
        ];

        return view('shop.show', compact('shopData', 'menuCategories', 'shop'));
    }
}
