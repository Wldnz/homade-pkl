@if ($response['status'] === 'success')

    <h2>Detail Menu - {{ $response['data']['name'] }}</h2>

    <form action="{{ route('') }}" method="POST">
        @csrf
        @method('put')
        
    </form>

@else
    {{ dd($response) }}
@endif

<div style="margin-top:200px;">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>>