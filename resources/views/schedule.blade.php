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
                    <div class="day mon"></div>
                    <div class="day tue"></div>
                    <div class="day wed"></div>
                    <div class="day thu"></div>
                    <div class="day fri"></div>
                </div>

                <div class="footer">
                    <div class="container info"></div>
                    <div class="container contact"></div>
                    <div class="container address"></div>
                </div>
            </div>
        </div>

        @include('components.navbarFoot',[ "page" => "home"])
    </body>

</html>