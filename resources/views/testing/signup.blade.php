<div>
    
    <form action="/testing/signup" method="post">
        @csrf
        <input type="text" name="first_name" value="nama_depan_catering">
        <input type="text" name="last_name" value="nama_belakang_catering">
        <input type="text" name="email" value="catering@testing.id">
        <input type="text" name="password" id="" value="catering-Passowrd@asid">
        <button type="submit">Register!</button>
    </form>

    @if (session()->has('message'))
        {{ dd(session()->all()) }}
    @endif

</div>
