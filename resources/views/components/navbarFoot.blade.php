@php
    $page;
    $placeImg = "https://placehold.co/400";
@endphp

<nav class="nav-foot">
    <div class="container-links">
        <a href="/" class="{{ $page === "home" ? "active" : "" }}">Home</a>
        <a href="/" class="{{ $page === "menu" ? "active" : "" }}">Menu</a>
        <a href="/" class="{{ $page === "schedule" ? "active" : "" }}">Jadwal</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Profil</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Faq</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Blog</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Kontak</a>
    </div>

    <div class="container-whatsapp">
        <h2>Whatsapp Customer Care</h2>
        <h1><img src="{{ $placeImg }}" alt=""> 0857-1180-1336</h1>
        <h3>(Senin - Jumat 9:00 - 17:00 WIB)</h3>
    </div>

    <div class="container-title">
        <h1>Homade Catering Jakarta</h1>
        <h2>Catering harian tanpa langganan, masakan rumahan, hemat, enak, bersih di Jakarta</h2>
    </div>

    <div class="container-media">
        <button class="wrapper-img">
            <img src="{{ $placeImg }}" alt="">
        </button>
        <button class="wrapper-img">
            <img src="{{ $placeImg }}" alt="">
        </button>
        <button class="wrapper-img">
            <img src="{{ $placeImg }}" alt="">
        </button>
    </div>

    <div class="container-copyright">
        <h3>© Copyright 2017 - 2026</h3>
        <div class="span"></div>
        <h3>Homade Kreatif Teknologi</h3>
    </div>
</nav>
