<div>
    <!-- Simplicity is the essence of happiness. - Cedric Bledsoe -->
    <h2>Halo, {{ $transaction->user->first_name }}</h2>

    <p>Invoice Terbaru Sudah kami terbitkan ya, segera bayar sekarang!</p>

    <a href="{{ route('user.detail-order', ['id' => $transaction->id]) }}">Lihat Pemesanan Disini!</a>

</div>
