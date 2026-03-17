<div style="">
    <form action="{{ route('user.forgot-password') }}" method="post">
        @csrf
        <h2>Kamu Lupa Password?</h2>
        <br>
        <input type="text" name="email" id="" value="customer@homade.id" required>
        @if (session()->has('response'))
            <br>
            <span style="color:red;">Error Message {{ session()->get('response')['message'] }}</span>
        @endif
        <br>
        <button>Kirim Permintaan Lupa password</button>
    </form>
</div>


<div style="margin-top:100px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>