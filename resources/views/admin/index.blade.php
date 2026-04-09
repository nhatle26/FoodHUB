@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')
@section('page-title', 'Quản lý Người dùng')
@section('page-subtitle', 'Danh sách và chỉnh sửa tài khoản hệ thống')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Danh sách người dùng</h4>
            <p class="text-small mb-0">Quản lý user admin, shop và khách hàng.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">➕ Tạo user mới</a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif ($user->role === 'shop')
                                    <span class="badge bg-primary">Shop</span>
                                @else
                                    <span class="badge bg-info">Customer</span>
                                @endif
                            </td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success">✓ Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">✗ Vô hiệu</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có user nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
