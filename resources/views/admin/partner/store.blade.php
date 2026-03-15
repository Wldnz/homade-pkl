 <a href="{{ route('admin.partners') }}">Back</a>


    <form action="{{ route('admin.add-partner') }}" method="post"
        style="display:flex; flex-direction:column; gap:15px" enctype="multipart/form-data">
        @csrf
        <span>name</span>
        <input type="text" name="name" value="{{ old('name') }}">
        <span>image</span>
        <input type="file" name="image" accept="">
        <button>edit handler</button>
    </form>
<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>