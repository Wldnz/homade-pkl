@include('components.header')
<div class="container py-4">
    <a href="{{ route('admin.payment-methods') }}" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="bi bi-arrow-left"></i> Back</a>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Tambahkan Metode Pembayaran</h5>
            <button type="submit" form="formStore" class="btn btn-success btn-sm fw-bold">Tambahkan</button>
        </div>
        <div class="card-body p-4">
            <form id="formStore" action="{{ route('admin.create-payment-method') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div class="border rounded bg-light d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                            <span class="text-muted small"><< Gambar >></span>
                        </div>
                        <input type="file" name="image" class="form-control form-control-sm mb-3" required accept="image/*">
                        <div class="form-check form-switch d-flex justify-content-center gap-2">
                            <label class="form-check-label fw-bold" for="statusSwitch">Status</label>
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="is_active" value="1" checked>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Bank / E-Wallet</label>
                            <input type="text" name="bank_name" class="form-control" required placeholder="Contoh: Bank BCA">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Pemilik Rekening</label>
                            <input type="text" name="account_owner" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor Rekening</label>
                            <input type="number" name="account_number" class="form-control" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('components.navbarFoot', ["page" => "payment"])