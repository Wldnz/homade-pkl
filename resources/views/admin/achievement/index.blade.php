@if ($response['status'] === 'success')

    @php
        $achievements = $response['data']['achievements'];
    @endphp

    <a href="{{ route('admin.add-achievement-page') }}">Tambahkan Prestasi</a>
    <br>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Prestasi</th>
            <th>Di dapatkan</th>
            <th>Dibuat</th>
            <th>Detail</th>
             <th>Hapus</th>
        </tr>
        <tbody>
            @foreach($achievements as $achievement)
                <tr>
                <td>{{ $achievement['id'] }}</td>
                <td>{{ $achievement['name'] }}</td>
                <td>{{ $achievement['date_at'] }}</td>
                <td>{{ $achievement['created_at'] }}</td>
                <td>
                    <a href="{{ route('admin.detail-achievement', ['id' => $achievement['id']]) }}">Detai;</a>
                </td>
                <td>
                    <form action="{{route('admin.delete-achievement', ['id' => $achievement['id']])}}" method="post">
                        @csrf
                        @method('delete')
                        <button>delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@else

{{ dd($response) }}

@endif

<div style="margin-top: 200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>