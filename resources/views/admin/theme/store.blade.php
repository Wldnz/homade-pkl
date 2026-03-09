
 <h2>Tambahakan Tema</h2>

    <form action="{{ route('admin.add-theme' ) }}" method="post" style="display:flex; flex-direction: column; gap:10px;">
        @csrf
        <h4>Data Tema</h4>
        <input type="text" name="name" value="{{ old('name') }}">
        <textarea name="description" id="">{{ old('description') }}</textarea>
        <button>Tambahkan Tema</button>
    </form>


<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>