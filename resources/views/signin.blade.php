@php
    $placeImg = "https://placehold.co/400";
@endphp

<!DOCTYPE html>
<html lang="en">
    @include('components.header' )

    <body>
        <div class="wrapper-user login">
            <div class="bg-view">
                <img src="{{ $placeImg }}" alt="">
            </div>

            <div class="container-login">
                <div class="wrapper-img">
                    <img src="{{ $placeImg }}" alt="">
                </div>

                <h2>Selamat Datang</h2>
                <h3>Masuk untuk pesan makan</h3>

                <div class="container-input">
                    <label for="email">Email</label>
                    <div class="wrapper-input">
                        <input type="email" id="email">
                    </div>
                </div>

                <div class="container-input">
                    <label for="pass">Password</label>
                    <div class="wrapper-input">
                        <input type="password" id="pass">
                        <button onclick="togglePass()"><img src="icons/show.svg" id="show" alt=""><img src="icons/hide.svg" id="hide" alt="" style="display: none;"></button>
                    </div>
                </div>

                <a href="/" class="submit">Login</a>
                <a href="/signup" class="signup">Doesn't have an account yet? click here</a>
            </div>
        </div>
    </body>

    <script>
        let showPass = 0

        function togglePass()
        {
            showPass = !showPass

            if (showPass)
            {
                document.getElementById("pass").type = "text"
                document.getElementById("hide").style.display = "block"
                document.getElementById("show").style.display = "none"
            }
            else
            {
                document.getElementById("pass").type = "password"
                document.getElementById("hide").style.display = "none"
                document.getElementById("show").style.display = "block"
            }
        }

    </script>
</html>