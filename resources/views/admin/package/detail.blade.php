
@if ($response['status'] === 'success')

@php
    $package = $response['data'];
@endphp

<form action="{{ route('admin.edit-package', [ 'id' => $package['id'] ]) }}" method="post"
    style="display:flex; flex-direction:column; gap:15px"
    enctype="multipart/form-data">
    @csrf
    @method('put')
    <span>name</span>
    <input type="text" name="name" value="{{ old('name') ?? $package['name'] }}">
    <span>description</span>
    <input type="text" name="description" value="{{ old('description') ?? $package['description'] }}">
    <span>minimum order</span>
    <input type="number" name="minimum_order" value="{{ old('minimum_order') ?? $package['minimum_order'] }}">
    <span>image</span>
    <img src="{{ $package['image_url'] }}" alt="image_gambar_package">
    <input type="file" name="image" accept="image/*">
    <button>edit handler</button>
</form>

@else
    {{ dd($response) }}
@endif
<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>