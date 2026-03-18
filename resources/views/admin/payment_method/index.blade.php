@include('components.header')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Metode Pembayaran <span class="badge bg-light text-dark ms-2">Total ({{ count($response['data']['payments']) }})</span></h5>
            <a href="{{ route('admin.create-payment-method-page') }}" class="btn btn-primary btn-sm fw-bold"><i class="bi bi-plus"></i> Tambahkan Metode</a>
        </div>
        <div class="card-body">
            @foreach($response['data']['payments'] as $payment)
            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3 bg-light">
                <div class="d-flex gap-3 align-items-center">
                    <img src="{{ $payment->image_url ?? asset('assets/default-bank.png') }}" class="rounded bg-white border" style="width: 60px; height: 60px; object-fit: contain; padding: 5px;">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $payment['bank_name'] }}</h6>
                        <p class="mb-0 small text-muted">{{ $payment['account_owner'] }}</p>
                        <p class="mb-0 small fw-bold text-primary">{{ $payment['account_number'] }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-check form-switch me-3">
                        <input class="form-check-input" type="checkbox" disabled {{ $payment['is_active'] ? 'checked' : '' }}>
                    </div>
                    <a href="{{ route('admin.detail-payment-method', $payment['id']) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                    <form action="{{ route('admin.delete-payment-method', $payment['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                        @csrf @method('delete')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
            <div class="mt-3">
            </div>
        </div>
    </div>
</div>
@include('components.navbarFoot', ["page" => "payment"])
<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>