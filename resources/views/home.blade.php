@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body>
        @include('components.navbarHead',[ "page" => "home", "size" => "large"])

        <div class="wrapper-user home">
            <section class="main">
                <div class="left">
                    <h2>Makanan rumahan, sehat, enak, bersih.</h2>
                    <div class="gap1"></div>
                    <h1 class="text-yellow">Catering Harian</h1>
                    <h1>Tanpa Langganan</h1>
                    <div class="gap2"></div>
                    <a href="/menus">Lihat Menu <img src="icons/arrow-right-c.svg" alt="arrow image"></a>
                </div>
    
                <div class="right">
                    <img src="img/food.png" alt="">
                </div>
            </section>
    
            <section class="feature">
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="icons/calendar.svg" alt="">
                    </div>
    
                    <h2>Tanpa Berlangganan</h2>
    
                    <h3>Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</h3>
    
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="icons/book.svg" alt="">
                    </div>
                    <h2>Bebas Pilih Menu</h2>
                    <h3>Terdapat lebih dari 30 pilihan menu, bebas memilih menu.</h3>
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="icons/clock.svg" alt="">
                    </div>
                    <h2>Bebas Pilih Waktu</h2>
                    <h3>Waktu pengantaran fleksibel antara jam 10:00-18:00 WIB.</h3>
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
            </section>
    
            <span class="bar1"></span>
    
            <section class="popular">
                <h2>Menu Terpopuler Minggu Ini</h2>
    
                <div class="container-menu">
    
                    <a href="/" class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </a>
    
                    <a href="/" class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </a>
    
                    <a href="/" class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </a>
                
                </div>
    
                <a class="btn" href="/menus">Lihat Menu Lainnya <img src="icons/arrow-right-c.svg"></a>
    
            </section>
    
            <section class="packaging">
                <h1>Kemasan Paket Menu Homade</h1>
    
                <div class="wrapper-img">
                    <img src="img/packaging.png" alt="">
                    <h6>*Lauk pendamping dapat berubah sewaktu waktu</h6>
                </div>
    
                <div class="container-pack">
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="img/bento.png" alt="">
                        </div>
    
                        <h2>Bento Mealbox</h2>
    
                        <h3>paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</h3>
                    </div>
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="img/cardboard.png" alt="">
                        </div>
    
                        <h2>Valuebox</h2>
    
                        <h3>Paket hemat terdiri dari  laukutama dan laukpendamping terbatas (optional</h3>
                    </div>
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="img/plastic.png" alt="">
                        </div>
    
                        <h2>Family Pack</h2>
    
                        <h3>Paket keluarga terdiri dari lauk utama dan sayuran pendamping (tanpa nasi), porsi untuk 4 orang.</h3>
                    </div>
    
                </div>
    
                <div class="wrapper-button">
                    <button>Lihat Menu Selengkapnya <img src="icons/arrow-right-c.svg" alt=""></button>
                </div>
            </section>
    
            <section class="category">
                <h2>Kemasan Paket Homade</h2>
    
                <div class="container-category">
    
                    <button class="category-tabs">
                        <img src="img/chicken.png" alt="">
                        <h2>Ayam (23)</h2>
                    </button>
                    
                    <button class="category-tabs">
                        <img src="img/fish.png" alt="">
                        <h2>Ikan & Seafood (21)</h2>
                    </button>
    
                    <button class="category-tabs">
                        <img src="img/rice.png" alt="">
                        <h2>Nasi (7)</h2>
                    </button>
    
                    <button class="category-tabs">
                        <img src="img/beef.png" alt="">
                        <h2>Sapi & Kambing (4)</h2>
                    </button>
    
                </div>
            </section>
    
            <section class="sponsor">
                <div class="wrapper-sponsor">

                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
                    
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
                    
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
        
                    <div class="sponsor-tabs">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
                </div>
    
            </section>
        </div>

        @include('components.navbarFoot',[ "page" => "home"])
    </body>

</html>