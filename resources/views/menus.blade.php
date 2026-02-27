@php
    $placeImg = "https://placehold.co/400";
    $category = request('category')
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body class="d-flex flex-column">
        @include('components.navbarHead',[ "page" => "menu", "bg" => ""])
        
            <div class="d-flex w-100 h-75 flex-shrink-0">
                <div class="d-flex w-100 h-75 align-items-center justify-content-center gap-5 flex-column flex-shrink-0">
                    <p class="fsc-6 fw-bolder text-white mb-5">Menu</p>
                    <div class="d-flex w-75 h-50 gap-5 mt-5">
                        <a href="/menus?category=chicken" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-chicken">Ayam (99)</a>
                        <a href="/menus?category=fish" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-fish">Ikan & Seafood (99)</a>
                        <a href="/menus?category=rice" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-rice">Nasi (99)</a>
                        <a href="/menus?category=beef" class="d-flex w-100 h-100 align-items-center justify-content-center text-white fs-3 bg-beef">Sapi & Kambing (99)</a>
                    </div>
                </div>
            </div>

            <div class="bg-menu">
                <img src="img/food2.webp" alt="" class="w-100 h-100 object-fit-cover">
            </div>

            <div class="d-flex w-100 h-50 flex-shrink-0 bg-white align-items-center flex-column justify-content-between pt-5 pb-5" id="category">

                <div class="d-flex h-40px w-100 justify-content-center gap-5">
                    
                    <a href="/menus#category" class="d-flex {{ !$category ? 'bg-accent' : '' }} h-100 align-items-center pe-5 ps-5 rounded-pill">
                        <p class="fs-2 {{ !$category ? 'text-white' : 'text-grey' }} mb-0 fw-bold">ALL</p>
                    </a>

                    <a href="/menus?category=chicken#category" class="d-flex {{ $category == "chicken" ? 'bg-accent' : '' }} h-100 align-items-center pe-5 ps-5 rounded-pill">
                        <p class="fs-2 {{ $category == "chicken" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Ayam</p>
                    </a>

                    <a href="/menus?category=fish#category" class="d-flex {{ $category == "fish" ? 'bg-accent' : '' }} h-100 align-items-center pe-5 ps-5 rounded-pill">
                        <p class="fs-2 {{ $category == "fish" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Ikan & Seafood</p>
                    </a>

                    <a href="/menus?category=rice#category" class="d-flex {{ $category == "rice" ? 'bg-accent' : '' }} h-100 align-items-center pe-5 ps-5 rounded-pill">
                        <p class="fs-2 {{ $category == "rice" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Nasi</p>
                    </a>

                    <a href="/menus?category=beef#category" class="d-flex {{ $category == "beef" ? 'bg-accent' : '' }} h-100 align-items-center pe-5 ps-5 rounded-pill">
                        <p class="fs-2 {{ $category == "beef" ? 'text-white' : 'text-grey' }} mb-0 fw-bold">Sapi & Kambing</p>
                    </a>
                </div>

                <div class="d-flex h-50px w-100 justify-content-center align-items-center">
                    <div class="w-90 h-100 d-flex justify-content-between">

                        <div class="h-100 w-50 bg-light-grey rounded d-flex align-items-center pe-5 ps-5 gap-5 cursor-text" onclick="focusInput()">
                            <div class="d-flex h-50 ratio-1"> <img src="icons/search.svg" class="w-100 h-100" alt=""></div>
                            <input type="text" class="w-100 h-100 fs-2" id="searchInput" placeholder="cari menu...">
                        </div>

                        <div class="h-100 w-25 bg-light-grey rounded pe-5 ps-5">
                            <select name="" id="" class="w-100 h-100 border-0 outline-0 bg-transparent fs-4">
                                <option value="" selected>Semua Tema Menu</option>
                                <option value="">Wildan</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <p class="w-100 fs-3 text-center">Porsinya pas, buat perut kenyang, aktifitas jadi lancar.</p>
                

            </div>

            <div class="d-flex w-100 flex-shrink-0 align-items-md-center justify-content-center">
                <div class="d-flex w-90 flex-shrink-0 flex-column">
                    <p class="fsc-5 homade-underline">{{ !$category ? 'Menu' : '' }}{{ $category == "chicken" ? 'Ayam' : '' }}{{ $category == "fish" ? 'Ikan & Seafood' : '' }}{{ $category == "rice" ? 'Nasi' : '' }}{{ $category == "beef" ? 'Sapi & Kambing' : '' }}</p>
                    <p class="fs-2">99 menu masakan {{ $category == "chicken" ? 'ayam' : '' }}{{ $category == "fish" ? 'ikan & seafood' : '' }}{{ $category == "rice" ? 'nasi' : '' }}{{ $category == "beef" ? 'sapi & kambing' : '' }} enak ala homade</p>
                </div>
            </div>

            <div class="d-flex w-100 h-150 flex-shrink-0 align-items-center justify-content-center">
                
                <div class="d-grid grid-template-homade w-90 h-100 flex-wrap flex-shrink-0 gap-5">

                    @foreach (range(1, 8) as $i)
                    <a href="menus/9" class="d-flex flex-column gap-5 div-{{ $i }}">

                        <div class="w-100 h-75">
                            <img src="{{ $placeImg }}" class="w-100 h-100 object-fit-cover">
                        </div>

                        <div class="w-100 h-25">
                            <p class="fs-1 fw-bold text-center">Ayam Geprek Wildan Maknyus Meletus</p>
                        </div>
                        
                    </a>
                    @endforeach
                    
                </div>
            
            </div>

            <div class="d-flex w-100 h-25 flex-shrink-0"></div>

        @include('components.navbarFoot',[ "page" => "menu"])
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
        <script>

            function focusInput() {
                document.getElementById("searchInput").focus();
            }

        </script>
    </body>

</html>