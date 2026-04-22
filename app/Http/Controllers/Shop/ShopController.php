<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function dashboard()
    {
        // Get shop info for current authenticated user
        $shop = Shop::where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        return view('shop.dashboard', compact('shop'));
    }

    public function revenue()
    {
        // Get shop info for current authenticated user
        $shop = Shop::where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        return view('shop.revenue', compact('shop'));
    }

    public function settings()
    {
        // Get shop info for current authenticated user
        $shop = Shop::where('user_id', Auth::id())->first();

        if (!$shop) {
            abort(403, 'Unauthorized');
        }

        return view('shop.settings', compact('shop'));
    }
}
