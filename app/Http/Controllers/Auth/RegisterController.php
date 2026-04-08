<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('Auth.registerShop', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100|unique:shops,name',
            'phone' => 'required|max:15',
            'address' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|max:500',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i|after:open_time',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|dimensions:min_width=1200,min_height=400,ratio=3/1',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024|dimensions:min_width=200,min_height=200,ratio=1/1',
        ], [
            'cover_image.mimes' => 'Ảnh bìa chỉ được phép là JPG hoặc PNG.',
            'cover_image.max' => 'Ảnh bìa không được lớn hơn 2MB.',
            'cover_image.dimensions' => 'Ảnh bìa phải tối thiểu 1200x400 và đúng tỷ lệ 3:1.',
            'logo.mimes' => 'Logo chỉ được phép là JPG hoặc PNG.',
            'logo.max' => 'Logo không được lớn hơn 1MB.',
            'logo.dimensions' => 'Logo phải là ảnh vuông và tối thiểu 200x200.',
            'close_time.after' => 'Giờ đóng cửa phải sau giờ mở cửa.',
        ]);

        $data['user_id'] = 1;
        $data['slug'] = $this->makeSlug($data['name']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('shops/covers', 'public');
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('shops/logos', 'public');
        }

        Shop::create($data);

        return back()->with('success', 'Đăng ký shop thành công');
    }

    private function makeSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while (Shop::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}