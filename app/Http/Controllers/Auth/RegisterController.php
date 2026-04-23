<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('Auth.user_register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Tạo user mới với role mặc định là customer
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
        ]);

        // Tự động đăng nhập
        Auth::login($user);

        // Redirect về trang chủ
        return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với FoodHub.');
    }
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('Auth.registerShop', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|max:100|unique:shops,name',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'required|max:15',
            'address'       => 'required|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|max:500',
            'open_time'     => 'nullable|date_format:H:i',
            'close_time'    => 'nullable|date_format:H:i|after:open_time',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'logo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            // Base64 crop data (từ Cropper.js)
            'cover_image_data' => 'nullable|string',
            'logo_data'        => 'nullable|string',
        ], [
            'email.unique'      => 'Email này đã được sử dụng.',
            'password.min'      => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'cover_image.mimes' => 'Ảnh bìa chỉ được phép là JPG, PNG hoặc WEBP.',
            'cover_image.max'   => 'Ảnh bìa không được lớn hơn 8MB.',
            'logo.mimes'        => 'Logo chỉ được phép là JPG, PNG hoặc WEBP.',
            'logo.max'          => 'Logo không được lớn hơn 4MB.',
            'close_time.after'  => 'Giờ đóng cửa phải sau giờ mở cửa.',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'shop',
        ]);

        $shop = Shop::create([
            'user_id' => $user->id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $this->makeSlug($data['name']),
            'status' => 'pending',
        ]);

        // Lưu ảnh bìa
        $cover_image = null;
        if ($request->filled('cover_image_data')) {
            $cover_image = $this->saveBase64Image($request->cover_image_data, 'shops/covers');
        } elseif ($request->hasFile('cover_image')) {
            $cover_image = $this->saveUploadedImage($request->file('cover_image'), 'shops/covers');
        }

        // Lưu logo
        $logo = null;
        if ($request->filled('logo_data')) {
            $logo = $this->saveBase64Image($request->logo_data, 'shops/logos');
        } elseif ($request->hasFile('logo')) {
            $logo = $this->saveUploadedImage($request->file('logo'), 'shops/logos');
        }

        \App\Models\ShopDetail::create([
            'shop_id' => $shop->id,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'description' => $data['description'] ?? null,
            'cover_image' => $cover_image,
            'logo' => $logo,
            'open_time' => $data['open_time'] ?? null,
            'close_time' => $data['close_time'] ?? null,
        ]);
        
        \App\Models\ShopMetric::create([
            'shop_id' => $shop->id,
        ]);

        // Tự động đăng nhập
        Auth::login($user);

        return redirect()->route('shop.dashboard')->with('success', 'Đăng ký shop thành công! Vui lòng hoàn tất thiết lập.');
    }

    /**
     * Lưu ảnh từ base64 string (Cropper.js) - không gọi GD library
     */
    private function saveBase64Image(string $base64Data, string $folder): string
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($imageData);

        $filename = $folder . '/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Lưu ảnh từ file upload fallback
     */
    private function saveUploadedImage($file, string $folder): string
    {
        $filename = $folder . '/' . Str::uuid() . '.' . $file->extension();
        Storage::disk('public')->putFileAs($folder, $file, basename($filename));

        return $filename;
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

