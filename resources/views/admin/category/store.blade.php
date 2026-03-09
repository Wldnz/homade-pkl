
 <h2>Tambahakan category</h2>

    <form action="{{ route('admin.add-category' ) }}" method="post" style="display:flex; flex-direction: column; gap:10px;">
        @csrf
        <h4>Data category</h4>
        <input type="text" name="name" value="{{ old('name') }}">
        <button>Tambahkan Perubahan</button>
    </form>


<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>