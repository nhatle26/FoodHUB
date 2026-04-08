@extends('layouts.admin')

@section('title', 'Shop chờ duyệt')
@section('page-title', 'Shop chờ duyệt')
@section('page-subtitle', 'Danh sách tất cả shop đang chờ duyệt để hiện thị trên FoodHub')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Shop chờ duyệt</h4>
            <p class="text-small mb-0">Xem toàn bộ shop đang ở trạng thái pending và xử lý duyệt hoặc từ chối.</p>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên shop</th>
                        <th>Địa chỉ</th>
                        <th>Điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingShops as $index => $shop)
                        <tr>
                            <td>{{ $pendingShops->firstItem() + $index }}</td>
                            <td>{{ $shop->name }}</td>
                            <td>{{ Str::limit($shop->address, 60) }}</td>
                            <td>{{ $shop->phone }}</td>
                            <td><span class="status-chip status-active">Đang chờ</span></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($shop->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.shops.approve', $shop->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                                <form action="{{ route('admin.shops.reject', $shop->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có shop nào đang chờ duyệt.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $pendingShops->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
