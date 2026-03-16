@include('components.header')


@if ($response['status'] === 'success')
    @php
        $trx = $response['data'] ?? null;
    @endphp

    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 text-center">

                @if($trx)
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                    </div>

                    <h1 class="fw-bold text-dark mb-3">Hore! Pesanan Berhasil Dibuat 🎉</h1>
                    <p class="text-muted fs-5 mb-5">Terima kasih, pesanan katering Anda sudah kami terima dan sedang menunggu
                        proses pembayaran.</p>

                    <div class="card border-0 shadow-sm text-start mb-5">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <div>
                                    <div class="text-muted small mb-1">ID Transaksi</div>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                        #{{ strtoupper(substr($trx['id'], 0, 8)) }}...
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small mb-1">Total Tagihan</div>
                                    <div class="fw-bold text-danger fs-4">Rp
                                        {{ number_format($trx['total_price'], 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-6">
                                    <div class="text-muted small">Jadwal Pengiriman</div>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($trx['delivery_at'])->translatedFormat('d F Y') }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Waktu Pemesanan</div>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($trx['created_at'])->translatedFormat('H:i WIB') }}</div>
                                </div>
                                <div class="col-6 mt-3">
                                    <div class="text-muted small">Total Item</div>
                                    <div class="fw-bold">{{ $trx['total_items'] }} Porsi</div>
                                </div>
                                <div class="col-6 mt-3">
                                    <div class="text-muted small">Kategori</div>
                                    <div class="fw-bold text-capitalize">{{ $trx['category'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('user.detail-order', ['id' => $trx['id']]) }}"
                            class="btn btn-primary btn-lg fw-bold px-5 shadow-sm">
                            Lanjut Pembayaran / Detail <i class="bi bi-arrow-right-circle ms-2"></i>
                        </a>

                        <a href="{{ route('user.home') }}" class="btn btn-light btn-lg fw-bold px-5 text-dark border shadow-sm">
                            <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
                        </a>
                    </div>

                @else
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3">Sesi Transaksi Habis</h3>
                    <p class="text-muted mb-4">Data pesanan sudah tidak tersedia atau Anda me-refresh halaman.</p>
                    <a href="{{ route('user.home') }}" class="btn btn-primary px-4">Kembali ke Beranda</a>
                @endif

            </div>
        </div>
    </div>
@endif

<div class="m-100 d-none">
    @if (session()->has('response'))
        {{ dump(session()->get('response')) }}
    @endif
</div>