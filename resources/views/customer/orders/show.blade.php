@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-5">
    <div class="mb-3">
        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-1"></i> Trở về danh sách</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">Chi tiết đơn hàng #{{ $order->order_code }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Món</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image ?? false)
                                                @php $productImgUrl = str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image); @endphp
                                                <img src="{{ $productImgUrl }}" alt="{{ $item->product->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-medium">{{ $item->product->name ?? 'Món đã xóa' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                    <td class="text-end fw-medium">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-0">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-4">Tạm tính</td>
                                    <td class="text-end fw-bold pt-4">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold border-0">Tổng cộng</td>
                                    <td class="text-end fw-bold text-brand fs-5 border-0">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($order->status === 'delivered' || $order->status === 'completed')
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Đánh giá đơn hàng</h5>
                    @if(\App\Models\Review::where('order_id', $order->id)->exists())
                        <div class="alert alert-info mb-0">Bạn đã đánh giá đơn hàng này. Cảm ơn bạn!</div>
                    @else
                        <form action="{{ route('customer.reviews.store', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Chất lượng món ăn (1-5 sao)</label>
                                <select name="rating" class="form-select w-auto" required>
                                    <option value="5">5 - Tuyệt vời</option>
                                    <option value="4">4 - Rất tốt</option>
                                    <option value="3">3 - Khá</option>
                                    <option value="2">2 - Tệ</option>
                                    <option value="1">1 - Rất tệ</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Nhận xét của bạn</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận về món ăn..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-brand rounded-pill px-4">Gửi đánh giá</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted mb-3">TRẠNG THÁI</h6>
                    <span class="badge bg-{{ $order->status === 'completed' || $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} fs-6 w-100 py-2">
                        @switch($order->status)
                            @case('pending') Chờ xác nhận @break
                            @case('confirmed') Đã xác nhận @break
                            @case('preparing') Đang chuẩn bị @break
                            @case('delivering') Đang giao @break
                            @case('delivered') Đã giao @break
                            @case('completed') Hoàn thành @break
                            @case('cancelled') Đã hủy @break
                            @default {{ $order->status }}
                        @endswitch
                    </span>
                    <p class="text-muted text-center mt-2 mb-0 small">Đặt lúc: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted mb-3">SHOP</h6>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-shop text-brand fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ $order->shop->name ?? 'N/A' }}</h6>
                            <a href="{{ route('shop.show', $order->shop_id) }}" class="text-decoration-none text-brand small">Xem menu shop</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted mb-3">THÔNG TIN GIAO HÀNG</h6>
                    <p class="mb-2"><strong>Người nhận:</strong> {{ $order->user->customer->full_name ?? ($order->user->email) }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->delivery->customer_phone ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Địa chỉ:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
