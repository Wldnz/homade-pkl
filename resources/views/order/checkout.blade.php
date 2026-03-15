@include('components.header')

@php
    // Asumsi data dari controller
    $transactionData = $response['data'];
    $userInfo = $transactionData['user_info'];
    $deliveryInfo = $transactionData['delivery_info'];
    $addresses = $deliveryInfo['user_address'] ?? [];
    $summaryItems = $transactionData['summary_orders']['items'];
@endphp

<div class="container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-1">Detail Pengiriman & Pembayaran</h2>
        <p class="text-muted">Lengkapi data di bawah ini untuk menyelesaikan pesanan Anda.</p>
    </div>

    <form action="{{ route('user.create-order') }}" method="POST" id="formCreateTransaction">
        @csrf
        <input type="hidden" name="checkout_payload" id="checkout_payload">

        <div class="row g-5">
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Data Pemesan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nama Depan</label>
                                <input type="text" class="form-control" id="u_first_name"
                                    value="{{ $userInfo['first_name'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nama Belakang</label>
                                <input type="text" class="form-control" id="u_last_name"
                                    value="{{ $userInfo['last_name'] }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Nomor WhatsApp / Telepon</label>
                                <input type="text" class="form-control" id="u_phone" value="{{ $userInfo['phone'] }}">
                                <div class="form-text small">Nomor ini bisa Anda ubah jika berbeda dengan profil utama.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Alamat Pengiriman
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($addresses) > 0)
                            <div class="mb-4" id="existingAddressContainer">
                                <label class="form-label small fw-bold mb-3">Pilih Alamat Tersedia</label>
                                @foreach($addresses as $index => $addr)
                                    <div class="form-check p-3 border rounded mb-2 bg-hover-light cursor-pointer">
                                        <input class="form-check-input ms-0 me-3 address-radio" type="radio"
                                            name="address_selection" id="addr_{{ $addr['id'] }}" value="{{ $addr['id'] }}" {{ $index === 0 ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="addr_{{ $addr['id'] }}"
                                            style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">{{ $addr['label'] }} <span
                                                        class="badge bg-light-primary text-primary ms-1">{{ $addr['is_main'] ? 'Utama' : '' }}</span></span>
                                                <span class="text-muted small">{{ $addr['phone'] }}</span>
                                            </div>
                                            <div class="text-muted small mt-1">{{ $addr['received_name'] }}</div>
                                            <div class="small mt-1">{{ $addr['address'] }}</div>
                                        </label>
                                    </div>
                                @endforeach

                                <div class="form-check p-3 border rounded border-dashed cursor-pointer mt-3">
                                    <input class="form-check-input ms-0 me-3 address-radio" type="radio"
                                        name="address_selection" id="addr_new" value="new">
                                    <label class="form-check-label w-100 fw-bold text-primary" for="addr_new"
                                        style="cursor: pointer;">
                                        <i class="bi bi-plus-circle me-1"></i> Gunakan Alamat Baru
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning small">Anda belum memiliki alamat tersimpan. Silakan isi alamat
                                baru di bawah ini.</div>
                            <input type="hidden" name="address_selection" value="new" class="address-radio">
                        @endif

                        <div id="newAddressForm"
                            class="{{ count($addresses) > 0 ? 'd-none' : '' }} p-4 border rounded bg-light">
                            <h6 class="fw-bold mb-3">Detail Alamat Baru</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nama Penerima <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm new-addr-input"
                                        id="na_fullname" placeholder="Contoh: Wildan Izhar">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nomor HP Penerima <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm new-addr-input" id="na_phone"
                                        placeholder="Contoh: 628123456789">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Label Alamat <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm new-addr-input" id="na_label"
                                        placeholder="Contoh: Rumah, Kantor, Kosan">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Alamat Lengkap <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-sm new-addr-input" id="na_address"
                                        rows="2" placeholder="Nama jalan, gedung, RT/RW..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Catatan Patokan (Opsional)</label>
                                    <input type="text" class="form-control form-control-sm new-addr-input" id="na_note"
                                        placeholder="Contoh: Pagar hitam sebelah Indomaret">
                                </div>
                                <div class="col-md-6 d-none">
                                    <input type="text" id="na_lat" value="-6.283818"> <input type="text" id="na_lng"
                                        value="106.703438">
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="na_is_main">
                                        <label class="form-check-label small" for="na_is_main">Jadikan Alamat
                                            Utama</label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="na_save_profile" checked>
                                        <label class="form-check-label small" for="na_save_profile">Simpan alamat ini ke
                                            profil saya</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label class="form-label fw-bold"><i class="bi bi-chat-text text-primary me-2"></i>Catatan
                            Tambahan Transaksi (Opsional)</label>
                        <textarea class="form-control" id="t_note" rows="2"
                            placeholder="Contoh: Tolong antar sebelum jam 12 siang ya..."></textarea>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="mb-0 fw-bold">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-4" id="orderItemsContainer">
                            @foreach($summaryItems as $item)
                                <div class="mb-3 pb-3 border-bottom item-data-row" data-menu-id="{{ $item['id'] }}">
                                    <div class="fw-bold small mb-1">{{ $item['name'] }}</div>
                                    @foreach($item['packages'] as $pkg)
                                        <div class="d-flex justify-content-between align-items-center mb-1 package-data-row"
                                            data-package-id="{{ $pkg['id'] }}" data-qty="{{ $pkg['quantity'] }}"
                                            data-note="{{ $pkg['note'] }}">
                                            <div class="small text-muted">
                                                {{ $pkg['quantity'] }}x {{ $pkg['name'] }}
                                            </div>
                                            <div class="small fw-bold">Rp
                                                {{ number_format($pkg['price'] * $pkg['quantity'], 0, ',', '.') }}</div>
                                        </div>
                                        @if($pkg['note'])
                                            <div class="small text-muted fst-italic ms-3" style="font-size: 0.75rem;">Catatan:
                                                {{ $pkg['note'] }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Subtotal ({{ $transactionData['transaction']['total_item'] }}
                                Item)</span>
                            <span class="fw-bold">Rp
                                {{ number_format($transactionData['transaction']['sub_total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Biaya Pengiriman</span>
                            <span
                                class="fw-bold text-success">{{ $transactionData['transaction']['shipping_cost'] == 0 ? 'Gratis' : 'Rp ' . number_format($transactionData['transaction']['shipping_cost'], 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5">Total Bayar</span>
                            <span class="fw-bold fs-4 text-danger">Rp
                                {{ number_format($transactionData['transaction']['sub_total'] + $transactionData['transaction']['shipping_cost'], 0, ',', '.') }}</span>
                        </div>

                        <button type="button" id="btnSubmitTransaction"
                            class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow-sm">
                            Buat Pesanan Sekarang <i class="bi bi-check-circle ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addressRadios = document.querySelectorAll('.address-radio');
        const newAddressForm = document.getElementById('newAddressForm');
        const btnSubmit = document.getElementById('btnSubmitTransaction');
        const form = document.getElementById('formCreateTransaction');

        // Delivery At bawaan dari halaman sebelumnya
        const deliveryAtRaw = "{{ $deliveryInfo['delivery_at'] }}";

        // Toggle Form Alamat Baru
        addressRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'new') {
                    newAddressForm.classList.remove('d-none');
                } else {
                    newAddressForm.classList.add('d-none');
                }
            });
        });

        // Kumpulkan Data dan Submit
        btnSubmit.addEventListener('click', function () {
            // 1. Ambil Mode Alamat
            let selectedAddressRadio = document.querySelector('.address-radio:checked');
            let addressMode = selectedAddressRadio ? selectedAddressRadio.value : 'new';

            // 2. Siapkan Kerangka JSON
            let payload = {
                items: [],
                delivery_info: {
                    delivery_at: deliveryAtRaw
                },
                user_info: {
                    first_name: document.getElementById('u_first_name').value.trim(),
                    last_name: document.getElementById('u_last_name').value.trim(),
                    phone: document.getElementById('u_phone').value.trim()
                },
                note: document.getElementById('t_note').value.trim()
            };

            // 3. Isi Delivery Info sesuai mode alamat
            if (addressMode === 'new') {
                // Validasi basic alamat baru
                let na_fullname = document.getElementById('na_fullname').value.trim();
                let na_phone = document.getElementById('na_phone').value.trim();
                let na_label = document.getElementById('na_label').value.trim();
                let na_address = document.getElementById('na_address').value.trim();

                if (!na_fullname || !na_phone || !na_label || !na_address) {
                    alert('Harap lengkapi semua field bertanda (*) pada Alamat Baru!');
                    return;
                }

                payload.delivery_info.new_user_address = {
                    fullname: na_fullname,
                    phone: na_phone,
                    label: na_label,
                    address: na_address,
                    note: document.getElementById('na_note').value.trim(),
                    longitude: document.getElementById('na_lng').value,
                    latitude: document.getElementById('na_lat').value,
                    is_main_address: document.getElementById('na_is_main').checked,
                    save_to_profile: document.getElementById('na_save_profile').checked
                };
            } else {
                payload.delivery_info.user_address_id = addressMode;
            }

            // 4. Rekonstruksi Items Array dari DOM
            document.querySelectorAll('.item-data-row').forEach(menuRow => {
                let menuId = menuRow.dataset.menuId;
                let packagesArr = [];

                menuRow.querySelectorAll('.package-data-row').forEach(pkgRow => {
                    let pkgData = {
                        id: pkgRow.dataset.packageId,
                        quantity: parseInt(pkgRow.dataset.qty)
                    };

                    let pNote = pkgRow.dataset.note;
                    if (pNote && pNote !== "") {
                        pkgData.note = pNote;
                    }
                    packagesArr.push(pkgData);
                });

                if (packagesArr.length > 0) {
                    payload.items.push({
                        id: menuId,
                        packages: packagesArr
                    });
                }
            });

            // 5. Masukkan ke Hidden Input & Submit
            document.getElementById('checkout_payload').value = JSON.stringify(payload);
            console.log("Data Payload:", payload); // Buat testing lo di inspect element
            form.submit();
        });
    });
</script>



<div class="m-100">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>