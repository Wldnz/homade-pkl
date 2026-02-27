<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    @if($response['status'] === 'success')
        <span>Nama Penerima : {{ $response['data']->received_name }}</span>
        <br>
        <a href="{{ route('user.user-address') }}"> Kembali? </a>
    @endif
</div>
