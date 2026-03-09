@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header',[ "page" => "home"])

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "home", "bg" => "black"])
        
        <!-- begin::Main -->
        <div class="bg-gradient-bw d-flex h-100 flex-shrink-0 flex-column">

            <div class="d-flex w-100 h-90">

                <div class="w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                    
                    <div class="d-flex flex-column w-75 h-75">
                        
                        <div class="d-flex flex-column h-100">
                            <h3 class="fsc-3 text-white fw-medium">Makanan rumahan, hemat, enak, bersih.</h3>
                            <h1 class="fsc-9 fsc-md-6 fsc-lg-6 text-yellow fw-bolder text-nowrap">Catering Harian</h1>
                            <h2 class="fsc-9 fsc-md-6 fsc-lg-6 text-white fw-bolder text-nowrap">Tanpa Langganan</h2>
                        </div>
    
                        <div class="h-100 d-flex flex-column justify-content-end">
                            <a href="/menus" class="btn-primary-homade fsc-3 fsc-md-2 rounded-pill">Lihat Menu <img src="icons/arrow-right-c.svg" alt="" class="img-white"></a>
                        </div>
    
                    </div>
                </div>
        
                <div class="d-none d-lg-flex w-100 h-100 align-items-center justify-content-center">
                    <div class="d-flex w-75 h-75">
                        <img src="img/food.webp" alt="" class="rounded-4 object-fit-cover">
                    </div>
                </div>

            </div>

        </div>
        <!-- end::Main -->

        <!-- begin::benefit -->
        <div class="d-flex w-100 bg-white flex-shrink-0 align-items-center justify-content-center">

            <div class="d-flex flex-column flex-md-row w-101 w-sm-51 w-md-90 w-lg-76 translate-50 gap-5 bg-white">

                <div class="d-flex flex-column w-100 h-250px align-items-center justify-content-center">
                    <div class="mb-3 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/calendar.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fsc-3 fsc-md-2 fsc-lg-3 mb-0">Tanpa Berlangganan</p>
                    <p class="fsc-2 fsc-md-1 fsc-lg-2 text-center w-70 w-lg-90">Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</p>
                    <p class="fsc-1 fsc-md-1 fsc-lg-1 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>

                <!--  -->

                <div class="d-flex flex-column w-100 h-250px align-items-center justify-content-center">
                    <div class="mb-3 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/book.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fsc-3 fsc-md-2 fsc-lg-3 mb-0">Bebas Pilih Menu</p>
                    <p class="fsc-2 fsc-md-1 fsc-lg-2 text-center w-70 w-lg-90">Terdapat lebih dari 30 pilihan menu, bebas memilih menu.</p>
                    <p class="fsc-1 fsc-md-1 fsc-lg-1 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>

                <!--  -->

                <div class="d-flex flex-column w-100 h-250px align-items-center justify-content-center">
                    <div class="mb-1 h-25 w-auto ratio-1 bg-accent rounded-circle d-flex align-items-center justify-content-center">
                        <div class="w-50 h-50">
                            <img src="icons/clock.svg" class="img-white w-100 h-100" alt="">
                        </div>
                    </div>
                    <p class="fsc-3 fsc-md-2 fsc-lg-3 mb-0">Bebas Pilih Waktu</p>
                    <p class="fsc-2 fsc-md-1 fsc-lg-2 text-center w-70 w-lg-90">Waktu pengantaran fleksibel antara jam 10:00-18:00 WIB.</p>
                    <p class="fsc-1 fsc-md-1 fsc-lg-1 text-center text-grey">syarat & ketentuan berlaku</p>
                </div>


            </div>

        </div>
        <!-- end::benefit -->

        <!-- begin::popular -->
        <div class="d-flex w-100 bg-white flex-shrink-0 align-items-center flex-column">

            <span class="d-flex bg-black w-50 h-1px mb-5"></span>
            <p class="fsc-6 fsc-md-5 fw-bold mb-5">Menu Terpopuler Minggu Ini</p>

            <div class="d-flex flex-column flex-sm-row w-90 w-md-76 gap-5">

                @foreach (range(1,3) as $i)
                <div class="d-flex w-100 h-350px h-sm-400px flex-column">
                    <div class="w-100 h-100 bg-black">
                        <!-- <img src="{{ $placeImg }}" alt="" class="w-100 h-100 rounded object-fit-cover"> -->
                    </div>
                    <p class="fs-1 mb-0 mt-3 w-100 text-center overflow-hidden flex-shrink-0 text-nowrap">Ayam Geprek</p>
                </div>
                @endforeach

            </div>

        </div>

        <div class="d-flex mb-5 justify-content-center">
            <a href="/menus" class="mt-5 mb-5 btn-primary-homade rounded-pill fsc-3 fsc-md-2">Lihat Menu Lainnya<img src="icons/arrow-right-c.svg" class="img-white"></a>
        </div>
        <!-- end::popular -->

        <!-- begin::packaging -->
        <div class="d-flex mt-5 w-100 justify-content-center">
            <p class="fsc-6 fsc-md-5 fw-bold">Kemasan Paket Menu Homade</p>
        </div>

        <div class="d-flex bg-1 h-100 w-100 align-content-center justify-content-center flex-column">
            <div class="d-flex h-50 h-sm-75 align-items-center justify-content-center mb-5">
                <img src="img/packaging.webp" alt="" class="h-100">
            </div>
            <p class="mt-5 fsc-3 fsc-sm-2 w-100 text-center">Lauk pendamping dapat berubah sewaktu waktu</p>
        </div>

        <div class="d-flex w-100 mb-5 bg-2 flex-shrink-0 align-content-center justify-content-center">
            
            <div class="d-flex w-75 flex-column flex-md-row">
                    
                <div class="d-flex flex-column w-100 h-400px align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/bento.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fsc-5 fsc-md-3 mb-0 text-center">Bento Mealbox</p>
                        <p class="fsc-2 fsc-md-1 mb-0 w-75 w-md-75 text-center">Paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</p>
                    </div>

                </div>
                
                <div class="d-flex flex-column w-100 h-400px align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/cardboard.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fsc-5 fsc-md-3 mb-0 text-center">Valuebox</p>
                        <p class="fsc-2 fsc-md-1 mb-0 w-75 w-md-75 text-center">Paket hemat terdiri dari lauk utama dan laukpendamping terbatas optional</p>
                    </div>

                </div>

                <div class="d-flex flex-column w-100 h-400px align-items-center">

                    <div class="d-flex w-100 h-75 flex-column justify-content-center">
                        <img src="img/plastic.webp" alt="" class="w-100 h-75 object-fit-contain">
                    </div>

                    <div class="d-flex flex-column w-100 h-25 align-items-center">
                        <p class="fsc-5 fsc-md-3 mb-0 text-center">Family Pack</p>
                        <p class="fsc-2 fsc-md-1 mb-0 w-75 w-md-75 text-center">Paket keluarga terdiri dari lauk utama dan sayuran pendamping (tanpa nasi), porsi untuk 4 orang.</p>
                    </div>

                </div>


            </div>
        </div>

        <div class="d-flex w-100 justify-content-center">

            <div class="d-flex w-75 justify-content-end mt-5">
                <a href="/menus" class="btn-primary-homade rounded-pill fsc-3 fsc-md-2 mt-5">Lihat Menu Selengkapnya <img src="icons/arrow-right-c.svg" class="img-white"></a>
            </div>
        </div>
        <!-- end::packaging -->

        <span class="h-100px flex-shrink-0 d-md-none"></span>

        <div class="d-flex w-100 justify-content-center">
            <div class="d-flex w-75 justify-content-start">
                <p class="fsc-6 fsc-md-5 fw-bold">Kategori Menu</p>
            </div>
        </div>

        <div class="d-flex w-100 align-items-center justify-content-center flex-shrink-0">
            <div class="d-flex flex-column flex-md-row w-75 gap-5">
                <a href="/menus" class="d-flex w-100 h-150px align-items-center justify-content-center text-white fsc-2 bg-chicken">Ayam (99)</a>
                <a href="/menus" class="d-flex w-100 h-150px align-items-center justify-content-center text-white fsc-2 bg-fish">Ikan & Seafood (99)</a>
                <a href="/menus" class="d-flex w-100 h-150px align-items-center justify-content-center text-white fsc-2 bg-rice">Nasi (99)</a>
                <a href="/menus" class="d-flex w-100 h-150px align-items-center justify-content-center text-white fsc-2 bg-beef">Sapi & Kambing (99)</a>
            </div>
        </div>

        <span class="d-flex w-1px h-25 flex-shrink-0"></span>

        <div class="d-flex w-100 h-md-50 flex-shrink-0 mb-5 align-items-center justify-content-center">
            <div class="d-flex align-items-center flex-column d-md-grid grid-template-homade-sponsor w-75 h-100 gap-5">
                @foreach (range(1,12) as $i)
                <img src="{{ $placeImg }}" alt="" class="object-fit-cover w-25 bg-black">
                @endforeach
            </div>
        </div>

        <span class="d-flex w-1px h-50px flex-shrink-0"></span>

        @include('components.navbarFoot',[ "page" => "home"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
    </body>

</html>