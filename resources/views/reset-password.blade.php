<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->

    <form action="{{ route('user.reset-password-handler', ['token' => $token]) }}" method="post">
        @csrf
        <h2>Halo, {{ $user->first_name }}</h2>
         <div style="display:flex; flex-direction:column; gap:5px;">
            <label for="email">Alamat Email</label>
            <input type="text" name="email" id="email" required readonly value="{{ $user->email }}">
        </div>
        <div style="display:flex; flex-direction:column; gap:5px;">
            <label for="password">Kata Sandi Baru</label>
            <input type="text" name="password" id="password" required  value="{{ old('password') }}">
        </div>
        <div style="display:flex; flex-direction:column; gap:5px;">
            <label for="password_confirmation">KOnfirmasi ata Sandi Baru</label>
            <input type="text" name="password_confirmation" id="password_confirmation" required  value="{{ old('password_confirmation') }}">
        </div>
        <button>Reset Kata Sandi Sekarang!</button>
    </form>

    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif

</div>
