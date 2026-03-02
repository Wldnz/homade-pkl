@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "profile", "bg" => "grey"])
        
        <span class="h-20px flex-shrink-0"></span>

        <div class="d-flex flex-column align-items-center mt-5 mb-5">
            <p class="fsc-7 fw-bolder text-black homade-underline-black">Profile Homade</p>
            <p class="fs-2 fw-bolder text-black text-center">Menghadirkan rasa masakan rumahan yang higienis dan terjangkau <br> untuk menemani setiap langkah  perjalanan anda</p>
        </div>

        <div class="d-flex h-100 w-100 align-items-center justify-content-center gap-5 flex-shrink-0">
            
            <div class="d-flex h-75 w-90">

                <div class="d-flex w-100 h-100 flex-column">
                    <p class="fs-1 fw-bolder text-accent">SIAPA KAMI</p>
                    <p class="fsc-6 fw-bolder">Catering Harian Tanpa Langganan</p>
                    <p class="fs-3 w-90 fw-bolder text-accent">MASAKAN RUMAHAN, HEMAT, ENAK, BERSIH DENGAN HARGA TERJANGKAU</p>
                    <span class="h-100"></span>
                    <p class="fs-3 w-90">Homade bergerak di dalam industri food & beverages yang memiliki standar kesehatan, rasa, kualitas yang tinggi, namun harganya sangat terjangkau.</p>
                    <p class="fs-3 w-90">Kami percaya bahwa makanan berkualitas tidak harus mahal. Dengan sistem manajemen yang efisien, kami membawa kelezatan dapur ibu ke meja makan Anda, kapan saja Anda inginkan.</p>
                </div>

                <div class="d-flex w-75 h-100 align-items-center justify-content-center">
                    <img src="{{ asset("img/profile.png") }}" class="w-90 h-90 rounded">
                </div>

            </div>

        </div>

        <div class="w-100 d-flex align-items-center justify-content-center">

            <div class="w-90 d-flex">

                <a href="/menu" class="btn-primary-homade rounded-pill bg-accent fs-2">Lihat Menu <img src="icons/arrow-right-c.svg" class="img-white"></a>

            </div>
            
        </div>

        <span class="h-100px flex-shrink-0"></span>

        <div class="d-flex flex-column align-items-center w-100 flex-shrink-0 gap-5">

            <p class="fsc-7 fw-bolder text-black homade-underline-black mb-5">Visi dan Misi</p>

            <div class="w-70 p-5 d-flex gap-5 mb-5">

                <div class="w-100 h-100 d-flex flex-column p-5 border-grey-1 rounded-4">

                    <div class="h-50px d-flex">
                        <div class="d-flex h-100 ratio-1 bg-light-accent rounded-3 align-items-center justify-content-center">
                            <div class="d-flex w-50 h-50">
                                <img src="{{ asset("icons/show.svg") }}" class="w-100 h-100 img-accent" alt="">
                            </div>
                        </div>
                    </div>

                    <p class="fsc-4 mt-5 mb-5 fw-black text-black">Visi Kami</p>

                    <p class="fs-5 fw-medium text-black">Memberikan makanan yang sangat terjangkau Kepada cutomer dengan kualitas bahan, rasa dan higienis  yang tinggi bagi seluruh masyarakat Indonesia</p>

                    <span class="h-20px"></span>
                    
                </div>
                
                <span class="w-40px"></span>
                
                <div class="w-100 h-100 d-flex flex-column p-5 border-grey-1 rounded-4">

                    <div class="h-50px d-flex">
                        <div class="d-flex h-100 ratio-1 bg-light-accent rounded-3 align-items-center justify-content-center">
                            <div class="d-flex w-50 h-50">
                                <img src="{{ asset("icons/document.svg") }}" class="w-100 h-100 img-accent" alt="">
                            </div>
                        </div>
                    </div>

                    <p class="fsc-4 mt-5 mb-5 fw-black text-black">Misi Kami</p>

                    <p class="fs-5 fw-medium text-black">Membantu perekonomian masyarakat indonesia, yang mana pasar makanan di indonesia cukup besar pengaruhnya bagi UMKM lokal den tenaga kerja lokal</p>

                    <span class="h-20px"></span>

                </div>

            </div>

        </div>

        <span class="h-60px flex-shrink-0"></span>
        
        <div class="d-flex flex-shrink-0 align-items-center justify-content-center w-100">

            <div class="d-flex gap-5 w-90">

                <div class="d-flex flex-column align-items-center w-50">
                    <div class="w-75 d-flex flex-column gap-1 align-items-center">

                        <img src="{{ $placeImg }}" alt="" class="w-100 h-250px object-fit-cover">

                        <div class="w-100 h-250px d-flex align-items-center gap-1 overflow-hidden">

                            <img src="{{ $placeImg }}" alt="" class="w-50 h-100 object-fit-cover">
                            <img src="{{ $placeImg }}" alt="" class="w-50 h-100 object-fit-cover">

                        </div>  

                    </div>
                </div>
                
                <div class="w-50 h-100px bg-warning"></div>

            </div>
                
        </div>
        
        <span class="h-60px flex-shrink-0"></span>

        @include('components.navbarFoot',[ "page" => "profile"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
    </body>


</html>