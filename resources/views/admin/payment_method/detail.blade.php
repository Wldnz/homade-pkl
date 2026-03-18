@include('components.header')
<div class="container py-4">
    <a href="{{ route('admin.payment-methods') }}" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="bi bi-arrow-left"></i> Back</a>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Detail - {{ $response['data']->bank_name }}</h5>
            <button type="submit" form="formEdit" class="btn btn-success btn-sm fw-bold">Simpan Perubahan</button>
        </div>
        <div class="card-body p-4">
            <form id="formEdit" action="{{ route('admin.edit-payment-method', $response['data']->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('put')
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div class="border rounded bg-white p-2 mb-2 d-flex justify-content-center">
                            @if($response['data']->image_url)
                                <img src="{{ $response['data']->image_url }}" class="img-fluid rounded" style="max-height: 130px;">
                            @else
                                <span class="text-muted small my-5">Tidak ada gambar</span>
                            @endif
                        </div>
                        <input type="file" name="image" class="form-control form-control-sm mb-3" accept="image/*">
                        
                        <div class="form-check form-switch d-flex justify-content-center gap-2 mb-3">
                            <label class="form-check-label fw-bold" for="statusSwitch">Status</label>
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="is_active" value="1" {{ $response['data']->is_active ? 'checked' : '' }}>
                        </div>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold" onclick="if(confirm('Yakin hapus data ini?')) document.getElementById('formDelete').submit();">Hapus</button>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Bank / E-Wallet</label>
                            <input type="text" name="bank_name" class="form-control" required value="{{ $response['data']->bank_name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Pemilik Rekening</label>
                            <input type="text" name="account_owner" class="form-control" required value="{{ $response['data']->account_owner }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor Rekening</label>
                            <input type="number" name="account_number" class="form-control" required value="{{ $response['data']->account_number }}">
                        </div>
                    </div>
                </div>
            </form>
            <form id="formDelete" action="{{ route('admin.delete-payment-method', $response['data']->id) }}" method="POST" class="d-none">
                @csrf @method('delete')
            </form>
        </div>
    </div>
</div>
@include('components.navbarFoot', ["page" => "payment"])
<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>