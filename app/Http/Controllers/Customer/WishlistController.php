<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('shop')->get();
        return view('customer.wishlist.index', compact('wishlists'));
    }

    public function toggle(Shop $shop)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('shop_id', $shop->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Đã bỏ yêu thích shop.');
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'shop_id' => $shop->id,
            ]);
            return back()->with('success', 'Đã thêm shop vào danh sách yêu thích.');
        }
    }
}
