@if ($response['status'] === 'success')

    @php
        $partners = $response['data']['partners'];
    @endphp

    <a href="{{ route('admin.add-partner-page') }}">Tambahkan Partner</a>
    <br>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Dibuat</th>
            <th>Detail</th>
             <th>Hapus</th>
        </tr>
        <tbody>
            @foreach($partners as $partner)
                <tr>
                <td>{{ $partner['id'] }}</td>
                <td>{{ $partner['name'] }}</td>
                <td>{{ $partner['created_at'] }}</td>
                <td>
                    <a href="{{ route('admin.detail-partner', ['id' => $partner['id']]) }}">Detai;</a>
                </td>
                <td>
                    <form action="{{route('admin.delete-partner', ['id' => $partner['id']])}}" method="post">
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