@extends('layouts.shop')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="products-header">
        <div>
            <h2>Sản phẩm của bạn</h2>
            <p class="text-muted">Tổng: {{ $products->total() }} sản phẩm</p>
        </div>
        <a href="{{ route('shop.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm
        </a>
    </div>

    @if($products->count() > 0)
        <div class="products-table-wrapper">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Nhóm</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Đã bán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-thumb">
                                @else
                                    <div class="product-thumb-empty">No image</div>
                                @endif
                            </td>
                            <td>
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-desc">{{ Str::limit($product->description, 50) }}</div>
                            </td>
                            <td>{{ $product->product_group }}</td>
                            <td class="price">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                            <td>
                                @if($product->is_available)
                                    <span class="badge badge-success">Có sẵn</span>
                                @else
                                    <span class="badge badge-danger">Hết hàng</span>
                                @endif
                            </td>
                            <td class="sold">{{ $product->total_sold }}</td>
                            <td class="actions">
                                <a href="{{ route('shop.products.edit', $product) }}" class="action-link edit"><i class="bi bi-pencil"></i> Sửa</a>
                                <form action="{{ route('shop.products.destroy', $product) }}" method="POST" class="action-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-link delete" onclick="return confirm('Xác nhận xóa?')"><i class="bi bi-trash3"></i> Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-box"></i></div>
            <h3>Chưa có sản phẩm nào</h3>
            <p>Hãy thêm sản phẩm đầu tiên của bạn ngay</p>
            <a href="{{ route('shop.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm
            </a>
        </div>
    @endif
@endsection

@push('scripts')
    <style>
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .products-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .products-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
        }

        .btn-primary {
            background: #f97316;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #ea580c;
        }

        .products-table-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            margin-bottom: 25px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table thead {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .products-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .products-table tbody tr:hover {
            background: #fafafa;
        }

        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-thumb-empty {
            width: 50px;
            height: 50px;
            background: #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #9ca3af;
        }

        .product-name {
            font-weight: 600;
            color: #1f2937;
        }

        .product-desc {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .price {
            font-weight: 600;
            color: #f97316;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .action-link {
            text-decoration: none;
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            background: none;
        }

        .action-link.edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-link.edit:hover {
            background: #bfdbfe;
        }

        .action-link.delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-link.delete:hover {
            background: #fecaca;
        }

        .action-delete {
            display: contents;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: 700;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .products-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .products-table {
                font-size: 12px;
            }

            .products-table th,
            .products-table td {
                padding: 10px;
            }

            .product-thumb {
                width: 40px;
                height: 40px;
            }

            .actions {
                flex-direction: column;
                gap: 5px;
            }

            .action-link {
                font-size: 11px;
                padding: 4px 8px;
            }
        }
    </style>
@endpush
