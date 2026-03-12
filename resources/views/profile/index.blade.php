@php
    $placeImg = "https://placehold.co/400";
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

                <span class="w-70px flex-shrink-0"></span>

            <!-- begin::Right Side -->
                <div class="d-flex w-75 p-10 flex-column border-grey-1 rounded-4">
                    <p class="fs-1 fw-bold mb-0">Profil Saya</p>
                    <p class="fs-5">Kelola informasi profil Anda untuk mengontrol, melindungi dan mengamankan akun</p>

                    <div class="d-flex gap-5 mb-5 w-100">

                        <div class="d-flex flex-column w-100">
                            <label for="first_name" class="mb-1 fs-6 text-grey">Nama Depan</label>
                            <input type="text" id="first_name" class="w-100 p-2 py-3 fs-6 border-grey-1 rounded-3">
                        </div>

                        <div class="d-flex flex-column w-100">
                            <label for="last_name" class="mb-1 fs-6 text-grey">Nama Belakang</label>
                            <input type="text" id="last_name" class="w-100 p-2 py-3 fs-6 border-grey-1 rounded-3">
                        </div>

                    </div>

                    <div class="d-flex flex-column mb-5 w-100">
                        <label for="phone_number" class="mb-1 fs-6 text-grey">Nomor Telepon</label>
                        <input type="number" id="phone_number" class="w-100 p-2 py-3 fs-6 border-grey-1 rounded-3">
                    </div>

                    <div class="d-flex flex-column mb-5 w-100">
                        <label for="email" class="mb-1 fs-6 text-grey">Email</label>
                        <input type="email" id="email" class="w-100 p-2 py-3 fs-6 border-grey-1 rounded-3">
                    </div>
                    
                    <label class="mb-1 fs-6 opacity-0">halo teman teman, nama aku wildan</label>
                    <button class="btn-primary-homade rounded-3 w-100 align-items-center justify-content-center fs-2 p-4 fw-bold mb-5">Simpan</button>
                    <button class="btn-primary-homade rounded-3 w-100 align-items-center justify-content-center fs-2 p-4 fw-bold" onclick="openPopUp()">Ubah Password</button>
                </div>
            <!-- end::Right Side -->

            <!-- begin::pop up change password -->
             <div class="w-100 h-100 pop-up" id="popUp">
                <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>
                <div class="d-flex w-50 align-items-center flex-shrink-0 flex-column">
                    <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>

                    <div class="d-flex w-75 h-75 flex-shrink-0 p-10 px-20 flex-column bg-white justify-content-center align-content-center rounded-5 border-grey-1">
                        <p class="w-100 text-center fs-2 text-accent fw-bold ">Ubah Password</p>
                        <input type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fs-4 mb-5 mt-5" placeholder="Password Lama">
                        <input type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fs-4" placeholder="Password Baru">
                        <p class="mb-0 mt-2 mb-1">Gunakan 8 karakter atau lebih dengan campuran huruf, angka, dan simbol.</p>
                        <input type="text" class="w-100 border-grey-1 h-50px ps-5 rounded-4 fs-4 mb-5" placeholder="Konfirmasi Password Baru">
                        <button class="btn-primary-homade rounded-4 w-100 align-content-center h-50px justify-content-center fs-3">Simpan</button>
                    </div>
                    
                    <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>
                </div>
                <button class="w-100 h-100 bg-transparent cursor-default" onclick="closePopUp()"></button>
             </div>
            <!-- end::pop up change password -->

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

            function openPopUp() {document.getElementById("popUp").style.display = "flex"}

            function closePopUp() {document.getElementById("popUp").style.display = "none"}

        </script>
    </body>


</html>