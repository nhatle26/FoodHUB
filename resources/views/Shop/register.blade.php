<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dang ky mo shop</title>
</head>
<body>
    <h1>Dang ky mo shop</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label>Ten shop</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ten shop">
        </div>

        <div>
            <label>So dien thoai</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="So dien thoai">
        </div>

        <div>
            <label>Dia chi</label>
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Dia chi">
        </div>

        <div>
            <label>Danh muc</label>
            <select name="category_id">
                <option value="">Chon danh muc</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Mo ta shop</label>
            <textarea name="description" placeholder="Mo ta shop">{{ old('description') }}</textarea>
        </div>

        <div>
            <label>Anh bia</label>
            <input type="file" name="cover_image">
        </div>

        <div>
            <label>Logo</label>
            <input type="file" name="logo">
        </div>

        <div>
            <label>Gio mo cua</label>
            <input type="time" name="open_time" value="{{ old('open_time') }}">
        </div>

        <div>
            <label>Gio dong cua</label>
            <input type="time" name="close_time" value="{{ old('close_time') }}">
        </div>

        <div>
            <label>Trang thai</label>
            <select name="status">
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Cho duyet</option>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoat dong</option>
            </select>
        </div>

        <button type="submit">Dang ky</button>
    </form>
</body>
</html>
