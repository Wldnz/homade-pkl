 <a href="{{ route('admin.achievements') }}">Back?</a>
    <br>

    <form action="{{ route('admin.add-achievement') }}" method="POST">
        @csrf
        <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="name">Nama Prestasi</label>
            <input type="text" name="name" value="Juara Internasional 2023" required>
        </div>
         <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="description">Deskripsi Prestasi</label>
            <input type="text" name="description" value="kita menang dan anda harus menang ya!" required>
        </div>
         <div style="display:flex; flex-direction:column; gap:10px;">
            <label for="date_at">Tanggal Di Dapatkan</label>
            <input type="text" name="date_at">
        </div>
        <button>Tambahkan</button>
    </form>

<div style="margin-top: 200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>