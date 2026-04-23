@extends('layouts.shop')

@section('title', 'Thiết lập Shop')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    .upload-box { border: 1px dashed #d7dbe3; border-radius: 12px; padding: 16px; background: #fafafa; position: relative; }
    .upload-box.has-image .upload-trigger { display: none; }
    .upload-trigger { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; width: 100%; min-height: 96px; border-radius: 10px; background: #fff7f3; color: #ea580c; text-align: center; cursor: pointer; font-weight: 600; padding:15px; }
    .upload-icon-badge { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #fff; color: #ea580c; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.1); }
    .upload-icon-badge svg { width: 20px; height: 20px; fill: currentColor; }
    .upload-actions { display: none; gap: 10px; margin-top: 12px; }
    .upload-box.has-image .upload-actions { display: flex; }
    .upload-action-btn { border: 1px solid #e5e7eb; background: #fff; color: #4b5563; border-radius: 8px; padding: 6px 12px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .upload-preview { display: none; width: 100%; max-height: 150px; object-fit: cover; border-radius: 8px; margin-top: 12px; border: 1px solid #e5e7eb; }
    .logo-preview { display: none; width: 86px; height: 86px; object-fit: cover; border-radius: 8px; margin-top: 12px; border: 1px solid #e5e7eb; background: #fff; }

    .cropper-modal-container { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center; }
    .cropper-modal-content { background: #fff; border-radius: 12px; padding: 20px; width: 90%; max-width: 600px; }
    .img-container { max-height: 400px; width: 100%; margin-bottom: 20px; }
    .img-container img { max-width: 100%; }
</style>

<div class="page-header">
    <div>
        <h2>Thiết lập Shop</h2>
        <p class="text-muted">Cập nhật thông tin và trạng thái cửa hàng của bạn</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('shop.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Shop</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shop->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $shop->description) }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $shop->details->phone ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giờ mở cửa</label>
                            <div class="d-flex align-items-center">
                                <input type="time" name="open_time" class="form-control" value="{{ old('open_time', $shop->details->open_time ?? '') }}">
                                <span class="mx-2">-</span>
                                <input type="time" name="close_time" class="form-control" value="{{ old('close_time', $shop->details->close_time ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $shop->details->address ?? '') }}">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ảnh Logo (1:1)</label>
                            <div class="upload-box {{ ($shop->details->logo ?? false) ? 'has-image' : '' }}" id="logo_box">
                                <label for="logo" class="upload-trigger" id="logo_trigger">
                                    <span class="upload-icon-badge">
                                        <svg viewBox="0 0 16 16" class="icon-svg"><path d="M14.002 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2.001a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 4v8h12V4zm10.648 7H3.354l2.387-3.102a.5.5 0 0 1 .79-.01l1.203 1.5 2.402-3.104a.5.5 0 0 1 .79.017zM4.502 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/></svg>
                                    </span>
                                    <span>Chọn logo mới</span>
                                </label>
                                <input type="file" id="logo" class="d-none" accept="image/jpeg,image/png,image/webp">
                                <input type="hidden" name="logo_data" id="logo_data">
                                <img id="logo_preview" class="logo-preview" 
                                    style="{{ ($shop->details->logo ?? false) ? 'display:block;' : '' }}" 
                                    src="{{ ($shop->details->logo ?? false) ? asset('storage/' . $shop->details->logo) : '' }}">
                                <div class="upload-actions" id="logo_actions">
                                    <label for="logo" class="upload-action-btn mb-0">Đổi ảnh</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ảnh Banner (3:1)</label>
                            <div class="upload-box {{ ($shop->details->cover_image ?? false) ? 'has-image' : '' }}" id="cover_box">
                                <label for="cover_image" class="upload-trigger" id="cover_trigger">
                                    <span class="upload-icon-badge">
                                        <svg viewBox="0 0 16 16" class="icon-svg"><path d="M8 0a5.53 5.53 0 0 0-5.234 3.712A4.5 4.5 0 0 0 4.5 12H7V9H5.707L8 6.707 10.293 9H9v3h2.5a3.5 3.5 0 0 0 .604-6.948A5.53 5.53 0 0 0 8 0"/><path d="M7.5 12.5h1v3h-1z"/></svg>
                                    </span>
                                    <span>Chọn ảnh bìa mới</span>
                                </label>
                                <input type="file" id="cover_image" class="d-none" accept="image/jpeg,image/png,image/webp">
                                <input type="hidden" name="cover_image_data" id="cover_image_data">
                                <img id="cover_preview" class="upload-preview" 
                                    style="{{ ($shop->details->cover_image ?? false) ? 'display:block;' : '' }}" 
                                    src="{{ ($shop->details->cover_image ?? false) ? asset('storage/' . $shop->details->cover_image) : '' }}">
                                <div class="upload-actions" id="cover_actions">
                                    <label for="cover_image" class="upload-action-btn mb-0">Đổi ảnh</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-4">
            <h5 class="fw-bold mb-3">Trạng thái hoạt động</h5>
            
            <div class="mb-4">
                @if($shop->status === 'active')
                    <span class="badge bg-success fs-6 px-4 py-2">Đang Mở Cửa</span>
                    <p class="text-muted mt-2 small">Khách hàng có thể nhìn thấy và đặt món từ shop của bạn.</p>
                @elseif($shop->status === 'inactive')
                    <span class="badge bg-secondary fs-6 px-4 py-2">Tạm Đóng Cửa</span>
                    <p class="text-muted mt-2 small">Shop của bạn hiện đang ẩn đối với khách hàng.</p>
                @elseif($shop->status === 'pending')
                    <span class="badge bg-warning fs-6 px-4 py-2">Chờ Duyệt</span>
                    <p class="text-muted mt-2 small">Shop đang chờ Quản trị viên duyệt.</p>
                @else
                    <span class="badge bg-danger fs-6 px-4 py-2">Đã Khóa</span>
                @endif
            </div>

            @if($shop->status === 'active' || $shop->status === 'inactive')
            <form action="{{ route('shop.settings.toggle_status') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $shop->status === 'active' ? 'outline-danger' : 'success' }} w-100">
                    {{ $shop->status === 'active' ? 'Tạm đóng cửa' : 'Mở cửa trở lại' }}
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

    {{-- Cropper Modal --}}
    <div id="cropperModal" class="cropper-modal-container">
        <div class="cropper-modal-content">
            <h5 class="mb-3 fw-bold">Cắt ảnh</h5>
            <div class="img-container">
                <img id="cropperImage" src="" alt="Ảnh cần cắt">
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" id="btnCancelCrop">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnApplyCrop">Xác nhận</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper = null;
        let currentTarget = null; // 'cover' or 'logo'

        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const btnCancelCrop = document.getElementById('btnCancelCrop');
        const btnApplyCrop = document.getElementById('btnApplyCrop');

        function setupUpload(inputId, previewId, boxId, dataInputId, ratio) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const box = document.getElementById(boxId);
            const dataInput = document.getElementById(dataInputId);
            
            if (!input || !preview || !box || !dataInput) return;

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert('Chỉ hỗ trợ ảnh JPG, PNG hoặc WEBP');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperModal.style.display = 'flex';
                    
                    currentTarget = {
                        type: inputId.includes('cover') ? 'cover' : 'logo',
                        preview,
                        box,
                        dataInput,
                        ratio
                    };

                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: ratio,
                        viewMode: 1,
                        autoCropArea: 1,
                        background: false
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        btnCancelCrop.addEventListener('click', function() {
            cropperModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (currentTarget) {
                document.getElementById(currentTarget.type === 'cover' ? 'cover_image' : 'logo').value = '';
            }
        });

        btnApplyCrop.addEventListener('click', function() {
            if (!cropper || !currentTarget) return;

            const canvas = cropper.getCroppedCanvas({
                width: currentTarget.type === 'cover' ? 1200 : 400,
                height: currentTarget.type === 'cover' ? 400 : 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const base64data = canvas.toDataURL('image/jpeg', 0.85);
            
            currentTarget.dataInput.value = base64data;
            currentTarget.preview.src = base64data;
            currentTarget.preview.style.display = 'block';
            currentTarget.box.classList.add('has-image');

            cropperModal.style.display = 'none';
            cropper.destroy();
            cropper = null;
        });

        setupUpload('cover_image', 'cover_preview', 'cover_box', 'cover_image_data', 3/1);
        setupUpload('logo', 'logo_preview', 'logo_box', 'logo_data', 1/1);
    </script>
@endpush
