@extends('layouts.shop')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
    <div class="form-header">
        <h2>Chỉnh sửa sản phẩm</h2>
        <a href="{{ route('shop.products.index') }}" class="btn-back"><i class="bi bi-chevron-left"></i> Quay lại</a>
    </div>

    <form action="{{ route('shop.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="product-form">
        @csrf
        @method('PUT')

        <div class="form-container">
            <!-- Left Column -->
            <div class="form-left">
                <!-- Tên sản phẩm -->
                <div class="form-group">
                    <label for="name">Tên sản phẩm <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="VD: Phở Bò Hà Nội" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Giá -->
                <div class="form-group">
                    <label for="price">Giá (₫) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}"
                           class="form-control @error('price') is-invalid @enderror"
                           placeholder="50000" min="0" step="1000" required>
                    @error('price')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nhóm sản phẩm -->
                <div class="form-group">
                    <label for="product_group">Nhóm sản phẩm <span class="required">*</span></label>
                    <input type="text" id="product_group" name="product_group" value="{{ old('product_group', $product->product_group) }}"
                           class="form-control @error('product_group') is-invalid @enderror"
                           placeholder="VD: Phở, Cơm, Nước ép" required>
                    @error('product_group')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label for="sort_order">Thứ tự hiển thị</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}"
                           class="form-control" placeholder="0" min="0">
                    <small class="form-text">Số nhỏ hiển thị trước</small>
                </div>

                <!-- Trạng thái -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                        <span>Có sẵn (bật để hiển thị bán ngay)</span>
                    </label>
                </div>

                <!-- Info -->
                <div class="product-info">
                    <div class="info-item">
                        <span class="label">Đã bán:</span>
                        <span class="value">{{ $product->total_sold }} cái</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Cập nhật lần cuối:</span>
                        <span class="value">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="form-right">
                <!-- Ảnh sản phẩm -->
                <div class="form-group">
                    <label for="image">Ảnh sản phẩm</label>
                    <div class="upload-area" id="uploadArea">
                        <input type="file" id="image" name="image" accept="image/*" class="file-input">
                        <div class="upload-content">
                            <div class="upload-icon"><i class="bi bi-image"></i></div>
                            <div class="upload-text">Kéo ảnh vào đây hoặc nhấp để chọn</div>
                            <small>JPG, PNG - Tối đa 2MB</small>
                        </div>
                    </div>
                    @if($product->image)
                        <img id="preview" class="image-preview" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <img id="preview" class="image-preview" alt="Xem trước">
                    @endif
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mô tả -->
                <div class="form-group">
                    <label for="description">Mô tả sản phẩm <span class="optional">(Tuỳ chọn)</span></label>
                    <textarea id="description" name="description" rows="6"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Nhập chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                    <div class="char-counter">
                        <span id="charCount">{{ strlen(old('description', $product->description)) }}</span>/1000 ký tự
                    </div>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Button -->
        <div class="form-actions">
            <a href="{{ route('shop.products.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">✅ Cập nhật</button>
            <button type="button" class="btn btn-danger" onclick="if(confirm('Xác nhận xóa sản phẩm này?')) document.getElementById('deleteForm').submit()">
                <i class="bi bi-trash3"></i> Xóa sản phẩm
            </button>
        </div>
    </form>

    <!-- Separate Delete Form -->
    <form id="deleteForm" action="{{ route('shop.products.destroy', $product) }}" method="POST" class="delete-form" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <style>
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .btn-back {
            text-decoration: none;
            color: #f97316;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(249, 115, 22, 0.1);
        }

        .product-form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 30px;
        }

        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required {
            color: #ef4444;
        }

        .optional {
            color: #9ca3af;
            font-weight: 400;
            font-size: 12px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
        }

        .form-text {
            display: block;
            color: #9ca3af;
            font-size: 12px;
            margin-top: 5px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 400;
        }

        .checkbox-label input {
            width: auto;
            cursor: pointer;
        }

        .product-info {
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .info-item .label {
            font-weight: 600;
            color: #6b7280;
        }

        .info-item .value {
            color: #1f2937;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .upload-area:hover {
            border-color: #f97316;
            background: rgba(249, 115, 22, 0.05);
        }

        .upload-area.active {
            border-color: #f97316;
            background: rgba(249, 115, 22, 0.1);
        }

        .file-input {
            display: none;
        }

        .upload-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .upload-text {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .upload-area small {
            color: #9ca3af;
            display: block;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 15px;
            border-radius: 8px;
        }

        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            align-items: center;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .delete-form {
            display: contents;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #f97316;
            color: white;
        }

        .btn-primary:hover {
            background: #ea580c;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .product-form {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn, .delete-form {
                width: 100%;
            }
        }
    </style>

    <script>
        // Image upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('image');
        const preview = document.getElementById('preview');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.classList.add('active');
        }

        function unhighlight(e) {
            uploadArea.classList.remove('active');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            handleFile();
        }

        uploadArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFile);

        function handleFile() {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Character counter
        const description = document.getElementById('description');
        const charCount = document.getElementById('charCount');

        description.addEventListener('input', () => {
            charCount.textContent = description.value.length;
        });
    </script>
@endpush
