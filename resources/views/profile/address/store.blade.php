<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <form action="{{ route('user.add-user-address') }}" method="post">
        @csrf
        <span>Nama Penerima</span>
        <input type="text" name="fullname" value="{{ old('fullname') }}">
        <br>
        <span>Nomor Telepon Penerima</span>
        <input type="text" name="phone" value="{{ old('phone')  }}">
        <br>
        <span>Label Alamat</span>
        <input type="text" name="label" value="{{ old('label') }}">
        <br>
        <span>Alamat Rumah</span>
        <input type="text" name="address" value="{{ old('address') }}">
        <br>
        <span>Catatan</span>
        <textarea type="text" name="note">{{ old('note')  }}</textarea>
        <br>
        <span>Pin Point</span>
        <input type="text" name="longitude" value="{{ old('longitude') }}" >
        <input type="text" name="latitude" value="{{ old('latitude') }}" >
        <br>
        <input type="checkbox" name="is_main_address" id=""> <span>jadikan sebagai alaamat utama</span>
        <button>Tambahkan Alamat</button>
    </form>

    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif

</div>