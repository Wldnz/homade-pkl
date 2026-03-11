<html>
    <html lang="en">
<!--begin::Head-->

<head>
	@vite([
		'sass/app.scss',
		'sass/metronic/style.scss',
		'resources/js/app.js',
		'resources/js/metronic/scripts.js'
	])
	<meta charset="utf-8" />
	<meta name="description"
		content="The most advanced Tailwind CSS & Bootstrap 5 Admin Theme with 40 unique prebuilt layouts on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel versions. Grab your copy now and get life-time updates for free." />
	<meta name="keywords"
		content="tailwind, tailwindcss, metronic, bootstrap, bootstrap 5, angular, VueJs, React, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel starter kits, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title"
		content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
	<meta property="og:url" content="https://keenthemes.com/metronic" />
	<meta property="og:site_name" content="Metronic by Keenthemes" />
	<link rel="canonical" href="http://preview.keenthemes.comindex.html" />
	<link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Vendor Stylesheets(used for this page only)-->
	<link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Vendor Stylesheets-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Global Stylesheets Bundle-->
	<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
	data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
	data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
	data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    @if(isset($response['status']) && $response['status'] === 'success')
    @php
        $data = $response['data'];
        $menu = $data['detail_menu'];
        $themes = $data['themes'];
        $categories = $data['categories'];
        $packages = $data['packages'];
        
        // Memastikan categories menu benar-benar menjadi array biasa, bukan collection
    @endphp

    <div class="container py-4">
        
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold"><i class="bi bi-pencil-square text-primary"></i> Edit Menu Catering</h3>
                    <p class="text-muted mb-0">Perbarui informasi dan gambar menu.</p>
                    <a href="{{ route('admin.menus') }}">back</a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.edit-menu', [ 'id' => $menu['id'] ] ) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <label class="form-label fw-bold">Preview Gambar Saat Ini</label>
                            
                            <div class="position-relative mb-3">
                                <img src="{{ $menu['image_url'] }}" 
                                     alt="{{ $menu['name'] }}" 
                                     class="img-fluid rounded shadow-sm w-100 object-fit-cover" 
                                     style="max-height: 250px;"
                                     onerror="this.onerror=null; this.src='https://placehold.co/400x250/FFEEEE/DC3545?text=Gambar+Rusak'; document.getElementById('img-warning').classList.remove('d-none');">
                                
                                <div id="img-warning" class="position-absolute top-0 start-0 m-2 badge bg-danger d-none shadow-sm">
                                    <i class="bi bi-exclamation-triangle"></i> Gagal memuat gambar
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label text-muted small">Upload Gambar Baru (Opsional)</label>
                                <input class="form-control form-control-sm" type="file" id="image" name="image" accept="image/*">
                            </div>

                            <div class="p-3 bg-light rounded border">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" {{ old('status_active',$menu['is_active'] ? 'active' : 'non-active') === 'active' ? 'checked' : '' }}
                                        onchange="handler_status_active(this)"
                                    >
                                    <input type="hidden" name="status_active" id="status_active" value="{{ old('status_active') ?? $menu['is_active'] ? 'active' : 'non-active' }}">
                                    <label class="form-check-label fw-bold ms-2" for="is_active">Tampilkan di Website</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama Menu</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $menu['name'] }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required>{{ $menu['description'] }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="theme_id" class="form-label fw-bold"><i class="bi bi-palette"></i> Theme</label>
                                    <select class="form-select" id="theme_id" name="theme_id" required>
                                        <option value="">Pilih Theme...</option>
                                        @foreach($themes as $theme)
                                            <option value="{{ $theme['id'] }}" {{  ($menu['theme_id'] )== $theme['id'] ? 'selected' : '' }}>
                                                {{ $theme['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold"><i class="bi bi-tags"></i> Kategori</label>
                                    <div class="p-2 border rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                                        @foreach($categories as $cat)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $cat['id'] }}" id="cat_{{ $cat['id'] }}" 
                                                    {{ in_array($cat['id'], $menu['categories']->toArray()) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cat_{{ $cat['id'] }}">
                                                    {{ $cat['name'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                         <div class="mt-3 p-3 border rounded">
                                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle-dotted"></i> Addons / Pelengkap</h6>
                                <div class="row gap-2">
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <label class="form-label small text-muted">Lauk Sampingan</label>
                                        <input type="text" class="form-control form-control-sm" name="side_dish" value="{{ $menu['addon']['side_dish'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <label class="form-label small text-muted">Sayuran / Lalapan</label>
                                        <input type="text" class="form-control form-control-sm" name="vegetable" value="{{ $menu['addon']['vegetable'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Saus / Sambal</label>
                                        <input type="text" class="form-control form-control-sm" name="sauce" value="{{ $menu['addon']['sauce'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Buah - Buahan</label>
                                        <input type="text" class="form-control form-control-sm" name="fruit" value="{{ $menu['addon']['fruit'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                        <div class="mt-3 p-3 border rounded">
                                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle-dotted"></i> Paket - Paket Yang Akan Hadir</h6>
                                <div class="row">
                                    @foreach ($menu['packages'] as $index=>$package)
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <label class="form-label small text-muted">{{ $package['name'] }} (price)</label>
                                            <input type="hidden" class="form-control form-control-sm" name="packages[{{ $index }}][package_id]" value="{{ $package['package_id'] }}">
                                            <input type="text" class="form-control form-control-sm" name="packages[{{ $index }}][price]" value="{{ $package['price']  }}">
                                        </div>
                                        @php
                                            $packages = array_filter($packages, function($p) use($package){
                                                return $p['id'] != $package['package_id'];
                                            });
                                        @endphp
                                    @endforeach
                                    @foreach ($packages as $index=>$package)
                                    @php
                                        $current_package_index = $index + count($menu['packages'])
                                    @endphp
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <label class="form-label small text-muted">{{ $package['name'] }} (price)</label>
                                            <input type="hidden" class="form-control form-control-sm" name="packages[{{ $current_package_index }}][package_id]" value="{{ $package['id'] }}">
                                            <input type="text" class="form-control form-control-sm" name="packages[{{ $current_package_index }}][price]" value="{{ 0 }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3 px-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

@else
    <div class="container py-5">
        <div class="alert alert-danger d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
            <div>
                <strong>Gagal Memuat Data!</strong> Pastikan response API berhasil (Status 200). Pesan sistem: {{ $response['message'] ?? 'Data tidak ditemukan.' }}
            </div>
        </div>
    </div>
    {{ dd($response) }}
@endif

<script defer>
    function handler_status_active(e) {
        document.getElementById('status_active').value = e.checked? 'active' : 'non-active';
    }
</script>

<div class="mt-20">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div>

</body>
</html>