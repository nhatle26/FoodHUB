<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopRegistrationController extends Controller
{
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('Shop.register', compact('categories'));
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
            'status' => 'required|in:pending,active',
            'cover_image' => 'nullable|image|max:2048',
            'logo' => 'nullable|image|max:1024',
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

        return back()->with('success', 'Dang ky shop thanh cong');
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
