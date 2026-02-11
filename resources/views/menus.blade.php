@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body>
        @include('components.navbarHead',[ "page" => "menu", "size" => "small"])

        <div class="wrapper-user menu">
    
            <section class="category">
                <h2 class="title">Menu</h2>
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

            <section class="searchbar">
                <div class="container-search">
                    <input type="text" placeholder="Search Menu...">
                    <button><img src="icons/search.svg" alt=""></button>
                </div>
            </section>

            <section class="food ayam">
                <h1>Porsinya pas, buat perut kenyang, aktifitas jadi lancar.</h1>
                <h2>Ayam</h2>
                <h3>23 menu masakan ayam enak ala homade.</h3>
                <div class="container-food">
                    @foreach (range(1, 5) as $i)
                    <div class="food-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
                        <h4>Ayam</h4>
                    </div>
                    @endforeach
                </div>
            </section>

            @foreach (range(1, 3) as $j)
            <section class="food ayam">
                <h2>Ayam</h2>
                <h3>23 menu masakan ayam enak ala homade.</h3>
                <div class="container-food">
                    @foreach (range(1, 5) as $i)
                    <div class="food-tabs">
                        <div class="wrapper-img">
                            <img src="{{ $placeImg }}" alt="">
                        </div>
                        <h4>Ayam</h4>
                    </div>
                    @endforeach
                </div>
            </section>
            @endforeach
    
        </div>

        @include('components.navbarFoot',[ "page" => "home"])
    </body>

</html>