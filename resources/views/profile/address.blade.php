@php
    $placeImg = "https://placehold.co/400"
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "", "bg" => "grey"])

        <span class="h-40px flex-shrink-0"></span>

        <div class="d-flex w-100 align-items-center justify-content-center">

            <div class="d-flex w-90 align-items-start gap-5">

            <!-- begin::Left Side -->
                <div class="d-flex w-25 flex-column border-grey-1 rounded-4 gap-1 p-5 flex-shrink-0">

                    <div class="d-flex w-100 mb-2 h-100px align-items-center gap-5">

                        <div class="h-100 ratio-1 flex-shrink-0">
                            <img src="{{ $placeImg }}" class="w-100 h-100 rounded-circle" alt="">
                        </div>

                        <div class="d-flex w-100 h-75 flex-column justify-content-center w-100 ">
                            <p class="fs-3 text-accent fw-bolder mb-0">Andi Pratama</p>
                            <a href="/me" class="fs-5 mb-0 text-accent fw-light d-flex align-items-center gap-1"><img src="{{ asset("icons/edit.svg") }}" class="h-12em"> Ubah Profil</a>
                        </div>

                    </div>

                    <!--begin::Accordion-->
                    <div class="accordion accordion-icon-collapse px-3" id="kt_accordion_3">
                        <!--begin::Item-->
                        <div class="">
                            <!--begin::Header-->
                            <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#kt_accordion_3_item_1">
                                <h3 class="fs-2 fw-bolder mb-0 d-flex text-black align-items-center gap-1"><img src="{{ asset("icons/user-circle.svg") }}" class="h-15em" alt="">Akun Saya</h3>
                            </div>
                            <!--end::Header-->

                            <!--begin::Body-->
                            <div id="kt_accordion_3_item_1" class="fs-6 mb-3 collapse show ps-10 flex-column" data-bs-parent="#kt_accordion_3">
                                <div class="d-flex flex-column w-100 h-100">
                                    <a href="/me" class="fs-4 w-100 px-5 text-black fw-semibold">Profil</a>
                                    <a href="/me/address" class="fs-4 w-100 px-5 text-black fw-semibold">Alamat</a>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Item-->

                    </div>
                    <!--end::Accordion-->

                    <a href="/" class="fs-2 fw-bolder px-3 mb-0 d-flex align-items-center text-black mb-3 gap-1"><img src="{{ asset('icons/document.svg')}}" class="h-15em" alt="">Pesanan Saya</a>
                    <a href="/" class="fs-2 fw-bolder px-3 mb-0 d-flex align-items-center text-accent gap-1"><img src="{{asset('icons/log-out.svg')}}" class="h-15em img-accent" alt="">Log Out</a>

                </div>
            <!-- end::Left Side -->

                <span class="w-1px flex-shrink-0"></span>

            <!-- begin::Right Side -->
                <div class="d-flex w-75 p-10 flex-column border-grey-1 rounded-4">

                    <div class="d-flex w-100 justify-content-between h-50px">
                        <p class="fsc-4 fw-bold">Daftar Alamat</p>
                        <button onclick="openPopUp()" class="btn-primary-homade rounded-3 fsc-3"><img class="img-white h-12em" src="{{ asset('icons/plus.svg') }}" alt=""> Tambah Alamat Baru</button>
                    </div>

                    <p class="fsc-3 fw-bold">Alamat</p>

                    <div class="d-flex w-100 rounded-3 mb-5 p-5 px-7 bg-light-accent border-homade-1">
                        <div class="d-flex w-100 flex-column">
                            <p class="fsc-2 border-grey-1 bg-dark-grey p-1 px-4 text-accent w-max rounded-pill">Rumah</p>
                            <p class="fsc-3 mb-0 fw-bolder">Jl. Kemajuan V No.45</p>
                            <p class="fsc-2 mb-0 fw-bold">0812-3456-7890</p>
                            <p class="fsc-2 mb-0 w-50">Jl. Kemajuan V No.45, RT.5/RW.4, Petukangan Sel., Kec. Pesanggrahan, Kota Jakarta Selatan.</p>
                        </div>

                        <div class="d-flex justify-content-end flex-column flex-shrink-0">
                            <div class="d-flex gap-5 px-5">
                                <button class="text-accent fw-bold fsc-2">Ubah</button>
                                <button class="text-accent fw-bold fsc-2">Hapus</button>
                            </div>
                        </div>

                    </div>
                    
                    <div class="d-flex w-100 rounded-3 mb-5 p-5 px-7 border-grey-1">
                        <div class="d-flex w-100 flex-column">
                            <p class="fsc-2 border-grey-1 bg-dark-grey p-1 px-4 text-accent w-max rounded-pill">Kantor</p>
                            <p class="fsc-3 mb-0 fw-bolder">Jl. Kemajuan V No.45</p>
                            <p class="fsc-2 mb-0 fw-bold">0812-3456-7890</p>
                            <p class="fsc-2 mb-0 w-50">Jl. Kemajuan V No.45, RT.5/RW.4, Petukangan Sel., Kec. Pesanggrahan, Kota Jakarta Selatan.</p>
                        </div>

                        <div class="d-flex justify-content-end flex-column flex-shrink-0">
                            <div class="d-flex gap-5 px-5">
                                <button class="text-accent fw-bold fsc-2">Ubah</button>
                                <button class="text-accent fw-bold fsc-2">Hapus</button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            <!-- end::Right Side -->

            <!-- begin::pop up add address -->
             <div class="d-none2 w-100 h-100 pop-up" id="popUp">
                <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>
                <div class="d-flex w-60 align-items-center overflow-scroll flex-shrink-0 flex-column">
                    
                    <button class="w-100 h-50px bg-transparent flex-shrink-0 cursor-default" onclick="closePopUp()"></button>

                    <div class="d-flex w-100 min-h-100 flex-shrink-0 flex-column bg-white justify-content-center align-content-center rounded-5 border-grey-1">
                        <p class="mb-0 w-100 text-center pb-10 pt-15 fsc-4 text-accent fw-bold">Tambah Alamat</p>
                        <span class="w-100 h-1px bg-black"></span>
                        <div class="d-flex w-100 h-100 flex-column px-10">

                            <p class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold">Pinpoint</p>
                            <div type="text" class="w-100 border-grey-1 h-250px rounded-4 fsc-2">
                                <div id="map" class="w-100 h-100 rounded-4"></div>
                            </div>
                            <div class="d-flex gap-5 w-100">
                                <p id="lat-display">lat: </p>
                                <p id="lng-display">lng: </p>
                            </div>

                            <label class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold" for="addressLabel">Label Alamat</label>
                            <input id="addressLabel" type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fsc-2">
                            
                            <label class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold" for="address">Alamat Lengkap</label>
                            <textarea id="address" type="text" class="w-100 border-grey-1 min-h-75px p-5 rounded-4 fsc-2"></textarea>

                            <label class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold" for="deliveryNote">Catatan kurir (opsional)</label>
                            <input id="deliveryNote" type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fsc-2">
                            
                            <label class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold" for="reciever">Nama Penerima</label>
                            <input id="reciever" type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fsc-2">

                            <label class="mb-2 mt-5 ps-5 fsc-2 text-grey fw-bold" for="phoneNumber">Nomor HP</label>
                            <input id="phoneNumber" type="text" class="w-100 mb-2 border-grey-1 h-50px ps-5 rounded-4 fsc-2">

                            <div class="d-flex w-100 border-grey-1 mt-5 h-50px px-5 align-items-center justify-content-between rounded-4">
                                <p class="fsc-2 mb-0 text-black fw-bold">Jadikan Alamat Utama?</p>
                                <button class="d-flex align-content-center flex-column justify-content-center h-100 ratio-1" onclick="activateSwitch()">
                                    <div class="d-flex w-100 h-50 rounded-pill p-1 switch" id="mainSwitch">
                                        <span class="fill1"></span>
                                        <span class="h-100 ratio-1 flex-shrink-0 rounded-circle bg-white"></span>
                                        <span class="fill2"></span>
                                    </div>
                                </button>
                            </div>
                            <p id="test"></p>

                            <button class="btn-primary-homade my-5 rounded-4 w-100 align-content-center h-50px justify-content-center fs-3">Simpan</button>
                        </div>
                    </div>

                    <button class="w-100 h-50px bg-transparent flex-shrink-0 cursor-default" onclick="closePopUp()"></button>
                    
                </div>
                <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>
             </div>
            <!-- end::pop up add address -->

            </div>

        </div>

        <span class="h-40px flex-shrink-0"></span>
        
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

        <script>
            
            document.addEventListener("wheel", function(event) {
                if (document.activeElement.type === "number") {
                    document.activeElement.blur()
                }
            })

            document.getElementById("popUp").style.display = "none"

            function openPopUp() {
                document.getElementById("popUp").style.display = "flex"
                
                setTimeout(() => {
                    map.invalidateSize()
                    
                    getLocation() 
                }, 200) 
            }

            function closePopUp() {
                document.getElementById("popUp").style.display = "none"
            }

            let mainAddress = 0

            function activateSwitch() {
                mainAddress = !mainAddress
                document.getElementById("mainSwitch").classList.toggle("active")
                document.getElementById("test").innerText = mainAddress
            }

            var map = L.map('map').setView([-6.28, 106.71], 13)
            var marker

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map)

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude
                            const lng = position.coords.longitude
                            const userLocation = [lat, lng]

                            map.flyTo(userLocation, 16)

                            if (marker) {
                                marker.setLatLng(userLocation)
                            } else {
                                marker = L.marker(userLocation).addTo(map)
                            }

                            document.getElementById('lat-display').innerText = 'lat: ' + lat.toFixed(4)
                            document.getElementById('lng-display').innerText = 'lng: ' + lng.toFixed(4)
                        },
                        function(error) {
                            console.warn("Location access denied.")
                        }
                    )
                }
            }

            map.on('click', function(e) {
                var lat = e.latlng.lat
                var lng = e.latlng.lng

                if (marker) {
                    marker.setLatLng(e.latlng)
                } else {
                    marker = L.marker(e.latlng).addTo(map)
                }

                document.getElementById('lat-display').innerText = 'lat: ' + lat.toFixed(4)
                document.getElementById('lng-display').innerText = 'lng: ' + lng.toFixed(4)
            })
        
        </script>
    </body>


