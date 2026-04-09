@extends('layouts.admin')

@section('title', 'Sửa danh mục')
@section('page-title', 'Sửa danh mục')
@section('page-subtitle', 'Cập nhật tên, icon và trạng thái hiển thị')

@section('content')
    <div class="admin-card" style="max-width:780px;">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Icon danh mục hiện tại</label>
                <div class="mb-3">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}" alt="icon" width="64" height="64" style="border-radius:16px; object-fit:cover;">
                    @else
                        <span class="text-muted">Chưa có icon</span>
                    @endif
                </div>
                <input type="file" name="icon" accept="image/*" class="form-control @error('icon') is-invalid @enderror">
                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Upload file mới để thay icon.</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $category->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Hiển thị trên trang chủ</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
