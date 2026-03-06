<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    @if($response['status'] === 'success')
        <form action="{{ route('user.edit-user-address', ['id' => $response['data']['id']]) }}" method="post">
        @csrf
        @method('put')
        <span>Nama Penerima</span>
        <input type="text" name="fullname" value="{{ old('fullname') ?? $response['data']['received_name'] }}">
        <br>
        <span>Nomor Telepon Penerima</span>
        <input type="text" name="phone" value="{{ old('phone') ?? $response['data']['phone'] }}">
        <br>
        <span>Label Alamat</span>
        <input type="text" name="label" value="{{ old('label') ?? $response['data']['label'] }}">
        <br>
        <span>Alamat Rumah</span>
        <input type="text" name="address" value="{{ old('address') ?? $response['data']['address'] }}">
        <br>
        <span>Catatan</span>
        <textarea type="text" name="note">{{ old('note') ?? $response['data']['note']  }}</textarea>
        <br>
        <span>Pin Point</span>
        <input type="text" name="longitude" value="{{ old('longitude') ?? $response['data']['longitude'] }}" >
        <input type="text" name="latitude" value="{{ old('latitude') ?? $response['data']['latitude'] }}" >
        <br>
        <input type="checkbox" name="is_main_address" id="" @checked(old('is_main_address') ?? $response['data']['is_main_address'])> <span>jadikan sebagai alaamat utama</span>
        <button>Ubah Alamat</button>
    </form>
        <br>
        <a href="{{ route('user.user-address') }}"> Kembali? </a>
    @endif

     @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>
