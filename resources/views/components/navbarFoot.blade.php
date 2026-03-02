@php
    $page;
    $placeImg = "https://placehold.co/400";
@endphp

<nav class="bg-nav h-90 flex-shrink-0 d-flex flex-column align-items-center justify-content-center">
    <div class="d-flex mb-5 w-100 gap-5 h-40px align-items-center justify-content-center">

        <a href="/" class="{{ $page == "home" ? 'text-yellow' : 'text-white fw-light' }} fsc-5 me-3">Home</a>
        <a href="/menus" class="{{ $page == "menu" ? 'text-yellow' : 'text-white fw-light' }} fsc-5 me-3">Menu</a>
        <a href="/schedule" class="{{ $page == "schedule" ? 'text-yellow' : 'text-white fw-light' }} fsc-5 me-3">Jadwal</a>
        <a href="/profile" class="{{ $page == "profile" ? 'text-yellow' : 'text-white fw-light' }} fsc-5 me-3">Profil</a>
        <a href="/" class="{{ $page == "contact" ? 'text-yellow' : 'text-white fw-light' }} fsc-5 me-3">Kontak</a>
        
        <a href="/" class="h-100 ratio-1"><img src="{{ $placeImg }}" class="rounded-circle" alt=""></a>
    </div>

    <div class="d-flex mt-5 w-100 gap-1 h-25 align-items-center justify-content-center flex-column">
        <p class="fs-2 flex-shrink-0 mb-0 text-white">Whatsapp Customer Care</p>
        <p class="fsc-6 h-100 align-items-center d-flex fw-bolder text-yellow gap-3 mb-0"><img src="icons/whatsapp.svg" class="h-1em" alt=""> 0857-1180-1336</p>
        <p class="fs-5 flex-shrink-0 mb-0 fw-light text-white">(Senin - Jumat 9:00-17:00 WIB)</p>
    </div>

    <div class="d-flex w-100 mb-5 flex-column">
        <p class="text-center text-white w-100 fsc-7 fw-bolder mb-0 h-auto">Homade Catering Jakarta</p>
        <p class="text-center text-white w-100 fs-3 fw-light">Catering harian tanpa langganan, masakan rumahan, hemat, enak, bersih di Jakarta</p>
    </div>

    <div class="d-flex w-100 h-40px align-items-center mt-5 mb-5 justify-content-center">
        <div class="d-flex h-100 gap-5">
            <a href="/" class="d-flex h-100 ratio-1 bg-white align-items-center rounded-circle justify-content-center"><img class="w-50 h-50" src="icons/facebook.svg" alt=""></a>
            <a href="/" class="d-flex h-100 ratio-1 bg-white align-items-center rounded-circle justify-content-center"><img class="w-50 h-50" src="icons/x.svg" alt=""></a>
            <a href="/" class="d-flex h-100 ratio-1 bg-white align-items-center rounded-circle justify-content-center"><img class="w-50 h-50" src="icons/instagram.svg" alt=""></a>
        </div>
    </div>
    
    <div class="d-flex w-100 mb-5 flex-column mt-3">
        <p class="text-center text-white w-100 fs-3">© Copyright 2017 - 2026 | Homade Kreatif Teknologi</p>
    </div>
</nav>
