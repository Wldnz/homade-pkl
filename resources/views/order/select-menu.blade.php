<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->

    @if ($response['status'] === 'success')
        <form action="{{ route('user.checkout') }}" style="display:flex; flex-direction: column; gap: 10px;" method="POST">
            @csrf
            <input type="date" name="delivery_at" id="" value="{{ now()->addDays(3)->format('Y-m-d') }}">
            @foreach ($response['data']['items'] as $index=>$item)
                <span>Nama Menu ({{ $index }}): {{ $item['name'] }}</span>
                @if ($index < 1)
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
                    @foreach ($item['packages'] as $index_package=>$package)
                        <span>Nama Paket: {{ $package->name }}</span>
                        <input type="hidden" name={{ "items[$index][packages][$index_package][id]"}} value="{{ $package->id }}">
                        <div style="display:flex; flex-direction: column; gap: 10px;">
                            <label for="">Jumlah Pemesanan</label>
                            <input type="number" name={{ "items[$index][packages][$index_package][quantity]" }} min="{{ $package->minimum_order }}" value="1">
                        </div>
                        <div style="display:flex; flex-direction: column; gap: 10px;">
                            <label for="">Catatan</label>
                            <textarea name={{"items[$index][packages][$index_package][note]"}} id="">
                                
                            </textarea>
                        </div>
                    @endforeach
                    <button class="">Buatkan invoice</button>
                @endif
            @endforeach
        </form>
    @endif

    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>