@if ($response['status'] === 'success')

<a href="{{ route('admin.partners') }}">Back</a>

@php
    $partner = $response['data'];
@endphp

<form action="{{ route('admin.edit-partner', ['id' => $partner['id']]) }}" method="post"
    style="display:flex; flex-direction:column; gap:15px" enctype="multipart/form-data">
    @csrf
    @method('put')
    <span>name</span>
    <input type="text" name="name" value="{{ old('name') ?? $partner['name'] }}">
    <span>image</span>
    <img src="{{ $partner['image_url'] }}" alt="image_gambar_partner">
    <input type="file" name="image" accept="">
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