@php
    $page;
    $size;
    $placeImg = "https://placehold.co/400";
@endphp

<nav class="nav-head {{ $size === "small" ? "small" : "" }}" id="navHead">
    <a href="/" class="wrapper-img">
        <img src="{{ $placeImg }}" alt="">
    </a>

    <div class="container-links">
        <a href="/" class="{{ $page === "home" ? "active" : "" }}">Home</a>
        <a href="/menu" class="{{ $page === "menu" ? "active" : "" }}">Menu</a>
        <a href="/schedule" class="{{ $page === "schedule" ? "active" : "" }}">Jadwal</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Profil</a>
        <a href="/" class="{{ $page === "wildan" ? "active" : "" }}">Kontak</a>
    </div>
</nav>

<script>
    let navbar = document.getElementById("navHead")

    @if ($size === "small")
    @else
    document.addEventListener('DOMContentLoaded', () => {
        const navbar = document.getElementById("navHead")

        window.addEventListener("scroll", () => {
            if (window.scrollY > 10) {
                navbar.classList.add("small")
            } else {
                navbar.classList.remove("small")
            }
        })
    })
    @endif
</script>