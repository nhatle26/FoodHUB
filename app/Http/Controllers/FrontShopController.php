<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class FrontShopController extends Controller
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
            $img = $product->image ?? null;
            if ($img && !str_starts_with($img, 'http')) {
                $img = preg_replace('/^\/?(storage\/)?/', '', $img);
                $img = asset('storage/' . $img);
            }
            $menuCategories[$groupName][] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $img ?: 'https://images.unsplash.com/photo-1544681280-d2dc2c07a3c3?w=300&q=80',
            ];
        }

        // Nếu shop chưa có món nào, hiển thị rỗng
        if(empty($menuCategories)) {
            $menuCategories['Đang cập nhật'] = [];
        }

        $details = $shop->details;
        $metrics = $shop->metrics;
        
        $bannerImg = $details->cover_image ?? null;
        if ($bannerImg && !str_starts_with($bannerImg, 'http')) {
            $bannerImg = asset('storage/' . preg_replace('/^\/?(storage\/)?/', '', $bannerImg));
        }
        
        $logoImg = $details->logo ?? null;
        if ($logoImg && !str_starts_with($logoImg, 'http')) {
            $logoImg = asset('storage/' . preg_replace('/^\/?(storage\/)?/', '', $logoImg));
        }

        $shopData = [
            'name' => $shop->name,
            'status' => $shop->status === 'active' || $shop->status === 'approved' ? 'Đang mở cửa' : 'Đóng cửa',
            'rating' => $metrics->rating_avg ?? 0,
            'reviews_count' => $metrics->review_count ?? 0,
            'category' => $shop->category->name ?? 'Món ăn',
            'address' => $details->address ?? 'Chưa cập nhật',
            'hours' => ($details->open_time ?? false) && ($details->close_time ?? false) 
                ? \Carbon\Carbon::parse($details->open_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($details->close_time)->format('H:i') 
                : '07:00 - 22:00',
            'banner' => $bannerImg ?: asset('images/shop_hero.png'), 
            'logo' => $logoImg ?: 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=200&q=80'
        ];

        return view('shop.show', compact('shopData', 'menuCategories', 'shop'));
    }
}
