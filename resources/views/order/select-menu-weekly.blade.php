@include('components.header')
@if(isset($response['status']) && $response['status'] === 'success')
    @php
        $data = $response['data'];
        $deliveryDate = $data['date'];
        $menus = $data['items'];
    @endphp

    <div class="container-fluid py-4">
        <div class="mb-5">
            <h2 class="fw-bold text-dark mb-1">Pilih Menu Mingguan</h2>
            <p class="text-muted">Jadwal Pengiriman: <span class="fw-bold text-primary">{{ \Carbon\Carbon::parse($deliveryDate)->translatedFormat('l, d F Y') }}</span></p>
        </div>

        <form action="{{ route('user.checkout') }}" method="POST" id="formWeeklyCheckout">
            @csrf
            <input type="hidden" name="checkout_payload" id="checkout_payload">
            
            <div class="row g-5">
                <div class="col-lg-8">
                    @foreach($menus as $menuIndex => $menu)
                        <div class="card border-0 shadow-sm mb-4 menu-card-container" data-menu-id="{{ $menu['id'] }}">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <img src="{{ $menu['image_url'] }}" alt="{{ $menu['name'] }}" class="rounded shadow-sm me-4" style="width: 100px; height: 100px; object-fit: cover;">
                                    <div>
                                        <div class="badge bg-light-primary text-primary mb-1">{{ $menu['theme'] }}</div>
                                        <h4 class="fw-bold mb-1">{{ $menu['name'] }}</h4>
                                        <p class="text-muted small mb-2">{{ $menu['description'] }}</p>
                                        <div class="text-muted small">
                                            <strong>Addons:</strong> 
                                            {{ $menu['addon']['vegetable'] ?? '' }}, 
                                            {{ $menu['addon']['side_dish'] ?? '' }}, 
                                            {{ $menu['addon']['sauce'] ?? '' }}
                                        </div>
                                    </div>
                                </div>

                                <hr class="text-muted opacity-25">

                                <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-primary me-2"></i>Pilihan Paket Tersedia</h6>
                                
                                <div class="row g-3">
                                    @foreach($menu['packages'] as $package)
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100 bg-hover-light">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="fw-bold">{{ $package['name'] }}</span>
                                                    <span class="fw-bold text-success">Rp {{ number_format($package['price'], 0, ',', '.') }}</span>
                                                </div>
                                                <p class="text-muted small mb-3" style="min-height: 40px;">{{ $package['description'] }}</p>
                                                
                                                <div class="package-input-group" data-package-id="{{ $package['id'] }}" data-package-price="{{ $package['price'] }}">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Jumlah Pesanan <span class="text-danger">*</span></label>
                                                        <div class="input-group input-group-sm">
                                                            <button class="btn btn-icon btn-light-danger px-3 btn-minus" type="button"><i class="bi bi-dash"></i></button>
                                                            <input type="number" class="form-control text-center qty-input" value="0" min="0" data-min-order="{{ $package['minimum_order'] }}" readonly>
                                                            <button class="btn btn-icon btn-light-success px-3 btn-plus" type="button"><i class="bi bi-plus"></i></button>
                                                        </div>
                                                        <div class="form-text text-danger small" style="font-size: 0.7rem;">Minimum Order: {{ $package['minimum_order'] }} porsi</div>
                                                    </div>

                                                    <div>
                                                        <label class="form-label small fw-bold">Catatan (Opsional)</label>
                                                        <input type="text" class="form-control form-control-sm note-input" placeholder="Cth: Sambal dipisah">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-header bg-white py-4 border-bottom-0">
                            <h4 class="mb-0 fw-bold">Ringkasan Pesanan</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Item Terpilih</span>
                                <span class="fw-bold" id="summaryTotalItems">0 Paket</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Estimasi Subtotal</span>
                                <span class="fw-bold text-danger fs-5" id="summaryTotalPrice">Rp 0</span>
                            </div>

                            <button type="button" id="btnProcessCheckout" class="btn btn-primary w-100 py-3 fw-bold fs-6">
                                Lanjutkan Checkout <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnProcess = document.getElementById('btnProcessCheckout');
        const form = document.getElementById('formWeeklyCheckout');
        
        // Raw Date dari response backend
        const deliveryAtRaw = "{{ $deliveryDate ?? '' }}";

        // Fungsi Format Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        };

        // Fungsi Update Ringkasan (Kanan)
        const updateSummary = () => {
            let totalItems = 0;
            let totalPrice = 0;

            document.querySelectorAll('.package-input-group').forEach(group => {
                const qty = parseInt(group.querySelector('.qty-input').value) || 0;
                const price = parseFloat(group.dataset.packagePrice) || 0;

                if (qty > 0) {
                    totalItems += qty;
                    totalPrice += (qty * price);
                }
            });

            document.getElementById('summaryTotalItems').innerText = totalItems + ' Porsi';
            document.getElementById('summaryTotalPrice').innerText = formatRupiah(totalPrice);
        };

        // Event Listener Tombol Plus (FIXED)
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Pastikan selalu target ke <button>, bukan <i>
                const button = e.currentTarget; 
                // Cari bungkusnya, lalu temukan input di dalamnya
                const input = button.closest('.input-group').querySelector('.qty-input');
                const minOrder = parseInt(input.dataset.minOrder) || 1;
                let val = parseInt(input.value) || 0;

                if (val === 0) {
                    val = minOrder;
                } else {
                    val += 1;
                }
                
                input.value = val;
                updateSummary();
            });
        });

        // Event Listener Tombol Minus (FIXED)
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const button = e.currentTarget;
                const input = button.closest('.input-group').querySelector('.qty-input');
                const minOrder = parseInt(input.dataset.minOrder) || 1;
                let val = parseInt(input.value) || 0;

                if (val > 0) {
                    val -= 1;
                    if (val < minOrder) {
                        val = 0;
                    }
                }
                
                input.value = val;
                updateSummary();
            });
        });

        // Proses Build JSON saat Checkout diklik
        btnProcess.addEventListener('click', function() {
            let finalData = {
                items: [],
                delivery_at: deliveryAtRaw 
            };

            let hasOrder = false;

            document.querySelectorAll('.menu-card-container').forEach(menuCard => {
                const menuId = menuCard.dataset.menuId;
                let packagesArr = [];

                menuCard.querySelectorAll('.package-input-group').forEach(group => {
                    const qty = parseInt(group.querySelector('.qty-input').value) || 0;
                    const note = group.querySelector('.note-input').value.trim();
                    const pkgId = group.dataset.packageId;

                    if (qty > 0) {
                        hasOrder = true;
                        let pkgData = {
                            id: pkgId,
                            quantity: qty
                        };
                        
                        if (note !== "") {
                            pkgData.note = note;
                        }

                        packagesArr.push(pkgData);
                    }
                });

                if (packagesArr.length > 0) {
                    finalData.items.push({
                        id: menuId,
                        packages: packagesArr
                    });
                }
            });

            if (!hasOrder) {
                alert('Pilih minimal 1 paket menu sebelum melanjutkan checkout!');
                return;
            }

            document.getElementById('checkout_payload').value = JSON.stringify(finalData);
            form.submit();
        });
    });
</script>
@else
    <div class="alert alert-danger m-4">Gagal memuat data menu mingguan.</div>
@endif

<div class="m-100">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>