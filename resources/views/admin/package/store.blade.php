<form action="{{ route('admin.add-package' ) }}" method="post"
    style="display:flex; flex-direction:column; gap:15px"
    enctype="multipart/form-data">
    @csrf
    <span>name</span>
    <input type="text" name="name" value="{{ old('name') }}">
    <span>description</span>
    <input type="text" name="description" value="{{ old('description') }}">
    <span>minimum order</span>
    <input type="number" name="minimum_order" value="{{ old('minimum_order')}}">
    <span>image</span>
    <input type="file" name="image" accept="image/*">
    <button>edit handler</button>
</form>

<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>