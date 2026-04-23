{{-- ===================================================
     Toast Notification Partial
     Include: @include('layouts.partials.toast')
     ================================================== --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">

    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body fw-medium">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body fw-medium">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="toast align-items-center text-bg-warning border-0 show mb-2" role="alert" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body fw-medium text-dark">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="toast align-items-center text-bg-info border-0 show mb-2" role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body fw-medium text-dark">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="toast align-items-center border-0 show mb-2" role="alert"
         style="background: #dc3545; color:#fff" data-bs-autohide="true" data-bs-delay="6000">
        <div class="d-flex">
            <div class="toast-body fw-medium">
                <i class="fas fa-times-circle me-2"></i>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-1 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto align-self-start mt-2" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

</div>

<script>
    // Auto-init tất cả toast trong container
    document.addEventListener('DOMContentLoaded', function () {
        const toastEls = document.querySelectorAll('#toast-container .toast');
        toastEls.forEach(function (el) {
            const t = new bootstrap.Toast(el);
            t.show();
        });
    });
</script>
