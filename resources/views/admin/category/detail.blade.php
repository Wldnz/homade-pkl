
@if ($response['status'] === 'success')

    <h2>Detail category - {{ $response['data']['name'] }}</h2>

    <form action="{{ route('admin.edit-category', ['id' => $response['data']['id']] ) }}" method="post" style="display:flex; flex-direction: column; gap:10px;">
        @csrf
        @method('put')
        <h4>Data category</h4>
        <input type="text" name="name" value="{{ old('name') ?? $response['data']['name'] }}">
        <span>Dibuat Pada: {{ $response['data']['created_at'] }}</span>
        <button>Simpan Perubahan</button>
    </form>

    <form action="{{ route('admin.delete-category', ['id' => $response['data']['id']] ) }}" method="post" style="display:flex; flex-direction: column; gap:10px;">
        @csrf
        @method('delete')
        <button>Hapus Data Perubahan</button>
    </form>

@else
    {{ dd($response) }}
@endif

<div style="margin-top:200px">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>