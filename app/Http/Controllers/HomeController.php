<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy tất cả danh mục để hiển thị lên thanh Tabs
        $categories = Category::all();

        // 2. Khởi tạo query lấy danh sách quán ăn
        $query = Shop::query();

        // 3. Xử lý logic tìm kiếm (Search)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('details', function($q2) use ($searchTerm) {
                      $q2->where('description', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        // 4. Xử lý logic lọc theo danh mục (Filter)
        // Lưu ý: Đảm bảo bảng shops của bạn có cột category_id, 
        // hoặc bạn phải dùng relation whereHas nếu là quan hệ nhiều-nhiều.
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // 5. Lấy kết quả và phân trang (12 quán 1 trang)
        $shops = $query->with(['metrics', 'details'])->latest()->paginate(12);

        // Gán rating thực tế
        $shops->getCollection()->transform(function ($shop) {
            $shop->rating = $shop->metrics->rating_avg ?? 0;
            $shop->total_reviews = $shop->metrics->review_count ?? 0;
            return $shop;
        });

        // Truyền dữ liệu ra view
        return view('index', compact('categories', 'shops'));
    }
}
