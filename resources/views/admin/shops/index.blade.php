@extends('layouts.admin')

@section('title', 'Quản lý Shop')
@section('page-title', 'Quản lý Shop')
@section('page-subtitle', 'Danh sách tất cả shop trên FoodHub')

@section('content')
{{-- Toast --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stat badges --}}
<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="{{ route('admin.shops.index') }}"
       class="badge rounded-pill fs-6 px-3 py-2 text-decoration-none {{ !request('status') ? 'bg-primary' : 'bg-secondary bg-opacity-25 text-dark' }}">
        Tất cả ({{ $statusCounts->sum() }})
    </a>
    @foreach(['active' => ['Đang hoạt động','success'], 'pending' => ['Chờ duyệt','warning'], 'banned' => ['Bị cấm','danger']] as $s => [$label, $color])
    <a href="{{ route('admin.shops.index', ['status' => $s]) }}"
       class="badge rounded-pill fs-6 px-3 py-2 text-decoration-none {{ request('status') === $s ? 'bg-'.$color : 'bg-secondary bg-opacity-25 text-dark' }}">
        {{ $label }} ({{ $statusCounts[$s] ?? 0 }})
    </a>
    @endforeach
</div>

{{-- Search bar --}}
<form method="GET" action="{{ route('admin.shops.index') }}" class="mb-4 d-flex gap-2">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-control rounded-3" placeholder="Tìm tên shop..." style="max-width:320px">
    <button type="submit" class="btn btn-primary rounded-3">
        <i class="fas fa-search me-1"></i> Tìm
    </button>
</form>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Shop</th>
                    <th>Danh mục</th>
                    <th>Địa chỉ</th>
                    <th class="text-center">Sản phẩm</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            @php 
                                $logo = $shop->details->logo ?? null; 
                                $logoUrl = $logo ? (str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo)) : null;
                            @endphp
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" class="rounded-circle" width="40" height="40" style="object-fit:cover">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                    <i class="fas fa-store text-primary"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $shop->name }}</div>
                                <small class="text-muted">{{ $shop->details->phone ?? '—' }}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $shop->category->name ?? '—' }}</span></td>
                    <td class="text-muted small" style="max-width:200px">{{ Str::limit($shop->details->address ?? '—', 45) }}</td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-15 text-info fw-semibold">{{ $shop->products_count }}</span>
                    </td>
                    <td>
                        @php
                            $sc = ['active'=>'success','pending'=>'warning','banned'=>'danger'][$shop->status] ?? 'secondary';
                            $sl = ['active'=>'Đang hoạt động','pending'=>'Chờ duyệt','banned'=>'Bị cấm'][$shop->status] ?? $shop->status;
                        @endphp
                        <span class="badge bg-{{ $sc }}">{{ $sl }}</span>
                    </td>
                    <td class="text-muted small">{{ $shop->created_at->format('d/m/Y') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.shops.show', $shop->id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill me-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.shops.edit', $shop->id) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">Không có shop nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3 px-4">
        {{ $shops->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
