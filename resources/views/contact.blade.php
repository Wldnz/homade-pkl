@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "contact", "bg" => "grey"])
        
        <div class="d-flex w-100 h-50 flex-shrink-0 bg-contact align-items-center justify-content-center flex-column">
            <p class="fsc-6 text-white fw-bolder">Kontak Kami</p>
            <p class="fs-2 text-white fw-bold text-center">Hubungi kami untuk pemesanan katering harian, acara <br> kantor, atau kerjasama bisnis.</p>
        </div>

        <span class="h-40px flex-shrink-0"></span>

        <div class="d-flex w-10 h-125 flex-shrink-0 justify-content-center">
            
            <div class="d-flex w-75 gap-5">

                <div class="d-flex flex-column w-50 h-100">
                    <p class="fsc-5 fw-black mb-3">Alamat</p>
                    <span class="d-flex h-5px bg-accent w-80px flex-shrink-0 mb-5"></span>

                    <div class="d-flex w-100 flex-shrink-0 gap-5">

                        <div class="d-flex w-60px h-60px flex-shrink-0">
                            <div class="d-flex w-100 h-100 bg-light-accent align-items-center justify-content-center rounded-4">
                                <div class="d-flex w-50 h-50">
                                    <img src="icons/building.svg" class="w-100 h-100 img-accent" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex w-100 flex-column">
                            <p class="fs-2 text-black fw-black">PT. Homade Kreatif Teknologi</p>
                            <p class="fs-4">Jl. Tebet Timur Dalam VI No.3, RT.008 RW.011 <br> Kel. Tebet Timur, Kec. Tebet, <br> Jakarta Selatan, Jakarta 12820</p>
                        </div>

                    </div>

                    <span class="h-20px flex-shrink-0"></span>

                    <div class="d-flex w-100 flex-shrink-0 gap-5">

                        <div class="d-flex w-60px h-60px flex-shrink-0">
                            <div class="d-flex w-100 h-100 bg-light-accent align-items-center justify-content-center rounded-4">
                                <div class="d-flex w-50 h-50 p-1">
                                    <img src="icons/whatsapp.svg" class="w-100 h-100 img-accent" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex w-100 flex-column">
                            <p class="fs-2 text-black fw-black">Telepon & Whatsapp</p>
                            <p class="fs-4">0857-1180-1336</p>
                        </div>

                    </div>

                    <span class="h-20px flex-shrink-0"></span>

                    <div class="d-flex w-100 flex-shrink-0 gap-5">

                        <div class="d-flex w-60px h-60px flex-shrink-0">
                            <div class="d-flex w-100 h-100 bg-light-accent align-items-center justify-content-center rounded-4">
                                <div class="d-flex w-50 h-50">
                                    <img src="icons/mail.svg" class="w-100 h-100 img-accent" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex w-100 flex-column">
                            <p class="fs-2 text-black fw-black">Email</p>
                            <p class="fs-4">0857-1180-1336</p>
                        </div>

                    </div>

                    <span class="h-20px flex-shrink-0"></span>
                    
                    <div class="d-flex w-100 flex-column">

                        <p class="fs-2 text-black fw-black">Ikuti Kami</p>

                        <div class="d-flex h-40px w-100 gap-5"> 
                            <a href="" class="h-100 ratio-1 bg-black"></a>
                            <a href="" class="h-100 ratio-1 bg-black"></a>
                            <a href="" class="h-100 ratio-1 bg-black"></a>
                        </div>

                    </div>

                    <span class="h-40px flex-shrink-0"></span>
                    
                    <div class="d-flex w-90 h-100 rounded-4">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.223779593465!2d106.85558407355448!3d-6.234205161047454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3ba3fb17413%3A0xe50e0d2266ac9e74!2sPT.%20Abhimantra%20Sistem%20Solusindo!5e0!3m2!1sen!2sid!4v1772608159435!5m2!1sen!2sid" style="border:0;" class="w-100 h-100 rounded-4" allowfullscreen="true" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                    
                </div>

                <form class="d-flex flex-column w-50 p-10 border-grey-1 overflow-scroll rounded-4">

                    <p class="fsc-5 flex-shrink-0 fw-black">Hubungi Kami</p>
                    <p class="fs-3 flex-shrink-0">Silahkan isi form di bawah ini, kami akan membalas pesan Anda secepatnya.</p>

                    <label for="fullname" class="flex-shrink-0 fs-3 mt-5 mb-1">NAMA LENGKAP WAJIB</label>
                    <input type="text" id="fullname" class="flex-shrink-0 h-40px border-grey-1 rounded-3 px-2 fs-4">

                    <label for="email" class="flex-shrink-0 fs-3 mt-5 mb-1">ALAMAT EMAIL WAJIB</label>
                    <input type="text" id="email" class="flex-shrink-0 h-40px border-grey-1 rounded-3 px-2 fs-4">

                    <label for="subject" class="flex-shrink-0 fs-3 mt-5 mb-1">SUBJEK</label>
                    <input type="text" id="subject" class="flex-shrink-0 h-40px border-grey-1 rounded-3 px-2 fs-4">
                    
                    <label for="content" class="flex-shrink-0 fs-3 mt-5 mb-1">PESAN ANDA</label>
                    <textarea type="text" id="content" placeholder="Masukkan pertanyaan atau komentar anda" class="flex-shrink-0 min-h-125px mb-5 border-grey-1 rounded-3 p-2 fs-6"></textarea>

                    <button class="flex-shrink-0 w-100 h-50px bg-accent rounded-3 fs-5 mt-5 fw-bold btn-primary-homade align-items-center justify-content-center">KIRIM PESAN <img src="icons/send.svg" class="img-white h-75"></button>

                </form>

            </div>
        </div>

        <span class="h-100px flex-shrink-0"></span>
        @include('components.navbarFoot',[ "page" => "contact"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
    </body>

    <form action="{{ route('user.contact-support') }}" method="post">
            @csrf
            <input type="text" name="fullname" value="wildan">
            <br>
            <input type="text" name="email" value="wildanofficial32@gmail.com">
            <br>
            <input type="text" name="subject" value="butuh bantuan cuk">
            <br>
            <textarea name="message" id=""> cik, saya laper cik, bisakah saya minta makananannya?
            </textarea>
            <button>hubungi support dek</button>
        </form>


</html>