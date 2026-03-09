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
            <div style="display:flex; flex-direction: column; gap:5px;">
                @foreach ($response['data']['status_information']['delivery'] as $status_delivery)
                    <form action="{{ route('admin.change-status-delivery-order', ['id' => $response['data']['id']]) }}"
                        method="POST">
                        @csrf
                        @method('put')
                        <button name="delivery_status" value="{{ $status_delivery }}">{{ $status_delivery }}</button>
                    </form>
                @endforeach
            </div>
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
            <form action="{{ route('admin.complete-order', ['id' => $response['data']['id']]) }}" method="post">
                @csrf
                <button>Selesaikan Transaksi</button>
            </form>
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
        <div style="display:flex; flex-direction: column; gap:5px;"></div>
        <h2>Aksi Yang Dapat Dilakukan</h2>
        <div style="display:flex; flex-direction: column; gap:2px;">
            <h4>Invoice Pemesanan</h4>
            @if ($response['data']['status'] === 'waiting_for_invoice' || !$response['data']['payment_proof'] || $response['data']['payment_proof']['status'] === 'rejected')
                    <span>Transaksi Belum Memiliki Invoice</span>
                    {{-- handler untuk mengganti ongkos kirim --}}
                    {{-- handler untuk membatalkan pemesanan berserta dengan alasannya? --}}
                    {{-- btw blm ada nomer rekening ya / atau di email aja? --}}
                    <form action="{{ route('admin.change-shipping-cost', ['id' => $response['data']['id']]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <h4>Yukk, Buatkan Invoice Untuk Customer, Dia nunggu lho!</h4>
                        <div style="display:flex; flex-direction: column; gap:2px;">
                            <label for="shipping_cost">Ongkos Kirim</label>
                            <input type="number" name="shipping_cost" id="shipping_cost"
                                value="{{ old('shipping_cost') ?? $response['data']['shipping_cost'] }}" required>
                            {{-- <textarea name="reason" id="">Amnn</textarea> --}}
                            <button>Tambahkan Invoice</button>
                        </div>
                    </form>
                    <form action="{{ route('admin.reject-order', ['id' => $response['data']['id']]) }}" method="POST">
                        @csrf
                        <textarea name="reason">Tidak memenuhi persyaratan</textarea>
                        <button>Tolak Transaksi</button>
                </div>
                </form>
            @endif
        </div>
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
                {{-- terima bukti pembayaran --}}
                <h4>Action Untuk Menerima / Menolak Bukti Pembayaran</h4>
                {{-- gak ada validasi ya disini, takutnya kepencet acc hehe... tpai kalo status transaksi sudah success itu gak bisa
                di ubah lagi & dan ketika makanannya udh di dikirimkan gak bisa di tolak! --}}
                <form action="{{ route('admin.accept-payment-proof', ['id' => $response['data']['id']]) }}" method="post"
                    style="display:flex; flex-direction: column; gap:10px;" enctype="multipart/form-data">
                    @csrf
                    <span>Ganti Bukti Pembayaran</span>
                    <input type="file" name="uplouded_file" accept="image/*">
                    <textarea name="reason" id="">Bukti pembayaran yang sangat valid!</textarea>
                    <button>Terima Bukti Pembayaran</button>
                </form>
                <form action="{{ route('admin.reject-payment-proof', ['id' => $response['data']['id']]) }}" method="post"
                    style="display:flex; flex-direction: column; gap:2px;">
                    {{-- tolak butki pembayaran --}}
                    @csrf
                    <textarea name="reason" id="">Bukti pembayaran yang sangat tidak valid!</textarea>
                    <button>Tolak Bukti Pembayaran</button>
                </form>
            </div>
            </div>
        @elseif(!$response['data']['payment_proof'] && $response['data']['status'] === 'pending')
            <h2>Tambahkan Bukti Pembayaran</h2>
            <form action="{{ route('admin.uploud-payment-proof', ['id' => $response['data']['id']]) }}" method="post"
                style="display:flex; flex-direction: column; gap:10px;" enctype="multipart/form-data">
                @csrf
                <span>Uploud Bukti Pembayaran</span>
                <input type="file" name="uplouded_file" accept="image/*">
                <button>Uploud Bukti Pembayaran</button>
            </form>
        @endif
        </div>
    @endif

<div style="margin-top: 200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>

</div>