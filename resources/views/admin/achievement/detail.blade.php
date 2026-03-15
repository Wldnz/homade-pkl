@if ($response['status'] === 'success')

    @php
        $achievement = $response['data'];
    @endphp

    <a href="{{ route('admin.achievements') }}">Back?</a>
    <br>

    <form action="{{ route('admin.edit-achievement', ['id' => $achievement['id']]) }}" method="POST">
        @csrf
        @method('put')
        <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="name">Nama Prestasi</label>
            <input type="text" name="name" value="{{ old('name') ?? $achievement['name'] }}" required>
        </div>
         <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="description">Deskripsi Prestasi</label>
            <input type="text" name="description" value="{{ old("description") ?? $achievement['description'] }}" required>
        </div>
         <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="date_at">Tanggal Di Dapatkan</label>
            {{-- <input type="text" name="date_at" value="{{ old('date_at') ?? $achievement['date_at'] }}"> --}}
        </div>
        <button>Simpan Perubahan</button>
    </form>

@else

{{ dd($response) }}

@endif

<div style="margin-top: 200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>