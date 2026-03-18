@include('components.header')
@if ($response['status'] === 'success')
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Manajemen Pengaturan</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.edit-setting') }}" method="POST">
                @csrf
                @method('put')

                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Informasi Utama & Operasional</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Aplikasi <span class="text-danger">*</span></label>
                        <input type="text" name="app_name" class="form-control"
                            value="{{ old('app_name', $response['data']['app_name'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email Support <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $response['data']['email'] ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Hari Operasional <span class="text-danger">*</span></label>
                        <input type="text" name="operating_days_info" class="form-control"
                            value="{{ old('operating_days_info', $response['data']['operating_days_info'] ?? '') }}"
                            placeholder="Contoh: Senin - Sabtu">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jam Buka <span class="text-danger">*</span></label>
                        <input type="time" name="open_hours_at" class="form-control"
                            value="{{ old('open_hours_at', $response['data']['open_hours_at'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jam Tutup <span class="text-danger">*</span></label>
                        <input type="time" name="close_hours_at" class="form-control"
                            value="{{ old('close_hours_at', $response['data']['close_hours_at'] ?? '') }}">
                    </div>
                </div>

                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Kontak, Alamat & Titik Lokasi</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">No. Customer Care <span class="text-danger">*</span></label>
                        <input type="text" name="customer_care_phone" class="form-control"
                            value="{{ old('customer_care_phone', $response['data']['customer_care_phone'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Longitude (Bujur)</label>
                        <input type="text" name="longitude" class="form-control"
                            value="{{ old('longitude', $response['data']['longitude'] ?? '') }}"
                            placeholder="Contoh: 106.8574">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Latitude (Lintang)</label>
                        <input type="text" name="latitude" class="form-control"
                            value="{{ old('latitude', $response['data']['latitude'] ?? '') }}"
                            placeholder="Contoh: -6.2305">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Ongkir per KM (Rp)</label>
                        <input type="number" name="shipping_fee_per_km" class="form-control"
                            value="{{ old('shipping_fee_per_km', $response['data']['shipping_fee_per_km'] ?? 0) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control"
                            rows="3">{{ old('address', $response['data']['address'] ?? '') }}</textarea>
                    </div>
                </div>

                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Sosial Media (Opsional)</h6>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><i class="bi bi-instagram text-danger me-1"></i> Instagram
                            URL</label>
                        <input type="url" name="instagram_url" class="form-control"
                            value="{{ old('instagram_url', $response['data']['instagram_url'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><i class="bi bi-tiktok text-dark me-1"></i> TikTok
                            URL</label>
                        <input type="url" name="tiktok_url" class="form-control"
                            value="{{ old('tiktok_url', $response['data']['tiktok_url'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><i class="bi bi-youtube text-danger me-1"></i> YouTube
                            URL</label>
                        <input type="url" name="youtube_url" class="form-control"
                            value="{{ old('youtube_url', $response['data']['youtube_url'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><i class="bi bi-facebook text-primary me-1"></i> Facebook
                            URL</label>
                        <input type="url" name="facebook_url" class="form-control"
                            value="{{ old('facebook_url', $response['data']['facebook_url'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><i class="bi bi-twitter-x text-dark me-1"></i> X (Twitter)
                            URL</label>
                        <input type="url" name="x_url" class="form-control"
                            value="{{ old('x_url', $response['data']['x_url'] ?? '') }}">
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="form-check form-switch p-3 bg-light rounded border border-primary border-opacity-25">
                        <input class="form-check-input ms-0 me-3 fs-5" type="checkbox" id="orderingActive"
                            name="is_ordering_active" value="1" {{ ($response['data']['is_ordering_active'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold mt-1" for="orderingActive">Aktifkan Fitur Pemesanan
                            (Checkout)</label>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endif
@include('components.navbarFoot', ["page" => "profile"])
<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>