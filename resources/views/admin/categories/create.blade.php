@extends('layouts.admin')

@section('title', 'Thêm danh mục')
@section('page-title', 'Thêm danh mục mới')
@section('page-subtitle', 'Tạo một danh mục lớn mới và hiển thị icon trên trang chủ')

@section('content')
    <div class="admin-card" style="max-width:780px;">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Icon danh mục</label>
                <input type="file" name="icon" accept="image/*" class="form-control @error('icon') is-invalid @enderror">
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Upload ảnh icon để hiển thị trên trang chủ.</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Hiển thị trên trang chủ</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu danh mục</button>
            </div>
        </form>
    </div>
@endsection
