@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "home", "size" => "large"])
        
        <div class="bg-gradient-bw d-flex h-100 flex-shrink-0 flex-column">

            <div class="d-flex w-100 h-90">

                <div class="w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                    
                    <div class="d-flex flex-column w-75 h-75">
                        
                        <div class="d-flex flex-column h-100">
                            <h3 class="fsc-3 text-white fw-medium">Makanan rumahan, hemat, enak, bersih.</h3>
                            <h1 class="fsc-7 text-yellow fw-bolder">Catering Harian</h1>
                            <h2 class="fsc-7 text-white fw-bolder">Tanpa Langganan</h2>
                        </div>
    
                        <div class="h-100 d-flex flex-column justify-content-end">
                            <a href="/menus" class="btn-primary-homade fs-2 rounded-pill">Lihat Menu <img src="icons/arrow-right-c.svg" alt="" class="img-white"></a>
                        </div>
    
                    </div>
                </div>
        
                <div class="d-flex w-100 h-100 align-items-center justify-content-center">
                    <div class="d-flex w-75 h-75">
                        <img src="img/food.webp" alt="" class="rounded-4 object-fit-cover">
                    </div>
                </div>

            </div>

        </div>

        <div class="d-flex w-100 h-50 bg-white flex-shrink-0 align-items-center justify-content-center">

            <div class="d-flex h-100 w-75 translate-50 gap-5 bg-white">

                <div class="d-flex flex-column w-100 h-100 align-items-center justify-content-center">
                    <div class="mb-3 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/calendar.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fs-1 mb-0">Tanpa Berlangganan</p>
                    <p class="fs-5 text-center">Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</p>
                    <p class="fs-6 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>

                <div class="d-flex flex-column w-100 h-100 align-items-center justify-content-center">
                    <div class="mb-3 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/book.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fs-1 mb-0">Bebas Pilih Menu</p>
                    <p class="fs-5 text-center">Terdapat lebih dari 30 pilihan menu, bebas memilih menu.</p>
                    <p class="fs-6 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>

                <div class="d-flex flex-column w-100 h-100 align-items-center justify-content-center">
                    <div class="mb-1 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/clock.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fs-2 mb-0">Bebas Pilih Waktu</p>
                    <p class="fs-5 text-center">Waktu pengantaran fleksibel antara jam 10:00-18:00 WIB.</p>
                    <p class="fs-6 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>


            </div>

        </div>

        <div class="d-flex w-100 h-100 bg-white flex-shrink-0 align-items-center flex-column">

            <span class="d-flex bg-black w-50 h-1px mb-5"></span>
            <p class="fsc-6 fw-bold mb-5">Menu Terpopuler Minggu Ini</p>

            <div class="d-flex w-75 h-75 gap-5">

            @foreach (range(1,3) as $i)
            <div class="d-flex w-100 h-100 flex-column">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 flex-shrink-1 rounded object-fit-cover">
                <p class="fs-1 mb-0 mt-3 w-100 text-center overflow-hidden text-nowrap">Ayam Geprek</p>
            </div>
            @endforeach

            </div>

        </div>

        <div class="d-flex mb-5 justify-content-center">
            <a href="/menus" class="mt-5 mb-5 btn-primary-homade rounded-pill fs-3">Lihat Menu Lainnya<img src="icons/arrow-right-c.svg" class="img-white"></a>
        </div>

        <div class="d-flex mt-5 w-100 justify-content-center">
            <p class="fsc-6 fw-bold">Kemasan Paket Menu Homade</p>
        </div>

        <div class="d-flex bg-1 h-100 w-100 align-content-center justify-content-center flex-column">
            <div class="d-flex h-75 align-items-center justify-content-center mb-5">
                <img src="img/packaging.webp" alt="" class="h-100">
            </div>
            <p class="mt-5 fs-5 w-100 text-center">Lauk pendamping dapat berubah sewaktu waktu</p>
        </div>

        <div class="d-flex w-100 h-75 mb-5 bg-2 flex-shrink-0 align-content-center justify-content-center">
            
            <div class="d-flex h-100 w-75">
                    
                <div class="d-flex flex-column w-100 h-100 align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/bento.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fs-1 mb-0 text-center">Bento Mealbox</p>
                        <p class="fs-6 mb-0 w-75 text-center">Paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</p>
                    </div>

                </div>
                
                <div class="d-flex flex-column w-100 h-100 align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/cardboard.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fs-1 mb-0 text-center">Valuebox</p>
                        <p class="fs-6 mb-0 w-100 text-center">Paket hemat terdiri dari  laukutama dan laukpendamping terbatas optional</p>
                    </div>

                </div>

                <div class="d-flex flex-column w-100 h-100 align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/plastic.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fs-1 mb-0 text-center">Family Pack</p>
                        <p class="fs-6 mb-0 w-75 text-center">Paket keluarga terdiri dari lauk utama dan sayuran pendamping (tanpa nasi), porsi untuk 4 orang.</p>
                    </div>

                </div>


            </div>
        </div>

        <div class="d-flex w-100 justify-content-center">

            <div class="d-flex w-75 justify-content-end mt-5">
                <a href="/menus" class="btn-primary-homade rounded-pill fs-3 mt-5">Lihat Mena Selengkapnya <img src="icons/arrow-right-c.svg" class="img-white"></a>
            </div>
        </div>

        <div class="d-flex w-100 justify-content-center">
            <div class="d-flex w-75 justify-content-start">
                <p class="fsc-6 fw-bold">Kategori Menu</p>
            </div>
        </div>

        <div class="d-flex w-100 h-25 align-items-center justify-content-center flex-shrink-0">
            <div class="d-flex w-75 h-100 gap-5">
                <a href="/menus" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-chicken">Ayam (99)</a>
                <a href="/menus" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-fish">Ikan & Seafood (99)</a>
                <a href="/menus" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-rice">Nasi (99)</a>
                <a href="/menus" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-beef">Sapi & Kambing (99)</a>
            </div>
        </div>

        <span class="d-flex w-1px h-25 flex-shrink-0"></span>

        <div class="d-flex w-100 h-50 flex-shrink-0 flex-column mb-5 gap-5 align-items-center justify-content-center">
            <div class="d-flex w-75 h-50 gap-5">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
            </div>
            <div class="d-flex w-75 h-50 gap-5">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
                <img src="{{ $placeImg }}" alt="" class="w-100 h-100 object-fit-cover">
            </div>
        </div>

        <span class="d-flex w-1px h-20px flex-shrink-0"></span>

        @include('components.navbarFoot',[ "page" => "home"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
    </body>

</html>