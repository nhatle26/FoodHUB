@extends('layouts.authShop')

@section('title')
    Đăng ký mở shop
@endsection

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <style>
        .cropper-modal-container { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center; }
        .cropper-modal-content { background: #fff; border-radius: 12px; padding: 20px; width: 90%; max-width: 600px; }
        .img-container { max-height: 400px; width: 100%; margin-bottom: 20px; }
        .img-container img { max-width: 100%; }
    </style>
    @php
        $goStep2 =
            old('description') ||
            old('open_time') ||
            old('close_time') ||
            $errors->has('description') ||
            $errors->has('open_time') ||
            $errors->has('close_time') ||
            $errors->has('logo');
    @endphp

    <div class="card auth-card">
        <div class="card-body p-4 p-lg-4">
            <h2 class="fw-bold mb-1">Đăng ký mở shop</h2>
            <p class="text-muted small mb-3">Điền đầy đủ thông tin để bắt đầu kinh doanh trên FoodHub.</p>

            <div class="step-progress">
                <div class="d-flex align-items-center gap-2">
                    <span id="step-indicator-1"
                        class="step-node {{ $goStep2 ? 'done' : 'active' }}">{{ $goStep2 ? '✓' : '1' }}</span>
                    <span class="small fw-semibold text-secondary">Thông tin cơ bản</span>
                </div>
                <div id="step-line" class="step-line {{ $goStep2 ? 'done' : '' }}"></div>
                <div class="d-flex align-items-center gap-2">
                    <span id="step-indicator-2" class="step-node {{ $goStep2 ? 'active' : 'idle' }}">2</span>
                    <span class="small fw-semibold {{ $goStep2 ? 'text-dark' : 'text-secondary' }}" id="step-label-2">Thông tin bổ sung</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 py-2 small">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Vui lòng kiểm tra lại các thông tin chưa hợp lệ bên dưới.
                </div>
            @endif

            <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data" id="shopForm">
                @csrf

                <div id="form-step-1" style="{{ $goStep2 ? 'display:none;' : '' }}">
                    <div class="d-flex flex-column flex-md-row gap-3 inline-fields mb-3">
                        <div>
                            <label class="form-label fw-semibold">Tên shop</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 1a3 3 0 1 1 0 6 3 3 0 0 1 0-6m0 7c-2.761 0-5 1.567-5 3.5C3 12.328 3.672 13 4.5 13h7c.828 0 1.5-.672 1.5-1.5C13 9.567 10.761 8 8 8"/></svg>
                                </span>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="form-control rounded-4 py-2 input-with-icon @error('name') is-invalid @enderror" placeholder="Ví dụ: Phở Hà Nội, Cơm Tấm Sài Gòn...">
                            </div>
                            @error('name') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M3.654 1.328a.68.68 0 0 1 .737-.17l2.522 1.01a.68.68 0 0 1 .39.805l-.547 2.19a.68.68 0 0 1-.64.516l-1.113.043a11.7 11.7 0 0 0 5.275 5.275l.043-1.113a.68.68 0 0 1 .516-.64l2.19-.547a.68.68 0 0 1 .805.39l1.01 2.522a.68.68 0 0 1-.17.737l-1.065 1.065a1.75 1.75 0 0 1-1.862.437l-.654-.218A15.6 15.6 0 0 1 2.28 4.924l-.218-.654a1.75 1.75 0 0 1 .437-1.862z"/></svg>
                                </span>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="form-control rounded-4 py-2 input-with-icon @error('phone') is-invalid @enderror" placeholder="0901234567">
                            </div>
                            @error('phone') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3 inline-fields mb-3">
                        <div class="w-100">
                            <label class="form-label fw-semibold">Email đăng nhập</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/></svg>
                                </span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="form-control rounded-4 py-2 input-with-icon @error('email') is-invalid @enderror" placeholder="admin@shop.com">
                            </div>
                            @error('email') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="w-100">
                            <label class="form-label fw-semibold">Mật khẩu</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                                </span>
                                <input type="password" name="password" id="password"
                                    class="form-control rounded-4 py-2 input-with-icon @error('password') is-invalid @enderror" placeholder="Tối thiểu 8 ký tự">
                            </div>
                            @error('password') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ shop</label>
                        <div class="input-icon-field">
                            <span class="input-icon">
                                <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 16s6-5.686 6-10A6 6 0 1 0 2 6c0 4.314 6 10 6 10m0-7.5A2.5 2.5 0 1 1 8 3.5a2.5 2.5 0 0 1 0 5"/></svg>
                            </span>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                class="form-control rounded-4 py-2 input-with-icon @error('address') is-invalid @enderror" placeholder="Số nhà, tên đường, phường xã, quận huyện...">
                        </div>
                        @error('address') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Danh mục</label>
                        <div class="category-grid">
                            @foreach ($categories as $category)
                                <label class="category-option" title="{{ $category->name }}">
                                    <input type="radio" name="category_id" value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'checked' : '' }}>
                                    <span class="category-chip">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ảnh bìa</label>
                        <div class="upload-box" id="cover_box">
                            <label for="cover_image" class="upload-trigger" id="cover_trigger">
                                <span class="upload-icon-badge">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 0a5.53 5.53 0 0 0-5.234 3.712A4.5 4.5 0 0 0 4.5 12H7V9H5.707L8 6.707 10.293 9H9v3h2.5a3.5 3.5 0 0 0 .604-6.948A5.53 5.53 0 0 0 8 0"/><path d="M7.5 12.5h1v3h-1z"/></svg>
                                </span>
                                <span>Nhấp để chọn ảnh bìa<br><small class="text-muted fw-normal">Khuyến nghị tỷ lệ 3:1</small></span>
                            </label>
                            <input type="file" id="cover_image" class="d-none" accept="image/jpeg,image/png,image/webp">
                            <input type="hidden" name="cover_image_data" id="cover_image_data">
                            <img id="cover_preview" class="upload-preview" alt="Xem trước ảnh bìa">
                            <div class="upload-actions" id="cover_actions">
                                <label for="cover_image" class="upload-action-btn mb-0">Đổi ảnh</label>
                                <button type="button" class="upload-action-btn delete" id="cover_remove">Xóa ảnh</button>
                            </div>
                        </div>
                        @error('cover_image') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        @error('cover_image_data') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="button" class="btn btn-foodhub rounded-4 w-100 py-2 fw-semibold"
                        onclick="nextStep()">Tiếp theo</button>
                </div>

                <div id="form-step-2" style="{{ $goStep2 ? '' : 'display:none;' }}">
                    <div class="soft-note mb-3">
                        Các trường dưới đây không bắt buộc, nhưng giúp shop của bạn đầy đủ và nổi bật hơn.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Giờ mở cửa</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 3.5a.5.5 0 0 1 .5.5v4.086l2.207 1.293a.5.5 0 0 1-.507.862l-2.45-1.435A.5.5 0 0 1 7.5 8.5V4a.5.5 0 0 1 .5-.5"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14"/></svg>
                                </span>
                                <input type="time" name="open_time" value="{{ old('open_time') }}"
                                    class="form-control rounded-4 py-2 input-with-icon @error('open_time') is-invalid @enderror">
                            </div>
                            @error('open_time') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Giờ đóng cửa</label>
                            <div class="input-icon-field">
                                <span class="input-icon">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M8 3.5a.5.5 0 0 1 .5.5v4.086l2.207 1.293a.5.5 0 0 1-.507.862l-2.45-1.435A.5.5 0 0 1 7.5 8.5V4a.5.5 0 0 1 .5-.5"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14"/></svg>
                                </span>
                                <input type="time" name="close_time" value="{{ old('close_time') }}"
                                    class="form-control rounded-4 py-2 input-with-icon @error('close_time') is-invalid @enderror">
                            </div>
                            @error('close_time') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Logo shop</label>
                        <div class="upload-box" id="logo_box">
                            <label for="logo" class="upload-trigger" id="logo_trigger">
                                <span class="upload-icon-badge">
                                    <svg viewBox="0 0 16 16" class="icon-svg" aria-hidden="true"><path d="M14.002 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2.001a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 4v8h12V4zm10.648 7H3.354l2.387-3.102a.5.5 0 0 1 .79-.01l1.203 1.5 2.402-3.104a.5.5 0 0 1 .79.017zM4.502 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/></svg>
                                </span>
                                <span>Chọn logo shop<br><small class="text-muted fw-normal">Tỷ lệ 1:1</small></span>
                            </label>
                            <input type="file" id="logo" class="d-none" accept="image/jpeg,image/png,image/webp">
                            <input type="hidden" name="logo_data" id="logo_data">
                            <img id="logo_preview" class="logo-preview" alt="Xem trước logo">
                            <div class="upload-actions" id="logo_actions">
                                <label for="logo" class="upload-action-btn mb-0">Đổi ảnh</label>
                                <button type="button" class="upload-action-btn delete" id="logo_remove">Xóa ảnh</button>
                            </div>
                        </div>
                        @error('logo') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                        @error('logo_data') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả shop</label>
                        <textarea name="description" class="form-control rounded-4" rows="4"
                            placeholder="Giới thiệu shop, món đặc trưng, phong cách phục vụ...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-4 w-50 py-2 fw-semibold"
                            onclick="prevStep()">Quay lại</button>
                        <button type="submit" class="btn btn-foodhub rounded-4 w-50 py-2 fw-semibold">Gửi đăng ký</button>
                    </div>
                </div>
            </form>
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
                <button type="button" class="btn btn-brand" id="btnApplyCrop">Xác nhận</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        function nextStep() {
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const address = document.getElementById('address').value.trim();
            const category = document.querySelector('input[name="category_id"]:checked');

            if (!name || !phone || !email || !password || !address || !category) {
                alert('Vui lòng nhập đầy đủ thông tin ở bước 1');
                return;
            }

            document.getElementById('form-step-1').style.display = 'none';
            document.getElementById('form-step-2').style.display = 'block';
            document.getElementById('step-indicator-1').className = 'step-node done';
            document.getElementById('step-indicator-1').innerText = '✓';
            document.getElementById('step-indicator-2').className = 'step-node active';
            document.getElementById('step-line').className = 'step-line done';
            document.getElementById('step-label-2').className = 'small fw-semibold text-dark';
        }

        function prevStep() {
            document.getElementById('form-step-2').style.display = 'none';
            document.getElementById('form-step-1').style.display = 'block';
            document.getElementById('step-indicator-1').className = 'step-node active';
            document.getElementById('step-indicator-1').innerText = '1';
            document.getElementById('step-indicator-2').className = 'step-node idle';
            document.getElementById('step-line').className = 'step-line';
            document.getElementById('step-label-2').className = 'small fw-semibold text-secondary';
        }

        let cropper = null;
        let currentTarget = null; // 'cover' or 'logo'

        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const btnCancelCrop = document.getElementById('btnCancelCrop');
        const btnApplyCrop = document.getElementById('btnApplyCrop');

        function setupUpload(inputId, previewId, boxId, removeId, dataInputId, ratio) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const box = document.getElementById(boxId);
            const removeButton = document.getElementById(removeId);
            const dataInput = document.getElementById(dataInputId);
            
            if (!input || !preview || !box || !removeButton || !dataInput) return;

            function clearPreview() {
                input.value = '';
                dataInput.value = '';
                preview.style.display = 'none';
                preview.removeAttribute('src');
                box.classList.remove('has-image');
            }

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    clearPreview();
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert('Chỉ hỗ trợ ảnh JPG, PNG hoặc WEBP');
                    clearPreview();
                    return;
                }

                if (file.size > 8 * 1024 * 1024) {
                    alert('Kích thước ảnh tối đa 8MB');
                    clearPreview();
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

            removeButton.addEventListener('click', clearPreview);
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

        setupUpload('cover_image', 'cover_preview', 'cover_box', 'cover_remove', 'cover_image_data', 3/1);
        setupUpload('logo', 'logo_preview', 'logo_box', 'logo_remove', 'logo_data', 1/1);
    </script>

@endpush

