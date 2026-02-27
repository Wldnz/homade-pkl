<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
    @if ($response['status'] == 'success')
        <h2>Data Berhasil</h2>
        <h2>akun {{ auth()->user()->first_name }}</h2>
        @foreach ($response['data'] as $key => $address)
            <a href="{{ route('user.detail-user-address', ['id' => $address->id]) }}">
                Alamat ke - {{ $key + 1 }}
                <br>
            </a>
            <span>Alamat : {{ $address->address }}</span>
            <br>
        @endforeach
    @endif
    @if(session()->has('response'))
        @if(isset(session()->get('response')['data']['show_form']) && session()->get('response')['data']['show_form'])
            <h2>Tampilkan detail alamat</h2>
            <form action="{{ route('user.edit-user-address', ['id' => session()->get('response')['data']['address']->id]) }}" method="POST">
                @csrf
                @method("PUT")
                <span>Nama Penerima</span>
                <input type="text" name="fullname" value="{{ session()->get('response')['data']['address']->received_name }}">
                <br>
                 <span>Nomor Telepon Penerima</span>
                <input type="text" name="phone" value="{{ session()->get('response')['data']['address']->phone }}">
                <br>
                 <span>Label Alamat</span>
                <input type="text" name="label" value="{{ session()->get('response')['data']['address']->label }}">
                <br>
                 <span>Alamat Rumah</span>
                <input type="text" name="address" value="{{ session()->get('response')['data']['address']->address }}">
                <br>
                <span>Catatan</span>
                <textarea type="text" name="note">{{ session()->get('response')['data']['address']->note }}"></textarea>
                <br>
                <span>Pin Point</span>
                <input type="text" name="longitude" value="{{ session()->get('response')['data']['address']->longitude }}">
                <input type="text" name="latitude" value="{{ session()->get('response')['data']['address']->latitude }}">
                <br>
                <button class="">Ubah Data</button>
            </form>
            <br>
            <h2>Hapus Data</h2>
            <br>
            <form action="{{ route('user.delete-user-address', [ 'id' => session()->get('response')['data']['address']->id ]) }}" method="post" id="kamu-yakin">
                @csrf
                @method('delete')
                <button>Delete Alamat Ini</button>
            </form>
        @else 
            <h2>Response (Alert)</h2>
            {{ dd(session()->get('response')) }}
        @endif      
    @endif
</div>

<script>
    document.getElementById('kamu-yakin').addEventListener('submit', (e) => {
        e.preventDefault();
        if(confirm('apakah kamu yakin ingin menhapus alamat ini?')){
            e.target.submit();
        }
    });
</script>