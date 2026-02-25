<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: MetronicProduct Version: 8.3.2
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head>
<base href="../../../" />
        @vite([
                    'sass/app.scss', 
                    'sass/metronic/style.scss', 
        ])
		<title>Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes</title>
		<meta charset="utf-8" />
		<meta name="description" content="The most advanced Tailwind CSS & Bootstrap 5 Admin Theme with 40 unique prebuilt layouts on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel versions. Grab your copy now and get life-time updates for free." />
		<meta name="keywords" content="tailwind, tailwindcss, metronic, bootstrap, bootstrap 5, angular, VueJs, React, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel starter kits, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
		<meta property="og:url" content="https://keenthemes.com/metronic" />
		<meta property="og:site_name" content="Metronic by Keenthemes" />
		<link rel="canonical" href="http://preview.keenthemes.comauthentication/layouts/fancy/sign-in.html" />
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">

    @if (session()->has('message'))
        {{ dd(session()->all()) }}
    @endif

		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->

		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid bg-grey">
				<!--begin::Logo-->
				<!--end::Logo-->
				<!--begin::Aside-->
				<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
					<!--begin::Wrapper-->
					<div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
						<!--begin::Header-->
						<div class="d-flex flex-stack py-2">
							<!--begin::Back link-->
							<div class="me-2"></div>
							<!--end::Back link-->
						</div>
						<!--end::Header-->
						<!--begin::Body-->
						<div class="py-20">
							<!--begin::Form-->
							<form class="form w-100" novalidate="novalidate" id="form" action="/signin">
                            @csrf
								<!--begin::Body-->
								<div class="card-body">
									<!--begin::Heading-->
									<div class="text-start mb-10 align-items-center d-flex flex-column">
										<!--begin::Title-->
                                        <img src="img/logo.svg" class="w-50 mb-5" alt="">
                                        <div class="mt-5 mb-5"></div>
										<h1 class="text-white mb-3 fs-3x text-center w-100" data-kt-translate="sign-in-title">Daftar</h1>
										<h3 class="text-white mb-3 fs-1x fw-light text-center w-100" data-kt-translate="sign-in-title">Nikmati menu harian</h3>
										<!--end::Title-->
									</div>
									<!--begin::Heading-->
									<!--begin::Input group=-->
									<div class="fv-row mb-8 d-flex gap-5">
										<input required type="text" placeholder="First Name" id="firstName" name="first_name" autocomplete="off" class="form-control form-control-solid" />
										<input required type="text" placeholder="Last Name" id="lastName" name="last_name" autocomplete="off" class="form-control form-control-solid" />
									</div>
									<div class="fv-row mb-8">
										<!--begin::Email-->
										<input required type="text" placeholder="Email" id="email" name="email" autocomplete="off" data-kt-translate="sign-in-input-email" class="form-control form-control-solid" />
										<!--end::Email-->
									</div>
									<!--end::Input group=-->
									<div class="fv-row mb-7">
										<!--begin::Password-->
                                        <div class="d-flex form-control">
                                            <input required type="password" placeholder="Password" id="password" name="password" autocomplete="off" data-kt-translate="sign-in-input-password" class="w-100" />
                                            <button type="button" onclick="showPass()">
                                                <img src="icons/show.svg" alt="" class="img-accent" id="show">
                                                <img src="icons/hide.svg" alt="" class="img-accent d-none" id="hide">
                                            </button>
                                        </div>
										<!--end::Password-->
									</div>
									
                                    <div class="fv-row mb-7">
										<!--begin::Password-->
                                        <div class="d-flex form-control">
                                            <input required type="password" placeholder="Confirm Password" id="conPassword" name="password" autocomplete="off" data-kt-translate="sign-in-input-password" class="w-100" />
                                            <button type="button" onclick="showConPass()">
                                                <img src="icons/show.svg" alt="" class="img-accent" id="conShow">
                                                <img src="icons/hide.svg" alt="" class="img-accent d-none" id="conHide">
                                            </button>
                                        </div>
										<!--end::Password-->
									</div>
									<!--end::Input group=-->
									<!--begin::Wrapper-->
									<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-0">
										<div></div>
									</div>
									<!--end::Wrapper-->
									<!--begin::Actions-->
									<div class="d-flex flex-stack">
										<!--begin::Submit-->
										<button id="kt_sign_in_submit" class="btn-primary-homade w-100 p-4 rounded">
											<!--begin::Indicator label-->
											<span class="indicator-label text-white fw-medium" data-kt-translate="sign-in-submit">Masuk</span>
											<!--end::Indicator label-->
											<!--begin::Indicator progress-->
											<span class="indicator-progress">
												<span data-kt-translate="general-progress">Please wait...</span>
												<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
											</span>
											<!--end::Indicator progress-->
										</button>
                                        <div class="m-0">
										<!--end::Submit-->
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <div class="d-flex w-100 justify-content-center mt-2 align-items-center">
                                        <span class="text-gray-500 fw-bold fs-5 me-2" data-kt-translate="sign-in-head-desc">Sudah punya akun?</span>
                                        <a href="signin" class="fw-bold fs-5 text-accent" data-kt-translate="sign-in-head-link">Masuk Sekarang!</a>
                                    </div>
                                </div>
								<!--begin::Body-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Body-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Aside-->
				<!--begin::Body-->
				<div class="d-none d-lg-flex flex-lg-row-fluid w-50 bgi-size-cover bgi-position-y-center bgi-position-x-start bgi-no-repeat" style="background-image: url(img/img1.jpg)"></div>
				<!--begin::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->
		<!--begin::Javascript-->
        <script>

            const firstInput = document.getElementById("firstName")
            const lastInput = document.getElementById("lastName")
            const emailInput = document.getElementById("email")
            const passInput = document.getElementById("password")
            const conPassInput = document.getElementById("conPassword")
            const form = document.getElementById('form')

            let passwordShown = 0
            const showIcon = document.getElementById("show")
            const hideIcon = document.getElementById("hide")

            let conPasswordShown = 0
            const conshowIcon = document.getElementById("conShow")
            const conhideIcon = document.getElementById("conHide")

            function showPass()
            {
                passwordShown = !passwordShown

                if (passwordShown)
                {
                    passInput.type = "text"
                    showIcon.classList.remove("d-block")
                    showIcon.classList.add("d-none")
                    hideIcon.classList.add("d-block")
                    hideIcon.classList.remove("d-none")
                }
                
                else
                {
                    passInput.type = "password"
                    showIcon.classList.add("d-block")
                    showIcon.classList.remove("d-none")
                    hideIcon.classList.remove("d-block")
                    hideIcon.classList.add("d-none")
                }
            }
        
            function showConPass()
            {
                conPasswordShown = !conPasswordShown

                if (conPasswordShown)
                {
                    conPassInput.type = "text"
                    conshowIcon.classList.remove("d-block")
                    conshowIcon.classList.add("d-none")
                    conhideIcon.classList.add("d-block")
                    conhideIcon.classList.remove("d-none")
                }
                
                else
                {
                    conPassInput.type = "password"
                    conshowIcon.classList.add("d-block")
                    conshowIcon.classList.remove("d-none")
                    conhideIcon.classList.remove("d-block")
                    conhideIcon.classList.add("d-none")
                }
            }
        

            form.addEventListener('submit', (e) => {
                if (passInput.value !== conPassInput.value) {
                    e.preventDefault()
                    alert("Passwords do not match!")
                    return false
                }

                if (!firstInput.value || !lastInput.value || !emailInput.value || !passInput.value || !conPassInput.value) {
                    e.preventDefault()
                    alert("Please fill all the input field!")
                    return false
                }
            })
            </script>

            
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>