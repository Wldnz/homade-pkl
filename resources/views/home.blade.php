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
                    <h1>Catering Harian</h1>
                    <h1>Tanpa Langganan</h1>
                    <div class="gap2"></div>
                    <button>Lihat Menu <img src="icons/arrow-right.svg" alt="arrow image"></button>
                </div>
    
                <div class="right">
    
                </div>
            </section>
    
            <section class="feature">
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
    
                    <h2>Tanpa Berlangganan</h2>
    
                    <h3>Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</h3>
    
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
                    <h2>Tanpa Berlangganan</h2>
                    <h3>Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</h3>
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
                <div class="feature-tab">
                    <div class="wrapper-img">
                        <img src="{{ $placeImg }}" alt="">
                    </div>
                    <h2>Tanpa Berlangganan</h2>
                    <h3>Tanpa kontrak langganan, bisa pesan sesuai kebutuhan.</h3>
                    <h4>syarat & ketentuan berlaku</h4>
                </div>
    
            </section>
    
            <span class="bar1"></span>
    
            <section class="popular">
                <h2>Menu Terpopuler Minggu Ini</h2>
    
                <div class="container-menu">
    
                    <div class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </div>
    
                    <div class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </div>
    
                    <div class="menu-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h3>Ayam Geprek</h3>
                    </div>
                
                </div>
    
                <button>Lihat Menu Lainnya</button>
    
            </section>
    
            <section class="packaging">
                <h1>Kemasan Paket Menu Homade</h1>
    
                <div class="wrapper-img">
                    <img src="{{ $placeImg }}" alt="">
                </div>
    
                <div class="container-pack">
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h2>Bento Mealbox</h2>
    
                        <h3>paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</h3>
                    </div>
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h2>Bento Mealbox</h2>
    
                        <h3>paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</h3>
                    </div>
    
                    <div class="pack">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
    
                        <h2>Bento Mealbox</h2>
    
                        <h3>paket normal terdiri dari lauk utama dan lauk pendamping lengkap.</h3>
                    </div>
    
                </div>
    
                <div class="wrapper-button">
                    <button>Lihat Menu Selengkapnya <img src="icons/arrow-right.svg" alt=""></button>
                </div>
            </section>
    
            <section class="category">
                <h2>Kemasan Paket Homade</h2>
    
                <div class="container-category">
    
                    <button class="category-tabs">
                        <img src="{{ $placeImg }}" alt="">
                        <h2>Ayam (40)</h2>
                    </button>
                    
                    <button class="category-tabs">
                        <img src="{{ $placeImg }}" alt="">
                        <h2>Ayam (40)</h2>
                    </button>
    
                    <button class="category-tabs">
                        <img src="{{ $placeImg }}" alt="">
                        <h2>Ayam (40)</h2>
                    </button>
    
                    <button class="category-tabs">
                        <img src="{{ $placeImg }}" alt="">
                        <h2>Ayam (40)</h2>
                    </button>
    
                </div>
            </section>
    
            <section class="sponsor">
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
    
            </section>
        </div>

        @include('components.navbarFoot',[ "page" => "home"])
    </body>

</html>