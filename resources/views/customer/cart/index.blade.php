@extends('layouts.auth')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="flex items-center mb-6">
            <a href="/" class="mr-4 text-gray-600 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Giỏ hàng & Thanh toán</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-4">
                        <h2 class="font-bold text-lg">Phở Hà Nội Truyền Thống</h2>
                        <a href="#" class="text-gray-500 text-sm hover:underline">+ Thêm món</a>
                    </div>

                    <div class="space-y-6">
                        @foreach($cartItems as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-16 h-16 rounded-lg object-cover bg-gray-200">
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                    <div class="flex items-center mt-2 border rounded-lg w-fit bg-gray-50">
                                        <button class="px-3 py-1 text-gray-500 hover:text-orange-500">-</button>
                                        <span class="px-3 font-medium">{{ $item->quantity }}</span>
                                        <button class="px-3 py-1 text-gray-500 hover:text-orange-500">+</button>
                                    </div>
                                </div>
                            </div>
                            <span class="font-bold text-orange-600">{{ number_format($item->product->price * $item->quantity) }}đ</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-orange-500">🎟️</span>
                        <h2 class="font-bold text-gray-800">Mã giảm giá</h2>
                    </div>
                    <div class="flex space-x-3">
                        <input type="text" placeholder="Thử FOODHUB20 hoặc NEWUSER" class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <button class="bg-orange-500 text-white px-6 py-2 rounded-lg font-bold hover:bg-orange-600">Áp dụng</button>
                    </div>
                    <div class="mt-3 p-2 bg-green-50 rounded-lg flex items-center text-green-700 text-sm">
                        <span class="mr-2">✅</span> Đơn từ 100k - Miễn phí giao hàng!
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-4">Chi tiết thanh toán</h2>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Tạm tính ({{ $cartItems->count() }} món)</span>
                            <span>{{ number_format($subtotal) }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí giao hàng</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                        <div class="flex justify-between pt-4 border-t items-center">
                            <span class="text-lg font-bold text-gray-800">Tổng cộng</span>
                            <span class="text-2xl font-bold text-orange-600">{{ number_format($subtotal) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-xl shadow-sm p-6 border-2 border-orange-500 relative">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-orange-500">📍</span>
                        <h2 class="font-bold text-gray-800">Địa chỉ giao hàng</h2>
                    </div>
                    <div class="text-sm space-y-1">
                        <p class="font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-gray-500">{{ Auth::user()->phone ?? '0901234567' }}</p>
                        <p class="text-gray-500 leading-relaxed">{{ Auth::user()->address ?? 'Vui lòng cập nhật địa chỉ' }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 bg-red-50 text-red-500 text-xs rounded border border-red-100">Mặc định</span>
                    </div>
                    <button class="w-full mt-4 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50">+ Thêm địa chỉ mới</button>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-3">Ghi chú cho người bán</h2>
                    <textarea placeholder="Ví dụ: Không cay, không rau mùi..." class="w-full bg-gray-50 border border-gray-100 rounded-lg p-3 text-sm focus:outline-none" rows="3"></textarea>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-4">Phương thức thanh toán</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border-2 border-orange-500 rounded-xl cursor-pointer bg-orange-50">
                            <input type="radio" name="payment" value="cod" checked class="hidden">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">💵</div>
                            <div class="flex-1">
                                <p class="text-sm font-bold">Tiền mặt (COD)</p>
                                <p class="text-xs text-gray-500">Thanh toán khi nhận hàng</p>
                            </div>
                            <span class="text-orange-500 font-bold">✓</span>
                        </label>
                        </div>
                </div>

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-orange-500 text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-orange-600 transition duration-300">
                        Xác nhận đặt hàng • {{ number_format($subtotal) }}đ
                    </button>
                </form>
                <p class="text-[10px] text-gray-400 text-center px-4">Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="text-red-400">Điều khoản sử dụng</a> của FoodHub</p>
            </div>
        </div>
    </div>
</div>
@endsection
