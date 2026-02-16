<div>

    <form action='/testing/signin' method="post">
        @csrf
        <input type="email" name="email" value="akun@homade.id">
        <input type="text" name="password" value="homemade12">
        <button type="submit">Login disini</button>
    </form>

    @if(session()->has('message'))
        {{ dd(session()->all()) }}
    @endif

</div>