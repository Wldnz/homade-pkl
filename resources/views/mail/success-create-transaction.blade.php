<div>
    <!-- Be present above all else. - Naval Ravikant -->
    <h2>Berhasil membuat transaksi!</h2>
    @if ($transaction->is_guest)
        <a href="{{ route('guest.detail-order', [
            'id' => $transaction->id,
            'token' => $transaction->access_token,
            // 'email' => $transaction->contact_email 
        ]) }}">
            Lihat Pemesanan
        </a>
    @else
        <a href="{{ route('user.detail-order', ['id' => $transaction->id]) }}">
            Lihat Pemesanan
        </a>
    @endif
</div>