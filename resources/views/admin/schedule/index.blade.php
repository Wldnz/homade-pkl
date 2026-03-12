<html>
    <html lang="en">
<!--begin::Head-->

<head>
	@vite([
		'sass/app.scss',
		'sass/metronic/style.scss',
		'resources/js/app.js',
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
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
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
    $menus = $data['menus'] ?? [];
    $schedules = $data['schedules'] ?? [];
    
    // Mapping jadwal agar mudah diakses berdasarkan tanggal (DD-MM-YYYY)
    $mappedSchedules = [];
    foreach($schedules as $key => $schedule) {
        $mappedSchedules[$schedule['date']] = $schedule['menus'];
    }

    // Generate 5 Hari (Senin - Jumat) dari startOfWeek
    $days = [];
    if(isset($data['start_of_week'])) {
        $start_of_week = \Carbon\Carbon::parse($data['start_of_week']);
        for ($i = 0; $i < 5; $i++) {
            $date = $start_of_week->copy()->addDays($i);
            $days[] = [
                // Menggunakan format bahasa indonesia jika app locale sudah di set, atau fallback ke format standar
                'day_name' => $date->translatedFormat('l'), 
                'date_formatted' => $date->translatedFormat('d F Y'),
                'date_key' => $date->format('d-m-Y'),
            ];
        }
		}
@endphp

<style>
    /* Custom Styling untuk Slot Menu */
    .slot-empty {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.2s;
        cursor: pointer;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slot-empty:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    
    .slot-filled {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        min-height: 120px;
    }
    .slot-filled img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    .slot-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        gap: 10px;
    }
    .slot-filled:hover .slot-overlay {
        opacity: 1;
    }
    .menu-title-badge {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 4px 8px;
        font-size: 0.8rem;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="container-fluid py-4">

    <form action="{{ route('admin.add-or-update-schedules') }}" method="POST">
        @csrf
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold"><i class="bi bi-calendar-week text-primary"></i> Jadwal Menu Catering</h3>
                <p class="text-muted mb-0">Kelola menu mingguan (Senin - Jum'at)</p>
				<input type="hidden" name="start_of_week" value={{ $data['start_of_week'] }}>
				<input type="hidden" name="end_of_week" value={{ $data['end_of_week'] }}>
            </div>
            
            <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
                <div class="btn-group shadow-sm">
                    <a href="?week={{ request('week', $data['current_week']) - 1 }}" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                    <span class="btn btn-light border-secondary text-dark fw-bold" style="pointer-events: none;">
                        Minggu ke-{{ request('week', $data['current_week']) }}
                    </span>
                    <a href="?week={{ request('week', $data['current_week']) + 1 }}" class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
                </div>
                
                <button type="submit" class="btn btn-dark shadow-sm px-4">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-3">
            @foreach($days as $day)
                @php
                    // Ambil menu untuk hari ini, maksimal 2 slot
                    $dayMenus = $mappedSchedules[$day['date_key']] ?? [];
                    $slots = [
                        $dayMenus[0] ?? null,
                        $dayMenus[1] ?? null
                    ];
                @endphp
                
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header text-center bg-white border-bottom pb-2 pt-3">
                            <h6 class="fw-bold mb-1">{{ $day['day_name'] }}</h6>
                            <small class="text-muted">{{ $day['date_formatted'] }}</small>
                        </div>
                        <div class="card-body p-2 d-flex flex-column gap-2" id="container-{{ $day['date_key'] }}">
                            
                            @foreach($slots as $index => $menu)
                                <div id="slot-{{ $day['date_key'] }}-{{ $index }}">
                                    @if($menu)
                                        <div class="slot-filled shadow-sm">
                                            <input type="hidden" name="schedules[{{ $day['date_key'] }}][]" value="{{ $menu['id'] }}">
                                            
                                            <img src="{{ $menu['image_url'] }}" loading="lazy" alt="Menu" onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                                            <div class="menu-title-badge">{{ $menu['name'] }}</div>
                                            
                                            <div class="slot-overlay">
                                                <button type="button" class="btn btn-sm btn-light w-75 rounded-pill" onclick="openMenuModal('{{ $day['date_key'] }}', {{ $index }})">
                                                    <i class="bi bi-arrow-repeat"></i> Ganti
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger w-75 rounded-pill" onclick="removeMenu('{{ $day['date_key'] }}', {{ $index }})">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="slot-empty shadow-sm" onclick="openMenuModal('{{ $day['date_key'] }}', {{ $index }})">
                                            <span class="text-muted small fw-bold"><i class="bi bi-plus-circle"></i> Tambah Menu</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

<div class="modal fade" id="menuSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title"><i class="bi bi-search me-2"></i>Pilih Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="input-group mb-4 shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchMenuInput" class="form-control border-start-0" placeholder="Cari nama menu...">
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-3" id="menuListContainer">
                    </div>
                
                <div id="emptyMenuState" class="text-center py-5 d-none">
                    <i class="bi bi-inbox text-muted fs-1"></i>
                    <p class="mt-2 text-muted">Menu tidak ditemukan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session()->has('response'))
    @php
        $sessResponse = session('response');
        $statusCode = $sessResponse['status_code'] ?? 200;
        $statusMsg = $sessResponse['message'] ?? 'Pemberitahuan Sistem';
        $statusType = $sessResponse['status'] ?? 'success'; 
        
        // Logika warna dan icon berdasarkan status code
        $themeClass = 'text-success';
        $iconClass = 'bi-check-circle-fill';
        
        if($statusCode == 500) {
            $themeClass = 'text-danger';
            $iconClass = 'bi-x-circle-fill';
        } elseif($statusCode != 200 && $statusCode != 201) {
            $themeClass = 'text-warning';
            $iconClass = 'bi-exclamation-triangle-fill';
        }
    @endphp

    <div class="modal fade" id="sessionAlertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-center p-3">
                <div class="modal-body py-4">
                    <i class="bi {{ $iconClass }} {{ $themeClass }}" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-1">Peringatan</h5>
                    <p class="text-muted small mb-4">{{ $statusMsg }}</p>
                    <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup Pemberitahuan</button>
                </div>
            </div>
        </div>
    </div>
@endif


<script>
    // 1. Simpan data menu dari backend ke dalam variable JS
    const rawMenus = @json($menus);
    
    // State untuk mencatat slot mana yang sedang di-edit
    let activeTargetDate = null;
    let activeTargetSlotIndex = null;
    
    // Inisialisasi Modal Bootstrap
    let menuModalInstance;

    document.addEventListener("DOMContentLoaded", function() {
        menuModalInstance = new bootstrap.Modal(document.getElementById('menuSelectionModal'));

        // Cek dan tampilkan Pop-out Global Session jika ada
        const sessionModalEl = document.getElementById('sessionAlertModal');
        if(sessionModalEl) {
            const sessionModal = new bootstrap.Modal(sessionModalEl);
            sessionModal.show();
        }

        // Listener Search Engine (Client-side)
        document.getElementById('searchMenuInput').addEventListener('input', function(e) {
            renderMenuList(e.target.value);
        });
    });

    // Fungsi membuka modal untuk suatu slot
    function openMenuModal(dateKey, slotIndex) {
        activeTargetDate = dateKey;
        activeTargetSlotIndex = slotIndex;
        
        document.getElementById('searchMenuInput').value = ''; // Reset search
        renderMenuList(); // Render semua menu
        menuModalInstance.show();
    }

    // Fungsi merender list menu di dalam modal (dengan fitur filter)
    function renderMenuList(keyword = '') {
        const container = document.getElementById('menuListContainer');
        const emptyState = document.getElementById('emptyMenuState');
        container.innerHTML = '';
        
        const filteredMenus = rawMenus.filter(m => m.name.toLowerCase().includes(keyword.toLowerCase()));
        
        if(filteredMenus.length === 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
            
            filteredMenus.forEach(menu => {
                // Gunakan loading="lazy" untuk performa gambar!
                const card = `
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm" style="cursor: pointer;" onclick="selectMenu('${menu.id}')">
                            <img src="${menu.image_url}" loading="lazy" class="card-img-top" alt="${menu.name}" style="height: 120px; object-fit: cover;" onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                            <div class="card-body p-2 text-center">
                                <h6 class="card-title text-truncate mb-0" style="font-size: 0.85rem;" title="${menu.name}">${menu.name}</h6>
                                <span class="badge bg-light text-dark border mt-1">${menu.theme}</span>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            });
        }
    }

    // Fungsi saat menu diklik di dalam modal -> Isi slot yang kosong/diganti
    function selectMenu(menuId) {
        const selectedMenu = rawMenus.find(m => m.id === menuId);
        if(!selectedMenu) return;

        const targetDivId = `slot-${activeTargetDate}-${activeTargetSlotIndex}`;
        const targetDiv = document.getElementById(targetDivId);

        // Buat HTML untuk slot yang terisi beserta Hidden Input-nya
        const filledHtml = `
            <div class="slot-filled shadow-sm">
                <input type="hidden" name="schedules[${activeTargetDate}][]" value="${selectedMenu.id}">
                <img src="${selectedMenu.image_url}" loading="lazy" alt="${selectedMenu.name}" onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                <div class="menu-title-badge">${selectedMenu.name}</div>
                <div class="slot-overlay">
                    <button type="button" class="btn btn-sm btn-light w-75 rounded-pill" onclick="openMenuModal('${activeTargetDate}', ${activeTargetSlotIndex})">
                        <i class="bi bi-arrow-repeat"></i> Ganti
                    </button>
                    <button type="button" class="btn btn-sm btn-danger w-75 rounded-pill" onclick="removeMenu('${activeTargetDate}', ${activeTargetSlotIndex})">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;

        targetDiv.innerHTML = filledHtml;
        menuModalInstance.hide();
    }

    // Fungsi menghapus menu dari slot (Kembali ke mode "Tambah Menu")
    function removeMenu(dateKey, slotIndex) {
        const targetDivId = `slot-${dateKey}-${slotIndex}`;
        const targetDiv = document.getElementById(targetDivId);
        
        const emptyHtml = `
            <div class="slot-empty shadow-sm" onclick="openMenuModal('${dateKey}', ${slotIndex})">
                <span class="text-muted small fw-bold"><i class="bi bi-plus-circle"></i> Tambah Menu</span>
            </div>
        `;
        
        targetDiv.innerHTML = emptyHtml;
    }
</script>

@else
    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center m-4">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
        <div>
            <strong>Gagal Memuat Jadwal!</strong> Pastikan response API sukses (Status 200).
        </div>
    </div>
@endif

{{-- <div class="mt-20">
    @if (session()->has('response'))
        {{ dd(session()->get('response')) }}
    @endif
</div> --}}

</body>
</html>