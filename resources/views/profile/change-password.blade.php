<form action="{{ route('user.change-password') }}" method="post">
    @csrf
    @method('put')
    <label for="">Password Lama</label>
    <br>
    <input type="text" name="old_password" value="Customermade14#">
    <br>
    <label for="">Password Baru</label>
    <br>
    <input type="text" name="password" value="Customermade14#">
    <br>
    <label for="">Konfirmasi Password Baru</label>
    <br>
    <input type="text" name="password_confirmation" value="Customermade14#">
    <br>
    <button type="submit">Merubah Kata Sandi</button>
</form>

<div style="margin-top:100px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>