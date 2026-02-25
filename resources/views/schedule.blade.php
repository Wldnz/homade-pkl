@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body>
        @include('components.navbarHead',[ "page" => "schedule", "size" => "small"])

        <div class="wrapper-user schedule">
            <h1 class="title">Jadwal Menu</h1>

            <div class="calendar">
                <div class="header">
                    <div class="left">
                        <h2>Jadwal Menu Harian Homade Catering</h2>
                    </div>
                    <div class="right">
                        <div class="input">
                            <button class="left"><img src="icons/caret-arrow-left.svg" alt=""></button>
                            <h3 class="date"><img src="icons/calendar.svg" alt=""> 9 Feb - 15 Feb 2026</h3>
                            <button class="right"><img src="icons/caret-arrow-right.svg" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="body">
                    <div class="day mon">

                        <div class="day-info">
                            <h2>Senin</h2>
                            <h3>Feb 9</h3>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu A</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu B</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="day tue">

                        <div class="day-info">
                            <h2>Selasa</h2>
                            <h3>Feb 10</h3>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu A</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu B</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="day wed">

                        <div class="day-info">
                            <h2>Rabu</h2>
                            <h3>Feb 11</h3>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu A</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu B</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="day thu">

                        <div class="day-info">
                            <h2>Kamis</h2>
                            <h3>Feb 12</h3>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu A</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu B</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="day fri">

                        <div class="day-info">
                            <h2>Jumat</h2>
                            <h3>Feb 13</h3>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu A</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                        <div class="container-menu">
                            <div class="menu">
                                <div class="wrapper-img">
                                    <img src="{{ $placeImg }}" alt="">
                                    <span>Menu B</span>
                                </div>
                                <div class="food-info">
                                    <h3>Ayam Goreng Maknyus</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="footer">

                    <div class="container info">
                        <h2><img src="icons/info.svg" alt="">Informasi Catering</h2>

                        <h3>Pemesanan Maksimal Hari H-1 jam 15.00</h3>
                        <h3>Pengiriman area jakarta & sekitarnya.</h3>
                        <h3>Semua bahan  segar dan halal 100%</h3>
                    </div>

                    <div class="container contact">
                        <h2><img src="icons/phone.svg" alt="">Contact</h2>

                        <h4>Whatsapp Customer Care</h4>
                        <h3><img src="icons/chat-bubble.svg" alt="">0857-1180-1336</h3>
                        <h4>Senin - Jumat 9:00-17.00 WIB</h4>
                    </div>

                    <div class="container address">
                        <h2><img src="icons/building.svg" alt="">Alamat</h2>

                        <h3>PT. Homade Kreatif Teknologi Jl.Tebet Timur Dalam VI No.3, RT.008  RW.011 Kel. Tebet Timur, Kec.Tebet, Jakarta Selatan, Jakarta 12820 <br>Telp. 0857-1180-1336.</h3>
                    </div>
                </div>
            </div>
        </div>

        @include('components.navbarFoot',[ "page" => "home"])
    </body>

</html>