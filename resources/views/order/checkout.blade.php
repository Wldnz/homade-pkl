<div>
    <!-- Walk as if you are kissing the Earth with your feet. - Thich Nhat Hanh -->
    {{-- posisinya ini tu methodnya post? --}}
    <form action=""></form>

    {{ dd($response) }}

    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>
