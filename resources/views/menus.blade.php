@php
    $placeImg = "https://placehold.co/400";
    $filter = request('filter')
@endphp
    {{ dd($response) `}}

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "menu", "bg" => ""])
        
            <div class="d-flex w-100 h-75 flex-shrink-0">
                <div class="d-flex w-100 h-75 align-items-center justify-content-center gap-5 flex-column flex-shrink-0">
                    <p class="fsc-6 fw-bolder text-white mb-5">Menu</p>
                    <div class="d-flex w-75 h-50 gap-5 mt-5">
                        <a href="/menus?filter=chicken" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-chicken">Ayam (99)</a>
                        <a href="/menus?filter=fish" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-fish">Ikan & Seafood (99)</a>
                        <a href="/menus?filter=rice" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-rice">Nasi (99)</a>
                        <a href="/menus?filter=beef" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-beef">Sapi & Kambing (99)</a>
                    </div>
                </div>
            </div>

            <div class="bg-menu">
                <img src="img/food2.webp" alt="" class="w-100 h-100 object-fit-cover">
            </div>

            <div class="d-flex w-100 h-50 flex-shrink-0 bg-white align-items-center" id="filter">
                <div class="d-flex h-40px w-100 justify-content-center gap-5">
                    
                    <a href="/menus#filter" class="d-flex {{ !$filter ? 'bg-accent' : '' }} h-100 align-items-center p-0-20px rounded-pill">
                        <p class="fs-2 {{ !$filter ? 'text-white' : 'text-grey' }} mb-0 fw-bold">ALL</p>
                    </a>

                    <a href="/menus?filter=chicken#filter" class="d-flex {{ $filter == "chicken" ? 'bg-accent' : '' }} h-100 align-items-center p-0-20px rounded-pill">
                        <p class="fs-2 {{ $filter == "chicken" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Ayam</p>
                    </a>

                    <a href="/menus?filter=fish#filter" class="d-flex {{ $filter == "fish" ? 'bg-accent' : '' }} h-100 align-items-center p-0-20px rounded-pill">
                        <p class="fs-2 {{ $filter == "fish" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Ikan & Seafood</p>
                    </a>

                    <a href="/menus?filter=rice#filter" class="d-flex {{ $filter == "rice" ? 'bg-accent' : '' }} h-100 align-items-center p-0-20px rounded-pill">
                        <p class="fs-2 {{ $filter == "rice" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Nasi</p>
                    </a>

                    <a href="/menus?filter=beef#filter" class="d-flex {{ $filter == "beef" ? 'bg-accent' : '' }} h-100 align-items-center p-0-20px rounded-pill">
                        <p class="fs-2 {{ $filter == "beef" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Sapi & Kambing</p>
                    </a>
                </div>
            </div>

        @include('components.navbarFoot',[ "page" => "menu"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
    </body>

</html>