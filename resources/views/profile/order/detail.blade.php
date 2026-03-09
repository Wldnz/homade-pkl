<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
    @if ($response['status'] === 'success')
            <div style="display:flex; flex-direction: column; gap:10px;"></div>
            <div style="display:flex; flex-direction: column; gap:5px;"></div>
            <h2>Data Customer</h2>
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Nama Pembeli: {{ $response['data']['user']->first_name . ' ' . $response['data']['user']->last_name }}
                </span>
                <span>Nomor Telepon Pembeli: {{ $response['data']['user']->phone }} </span>
                <span>Alamat Email Pembeli: {{ $response['data']['user']->email }} </span>
                <span>Tanggal Dibuatnya Akun: {{ $response['data']['user']->created_at }}</span>
                <a href="{{ route('admin.detail-account', ['id' => $response['data']['user']->id]) }}">Lihat Akun</a>
            </div>
        </div>
        <div style="display:flex; flex-direction: column; gap:5px;"></div>
        <h2>Informasi Pengiriman</h2>
        <div style="display:flex; flex-direction: column; gap:2px;">
            <span>Dikirimkan Pada: {{ $response['data']['delivery_info']['delivery_at'] }}</span>
            <span>Estimasi Jarak: {{ $response['data']['delivery_info']['distance'] }} Kilometer</span>
            <span>Status: {{ $response['data']['delivery_info']['status'] }}</span>
            <div style="display:flex; flex-direction: column; gap:5px;"></div>
            <h4>Data Alamat Customer</h4>
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Nama Penerima: {{ $response['data']['delivery_info']['address_info']['received_name'] }}</span>
                <span>Nomor Telepon: {{ $response['data']['delivery_info']['address_info']['phone']   }} </span>
                <span>Alamat: {{ $response['data']['delivery_info']['address_info']['address']   }} </span>
                <span>Catatan: {{ $response['data']['delivery_info']['address_info']['note']   }} </span>
            </div>
            <iframe width="100%" height="300" frameborder="0" style="border:0"
                src="https://www.google.com/maps?q={{ $response['data']['delivery_info']['address_info']['latitude'] }},{{ $response['data']['delivery_info']['address_info']['longitude'] }}&output=embed"
                allowfullscreen>
            </iframe>
        </div>
        </div>
        </div>
        <div style="display:flex; flex-direction: column; gap:5px;"></div>
        <h2>Data Transaksi</h2>
        <div style="display:flex; flex-direction: column; gap:2px;">
            <span>Transaksi ID: {{ $response['data']['id'] }}</span>
            <span>Total menu yang dipesan : {{ $response['data']['total_menu'] }}</span>
            <span>Sub Total : {{ $response['data']['subtotal'] }}</span>
            <span>Ongkos Kirim : {{ $response['data']['shipping_cost'] }}</span>
            <span>Total Price : {{ $response['data']['total_price'] }}</span>
            <span>Status : {{ $response['data']['status'] }}</span>
            <span>Kategori : {{ $response['data']['category'] }}</span>
            <span>Catatan: {{ $response['data']['note'] ?? '-' }} </span>
            <span>Dibuat Pada: {{ $response['data']['created_at'] }} </span>
        </div>
        </div>
        @if ($response['data']['refund_info']['is_refund'])
            <div style="display:flex; flex-direction: column; gap:5px;"></div>
            <h2>Data Refund</h2>
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Alasan: {{ $response['data']['refund_info']['reason'] }}</span>
                <span>Status: {{ $response['data']['refund_info']['status'] }}</span>
            </div>
            </div>
        @endif
        @if (str_starts_with($response['data']['status'], 'cancelled_by'))
            <div style="display:flex; flex-direction: column; gap:5px;"></div>
            <h2>Data Dibatalkan</h2>
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Alasan: {{ $response['data']['cancelled_reason'] }}</span>
                <span>Di Cancel Oleh: {{ $response['data']['status'] === 'cancelled_by_admin' ? 'admin' : 'customer' }}</span>
            </div>
            </div>
        @endif
        <div style="display:flex; flex-direction: column; gap:5px;"></div>
        <h2>List Pemesanan</h2>
        @foreach ($response['data']['items'] as $item)
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Nama Menu: {{ $item['name'] }}</span>
                <span>Tema: {{ $item['theme'] }}</span>
                <span>Paket: {{ $item['package'] }}</span>
                <span>Jumlah Di Pesan: {{ $item['quantity'] }}</span>
                <span>Harga: {{ $item['price_at_purchase'] }}</span>
            </div>
        @endforeach
        </div>
        @if ($response['data']['payment_proof'])
            <div style="display:flex; flex-direction: column; gap:5px;"></div>
            <h2>Data Bukti Pembayaran</h2>
            <div style="display:flex; flex-direction: column; gap:2px;">
                <span>Payment Proof ID: {{ $response['data']['payment_proof']['id'] }}</span>
                <span>Bukti Pembayaran Dibawha ini</span>
                <img src="{{ $response['data']['payment_proof']['url'] }}" alt="bukt-pembayaram">
                <span>Status : {{ $response['data']['payment_proof']['status'] }}</span>
                <span>Alasan : {{ $response['data']['payment_proof']['reason'] }}</span>
                {{-- uploud ulang bukti pemabyaran --}}
                @if ($response['data']['payment_proof']['status'] === 'rejected')
                    <h4>Menguploud Ulang Bukti Pembayaran</h4>
                    <form action="{{ route('user.uploud-payment-proof', ['id' => $response['data']['id']]) }}" method="post"
                        style="display:flex; flex-direction: column; gap:10px;" enctype="multipart/form-data">
                        @csrf
                        <span>Mohon menguploud ulang bukti pembayaran</span>
                        <input type="file" name="uplouded_file" accept="image/*">
                        <button>Uploud Ulang Bukti Pembayarran</button>
                    </form>
                @endif
            </div>
            </div>
        @elseif(!$response['data']['payment_proof'])
            <h2>Tambahkan Bukti Pembayaran</h2>
            <form action="{{ route('user.uploud-payment-proof', ['id' => $response['data']['id']]) }}" method="post"
                style="display:flex; flex-direction: column; gap:10px;" enctype="multipart/form-data">
                @csrf
                <span>Tambahkan Bukti Pembayaran</span>
                <input type="file" name="uplouded_file" accept="image/*">
                <button>Tambhakan Bukti pembayaran</button>
            </form>
        @endif
        <div style="display:flex; flex-direction: column; gap:2px;">
            <form action="{{ route('user.cancell-order', ['id' => $response['data']['id']]) }}" method="post"
                style="display:flex; flex-direction: column; gap:10px;" enctype="multipart/form-data">
                @csrf
                <span>apa alasan anda ingin membtalkan pemesanan</span>
                <textarea name="reason" id="">
                    saya mengalami kendala mematikan bernama labubuu
                </textarea>
                <button>Batalkan transaksi</button>
            </form>
        </div>
        </div>
    @endif

<div style="margin-top: 200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>

</div>