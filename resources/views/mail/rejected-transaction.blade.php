<div>
    <!-- Simplicity is the essence of happiness. - Cedric Bledsoe -->
    <h2>Halo, {{ $transaction->user->first_name }}</h2>

    <p>Pemesanan kamu kami tolak!</p>

    <p>Alasan kami menolaknya adalah : {{ $transaction->cancelled_reason }}</p>

    <a href="{{ route('user.detail-order', ['id' => $transaction->id]) }}">Lihat Pemesanan Disini!</a>

</div>
