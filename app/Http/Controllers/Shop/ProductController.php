<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Get shop for authenticated user
     */
    private function getShop()
    {
        $shop = Shop::where('user_id', Auth::id())->first();
        if (!$shop) {
            abort(403, 'Unauthorized');
        }
        return $shop;
    }

    /**
     * Display a listing of products
     */
    public function index()
    {
        $shop = $this->getShop();
        $products = $shop->products()->paginate(10);

        return view('shop.products.index', compact('shop', 'products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $shop = $this->getShop();

        return view('shop.products.create', compact('shop'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $shop = $this->getShop();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999999',
            'product_group' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_available' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['shop_id'] = $shop->id;
        $validated['is_available'] = $request->has('is_available');
        $validated['total_sold'] = 0;
        $validated['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('shop.products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    /**
     * Show the form for editing the product
     */
    public function edit(Product $product)
    {
        $shop = $this->getShop();

        if ($product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        return view('shop.products.edit', compact('shop', 'product'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $shop = $this->getShop();

        if ($product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999999',
            'product_group' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_available' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('shop.products.index')->with('success', 'Sản phẩm đã được cập nhật!');
    }

    /**
     * Delete the specified product
     */
    public function destroy(Product $product)
    {
        $shop = $this->getShop();

        if ($product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized');
        }

        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('shop.products.index')->with('success', 'Sản phẩm đã được xóa!');
    }
}
