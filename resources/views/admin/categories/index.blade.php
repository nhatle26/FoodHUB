@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')
@section('page-title', 'Quản lý Danh mục')
@section('page-subtitle', 'Quản lý danh mục lớn: Trà sữa, Đồ ăn vặt, ...')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Danh sách danh mục</h4>
            <p class="text-small mb-0">Thêm, sửa, xoá và điều chỉnh hiển thị icon trên trang chủ.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Thêm danh mục mới</a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Danh mục</th>
                        <th>Icon</th>
                        <th>Slug</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                @if($category->icon)
                                    <img src="{{ asset('storage/' . $category->icon) }}" alt="icon" width="42" height="42" style="border-radius:12px; object-fit:cover;">
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <span class="status-chip {{ $category->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $category->is_active ? 'Hiện' : 'Ẩn' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá danh mục này?')">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có danh mục nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