</html>

<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
    @if ($response['status'] == 'success')
        <h2>Data Berhasil</h2>
        <h2>akun {{ auth()->user()->first_name }}</h2>
        @foreach ($response['data'] as $key => $address)
            <a href="{{ route('user.detail-user-address', ['id' => $address->id]) }}">
                Alamat ke - {{ $key + 1 }}
                <br>
            </a>
            <span>Alamat : {{ $address->address }}</span>
            <br>
        @endforeach
    @endif
    @if(session()->has('response'))
        @if(isset(session()->get('response')['data']['show_form']) && session()->get('response')['data']['show_form'])
            <h2>Tampilkan detail alamat</h2>
            <form action="{{ route('user.edit-user-address', ['id' => session()->get('response')['data']['address']->id]) }}" method="POST">
                @csrf
                @method("PUT")
                <span>Nama Penerima</span>
                <input type="text" name="fullname" value="{{ session()->get('response')['data']['address']->received_name }}">
                <br>
                 <span>Nomor Telepon Penerima</span>
                <input type="text" name="phone" value="{{ session()->get('response')['data']['address']->phone }}">
                <br>
                 <span>Label Alamat</span>
                <input type="text" name="label" value="{{ session()->get('response')['data']['address']->label }}">
                <br>
                 <span>Alamat Rumah</span>
                <input type="text" name="address" value="{{ session()->get('response')['data']['address']->address }}">
                <br>
                <span>Catatan</span>
                <textarea type="text" name="note">{{ session()->get('response')['data']['address']->note }}"></textarea>
                <br>
                <span>Pin Point</span>
                <input type="text" name="longitude" value="{{ session()->get('response')['data']['address']->longitude }}">
                <input type="text" name="latitude" value="{{ session()->get('response')['data']['address']->latitude }}">
                <br>
                <button class="">Ubah Data</button>
            </form>
            <br>
            <h2>Hapus Data</h2>
            <br>
            <form action="{{ route('user.delete-user-address', [ 'id' => session()->get('response')['data']['address']->id ]) }}" method="post" id="kamu-yakin">
                @csrf
                @method('delete')
                <button>Delete Alamat Ini</button>
            </form>
        @else 
            <h2>Response (Alert)</h2>
            {{ dd(session()->get('response')) }}
        @endif      
    @endif
</div>

<script>
    document.getElementById('kamu-yakin').addEventListener('submit', (e) => {
        e.preventDefault()
        if(confirm('apakah kamu yakin ingin menhapus alamat ini?')){
            e.target.submit()
        }
    })
</script>