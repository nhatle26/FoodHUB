@extends('layouts.shop')

@section('title', 'Quản lý Voucher')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Mã giảm giá (Vouchers)</h2>
        <p class="text-muted">Tạo và quản lý các mã khuyến mãi cho khách hàng</p>
    </div>
    <a href="{{ route('shop.vouchers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tạo Voucher mới
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Mã Voucher</th>
                    <th>Loại</th>
                    <th>Giá trị</th>
                    <th>Đơn tối thiểu</th>
                    <th>Lượt dùng</th>
                    <th>Hết hạn</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td class="ps-4 fw-bold text-primary">{{ $voucher->code }}</td>
                    <td>{{ $voucher->type === 'percent' ? 'Giảm theo %' : 'Giảm tiền (VNĐ)' }}</td>
                    <td class="fw-bold">{{ $voucher->type === 'percent' ? $voucher->value . '%' : number_format($voucher->value) . '₫' }}</td>
                    <td>{{ $voucher->min_order_amount ? number_format($voucher->min_order_amount) . '₫' : 'Không' }}</td>
                    <td>{{ $voucher->usage_limit ? $voucher->used_count . ' / ' . $voucher->usage_limit : 'Không giới hạn' }}</td>
                    <td>{{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y') : 'Không' }}</td>
                    <td>
                        @if($voucher->is_active)
                            <span class="badge bg-success">Đang kích hoạt</span>
                        @else
                            <span class="badge bg-secondary">Tạm dừng</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('shop.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                        <form action="{{ route('shop.vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa voucher này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Chưa có mã giảm giá nào được tạo.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
    <div class="card-footer bg-white border-0 mt-2">
        {{ $vouchers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
