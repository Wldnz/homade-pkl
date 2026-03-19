@include('components.header')

@if(session('response'))
    @if(session('response')['status'] !== 'success')
        <div class="alert alert-danger mx-8 mt-4">
            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('response')['message'] }}</h6>
            @if(isset(session('response')['errors']))
                <ul class="mb-0">
                    @foreach(session('response')['errors'] as $error)
                        <li>{{ $error[0] }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @else
        <div class="alert alert-success mx-8 mt-4">
            <h6 class="fw-bold mb-0"><i class="bi bi-check-circle-fill me-2"></i> {{ session('response')['message'] }}</h6>
        </div>
    @endif
@endif

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid py-8">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="mb-5 d-flex align-items-center justify-content-between">
                <h3 class="fw-bold text-dark mb-0">Tambah Transaksi Baru</h3>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light-primary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <form id="formCheckoutAdmin" action="{{ route('admin.add-order') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="checkout_payload" id="checkoutPayloadInput">

                <div class="row g-5">
                    <div class="col-lg-8">
                        
                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold">1. Informasi Klien</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold required">Pilih Pengguna</label>
                                        <select class="form-select form-select-sm" id="userSelect">
                                            </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold required">Email Kontak (Notifikasi)</label>
                                        <input type="email" class="form-control form-control-sm" id="contactEmail">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold">2. Alamat Pengiriman</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Pilih Alamat Tersimpan</label>
                                    <select class="form-select form-select-sm mb-3" id="addressSelect">
                                        <option value="">-- Buat Alamat Baru --</option>
                                    </select>
                                </div>

                                <div class="p-4 bg-light rounded border border-dashed" id="formNewAddress">
                                    <h6 class="fw-bold mb-3 text-primary">Input Alamat Baru</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small">Nama Penerima</label>
                                            <input type="text" class="form-control form-control-sm" id="newFullname">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">No. HP</label>
                                            <input type="text" class="form-control form-control-sm" id="newPhone">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Label (Cth: Rumah/Kantor)</label>
                                            <input type="text" class="form-control form-control-sm" id="newLabel">
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="saveProfileCheck" value="1">
                                                <label class="form-check-label small fw-bold" for="saveProfileCheck">
                                                    Simpan ke Profil (Save to Profile)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Alamat Lengkap</label>
                                            <textarea class="form-control form-control-sm" rows="2" id="newAddress"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Patokan / Catatan</label>
                                            <input type="text" class="form-control form-control-sm" id="newAddressNote">
                                        </div>
                                        <input type="hidden" id="newLongitude" value="106.8574">
                                        <input type="hidden" id="newLatitude" value="-6.2305">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-5">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold">3. Info Transaksi & Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4 mb-4 border-bottom pb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold required">Tanggal Pengiriman (Delivery At)</label>
                                        <input type="date" class="form-control form-control-sm" id="deliveryAt" required>
                                        <small class="text-primary" style="font-size: 11px;">Otomatis terisi jika memilih Menu Mingguan</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold required">Ongkos Kirim (Rp)</label>
                                        <input type="number" class="form-control form-control-sm" id="shippingCost" value="{{ $response['data']['delivery_info']['fee_per_km'] ?? 5000 }}" min="0">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Catatan Pesanan (Utama)</label>
                                        <textarea class="form-control form-control-sm" rows="2" id="transactionNote"></textarea>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3">Status Pembayaran</h6>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch p-3 bg-light rounded border">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" id="isSuccess">
                                            <label class="form-check-label fw-bold" for="isSuccess">Langsung SUCCESS</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Tipe Pembayaran</label>
                                        <select class="form-select form-select-sm" id="paymentType">
                                            <option value="transfer">Transfer (Butuh Bukti)</option>
                                            <option value="cash">Cash / COD</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Upload Bukti Transfer</label>
                                        <input type="file" class="form-control form-control-sm" name="payment_proof" accept="image/*">
                                        <small class="text-danger fw-bold d-block mt-1" style="font-size: 11px;">* Wajib diisi jika status "SUCCESS" dan tipe "TRANSFER". Opsional jika COD.</small>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2 border-top pt-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch p-3 bg-light rounded border border-warning border-opacity-25">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" id="isCreated">
                                            <label class="form-check-label fw-bold text-warning" for="isCreated">Transaksi Lawas</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">Tanggal Dibuat (Created At)</label>
                                        <input type="date" class="form-control form-control-sm" id="createdAt" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 bg-primary text-white sticky-top" style="top: 20px; z-index: 1;">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3 border-bottom border-white border-opacity-25 pb-2">Ringkasan Pesanan</h5>
                                
                                <button type="button" class="btn btn-light text-primary w-100 fw-bold mb-4" data-bs-toggle="modal" data-bs-target="#modalPilihMenu">
                                    <i class="bi bi-cart-plus me-2"></i> TAMBAH MENU
                                </button>

                                <div id="cartItemsContainer" class="mb-3 small" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center text-white-50 my-4 fst-italic">Belum ada menu dipilih</div>
                                </div>

                                <hr class="border-white border-opacity-25">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span>Subtotal</span>
                                    <span id="txtSubtotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span>Ongkos Kirim</span>
                                    <span id="txtOngkir">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="fw-bold fs-6">TOTAL BAYAR</span>
                                    <span class="fw-bold fs-4" id="txtTotal">Rp 0</span>
                                </div>

                                <button type="button" onclick="submitTransaction()" class="btn btn-warning text-dark w-100 fw-bold fs-6 py-3">BUAT PESANAN SEKARANG</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modalPilihMenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Katalog Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6 px-4 pt-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold text-dark" data-bs-toggle="tab" href="#tab_umum">Menu Umum (Reguler)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-dark" data-bs-toggle="tab" href="#tab_mingguan">Menu Mingguan (Weekly)</a>
                    </li>
                </ul>

                <div class="tab-content p-4">
                    <div class="tab-pane fade show active" id="tab_umum" role="tabpanel">
                        <div class="row g-4" id="containerMenuUmum"></div>
                    </div>

                    <div class="tab-pane fade" id="tab_mingguan" role="tabpanel">
                        <div class="alert alert-primary mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i> Memilih menu mingguan akan otomatis merubah Tanggal Pengiriman (Delivery At) di form utama.
                        </div>
                        <div id="containerMenuMingguan"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('components.navbarFoot', ["page" => "transaction"])

<script>var hostUrl = "/assets/";</script>
<script src="/assets/plugins/global/plugins.bundle.js"></script>
<script src="/assets/js/scripts.bundle.js"></script>

<script>
    // 1. DATA DARI BACKEND
    const serverData = @json($response['data'] ?? []);
    let cart = []; 

    // INIT USERS
    const initUsers = () => {
        const userSelect = document.getElementById('userSelect');
        if(serverData.user_info.default) {
            userSelect.innerHTML += `<option value="${serverData.user_info.default.id}">Default / Guest</option>`;
        }
        if(serverData.user_info.users) {
            serverData.user_info.users.forEach(u => {
                userSelect.innerHTML += `<option value="${u.id}">${u.first_name} ${u.last_name} (${u.email})</option>`;
            });
        }
        userSelect.addEventListener('change', handleUserChange);
        handleUserChange();
    };

    const handleUserChange = () => {
        const userId = document.getElementById('userSelect').value;
        const emailInput = document.getElementById('contactEmail');
        const addressSelect = document.getElementById('addressSelect');
        
        let selectedUser = userId === serverData.user_info.default?.id 
            ? serverData.user_info.default 
            : serverData.user_info.users.find(u => u.id === userId);

        if(selectedUser) emailInput.value = selectedUser.email;

        addressSelect.innerHTML = '<option value="">-- Buat Alamat Baru --</option>';
        if(selectedUser && selectedUser.address) {
            selectedUser.address.forEach(addr => {
                addressSelect.innerHTML += `<option value="${addr.id}">${addr.label} - ${addr.address.substring(0, 30)}...</option>`;
            });
        }
        handleAddressChange();
    };

    const handleAddressChange = () => {
        const formNew = document.getElementById('formNewAddress');
        formNew.style.display = document.getElementById('addressSelect').value === "" ? 'block' : 'none';
    };
    document.getElementById('addressSelect').addEventListener('change', handleAddressChange);

    // RENDER MENU MODAL
    const initMenus = () => {
        const contUmum = document.getElementById('containerMenuUmum');
        const contMingguan = document.getElementById('containerMenuMingguan');

        if(serverData.menu_info.menus) {
            serverData.menu_info.menus.forEach(menu => {
                contUmum.innerHTML += generateMenuCardHTML(menu, null);
            });
        }

        if(serverData.menu_info.weekly) {
            serverData.menu_info.weekly.forEach(week => {
                let html = `<h5 class="fw-bold text-primary mt-4 border-bottom pb-2">Tanggal: ${week.date}</h5><div class="row g-4 mb-4">`;
                week.menus.forEach(menu => {
                    html += generateMenuCardHTML(menu, week.date);
                });
                html += `</div>`;
                contMingguan.innerHTML += html;
            });
        }
    };

    const generateMenuCardHTML = (menu, weeklyDate) => {
        let pkgOptions = '';
        
        // PENTING: Baca dari menu.packages
        let menuPackages = menu.packages || [];
        if (menuPackages.length === 0) {
             const foundInUmum = serverData.menu_info.menus?.find(m => m.id === menu.id);
             if (foundInUmum && foundInUmum.packages) menuPackages = foundInUmum.packages;
        }

        menuPackages.forEach((p, idx) => {
            pkgOptions += `<option value="${p.id}" data-price="${p.price}" data-name="${p.name}">${p.name} - Rp ${parseInt(p.price).toLocaleString('id-ID')}</option>`;
        });

        const suffix = weeklyDate ? '_weekly' : '';
        const dateArg = weeklyDate ? `'${weeklyDate}'` : `null`;

        return `
            <div class="col-md-6 col-lg-4">
                <div class="card border border-light-dark h-100 shadow-sm">
                    <img src="${menu.image_url || 'https://via.placeholder.com/300'}" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1">${menu.name}</h6>
                        <span class="badge bg-secondary mb-3">${menu.theme || '-'}</span>
                        
                        <div class="mb-2">
                            <label class="small fw-bold">Pilih Paket:</label>
                            <select class="form-select form-select-sm" id="sel_pkg_${menu.id}${suffix}">${pkgOptions}</select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <label class="small fw-bold">Qty:</label>
                                <input type="number" class="form-control form-control-sm" id="qty_${menu.id}${suffix}" value="1" min="1">
                            </div>
                            <div class="col-8">
                                <label class="small fw-bold">Catatan (Paket):</label>
                                <input type="text" class="form-control form-control-sm" id="note_${menu.id}${suffix}" placeholder="Opsional...">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary w-100 mt-2" 
                            onclick="addMenuToCart('${menu.id}', '${menu.name}', '${menu.image_url}', ${dateArg})">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>
        `;
    };

    // CART LOGIC
    const addMenuToCart = (menuId, menuName, img, weeklyDate) => {
        const suffix = weeklyDate ? '_weekly' : '';
        const selPkg = document.getElementById(`sel_pkg_${menuId}${suffix}`);
        
        if (!selPkg || selPkg.options.length === 0) return alert("Paket harga tidak ditemukan!");

        const pkgId = selPkg.value;
        const pkgName = selPkg.options[selPkg.selectedIndex].getAttribute('data-name');
        const price = parseInt(selPkg.options[selPkg.selectedIndex].getAttribute('data-price'));
        const qty = parseInt(document.getElementById(`qty_${menuId}${suffix}`).value);
        const note = document.getElementById(`note_${menuId}${suffix}`).value;

        if(qty < 1) return alert("Quantity minimal 1!");

        // Auto Fill Delivery At (YYYY-MM-DD Format)
        if(weeklyDate) {
            const parts = weeklyDate.split('-');
            const isoDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
            document.getElementById('deliveryAt').value = isoDate;
            alert(`Tanggal pengiriman otomatis diset ke ${isoDate}`);
        }

        let menuInCart = cart.find(m => m.menu_id === menuId);
        if(!menuInCart) {
            menuInCart = { menu_id: menuId, menu_name: menuName, img: img, packages: [] };
            cart.push(menuInCart);
        }

        let pkgInCart = menuInCart.packages.find(p => p.package_id === pkgId);
        if(pkgInCart) {
            pkgInCart.qty += qty;
            if(note) pkgInCart.note = pkgInCart.note ? pkgInCart.note + ", " + note : note;
        } else {
            menuInCart.packages.push({ package_id: pkgId, package_name: pkgName, price: price, qty: qty, note: note });
        }

        renderCart();
        
        document.getElementById(`qty_${menuId}${suffix}`).value = 1;
        document.getElementById(`note_${menuId}${suffix}`).value = '';
    };

    const removePackage = (menuIndex, pkgIndex) => {
        cart[menuIndex].packages.splice(pkgIndex, 1);
        if(cart[menuIndex].packages.length === 0) {
            cart.splice(menuIndex, 1);
        }
        renderCart();
    };

    const renderCart = () => {
        const container = document.getElementById('cartItemsContainer');
        let subtotal = 0;
        
        if(cart.length === 0) {
            container.innerHTML = `<div class="text-center text-white-50 my-4 fst-italic">Belum ada menu dipilih</div>`;
        } else {
            container.innerHTML = '';
            cart.forEach((menuItem, mIdx) => {
                let pkgHtml = '';
                menuItem.packages.forEach((pkg, pIdx) => {
                    subtotal += (pkg.price * pkg.qty);
                    pkgHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-1 ps-2 border-start border-2 border-warning ms-1">
                            <div class="small">
                                <span class="text-danger cursor-pointer me-1" onclick="removePackage(${mIdx}, ${pIdx})"><i class="bi bi-x"></i></span>
                                <b>${pkg.qty}x</b> ${pkg.package_name}
                                ${pkg.note ? `<br><small class="text-white-50 fst-italic ms-4">Catatan: ${pkg.note}</small>` : ''}
                            </div>
                            <span class="small fw-bold text-nowrap">Rp ${(pkg.price * pkg.qty).toLocaleString('id-ID')}</span>
                        </div>
                    `;
                });

                container.innerHTML += `
                    <div class="mb-3 pb-2 border-bottom border-white border-opacity-10">
                        <div class="fw-bold mb-1"><i class="bi bi-box-seam me-1"></i> ${menuItem.menu_name}</div>
                        ${pkgHtml}
                    </div>
                `;
            });
        }

        const shippingCost = parseInt(document.getElementById('shippingCost').value) || 0;
        document.getElementById('txtSubtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('txtOngkir').innerText = 'Rp ' + shippingCost.toLocaleString('id-ID');
        document.getElementById('txtTotal').innerText = 'Rp ' + (subtotal + shippingCost).toLocaleString('id-ID');
    };

    document.getElementById('shippingCost').addEventListener('input', renderCart);
    document.getElementById('isCreated').addEventListener('change', function() { document.getElementById('createdAt').disabled = !this.checked; });

    // RECOVERY JIKA ERROR
    const recoverOldData = () => {
        const oldPayloadStr = `{!! old('checkout_payload') !!}`;
        if(oldPayloadStr) {
            try {
                const old = JSON.parse(oldPayloadStr);
                document.getElementById('shippingCost').value = old.transaction_info.shipping_cost;
                document.getElementById('isSuccess').checked = old.transaction_info.is_success;
                document.getElementById('isCreated').checked = old.transaction_info.is_created;
                if(old.transaction_info.is_created) {
                    document.getElementById('createdAt').disabled = false;
                    document.getElementById('createdAt').value = old.transaction_info.created_at;
                }
                document.getElementById('paymentType').value = old.transaction_info.payment_type;
                document.getElementById('transactionNote').value = old.transaction_info.note;
                
                document.getElementById('userSelect').value = old.user_info.user_id;
                document.getElementById('contactEmail').value = old.user_info.contact_email;
                document.getElementById('deliveryAt').value = old.delivery_info.delivery_at;
                document.getElementById('addressSelect').value = old.delivery_info.user_address_id || '';
                handleAddressChange();

                if(!old.delivery_info.user_address_id) {
                    document.getElementById('newFullname').value = old.delivery_info.new_user_address.fullname;
                    document.getElementById('newPhone').value = old.delivery_info.new_user_address.phone;
                    document.getElementById('newLabel').value = old.delivery_info.new_user_address.label;
                    document.getElementById('newAddress').value = old.delivery_info.new_user_address.address;
                    document.getElementById('newAddressNote').value = old.delivery_info.new_user_address.note;
                    document.getElementById('saveProfileCheck').checked = old.delivery_info.new_user_address.save_to_profile;
                }

                if(old.items) {
                    old.items.forEach(item => {
                        let mName = "Menu", mImg = "";
                        const m = serverData.menu_info.menus?.find(x => x.id === item.id) || 
                                  serverData.menu_info.weekly?.flatMap(w=>w.menus).find(x => x.id === item.id);
                        if(m) { mName = m.name; mImg = m.image_url; }
                        
                        let packages = [];
                        item.packages.forEach(pkg => {
                            let pName = "Paket", pPrice = 0;
                            if(m) {
                                // PENTING: Baca dari packages juga saat recovery
                                const p = m.packages?.find(x => x.id === pkg.id) || [];
                                if(p) { pName = p.name; pPrice = p.price; }
                            }
                            packages.push({ package_id: pkg.id, package_name: pName, price: pPrice, qty: pkg.quantity, note: pkg.note || '' });
                        });
                        cart.push({ menu_id: item.id, menu_name: mName, img: mImg, packages: packages });
                    });
                }
                renderCart();
            } catch(e) {}
        }
    }

    // BUILD JSON SEBELUM SUBMIT
    const submitTransaction = () => {
        if(cart.length === 0) return alert("Pilih minimal 1 menu dulu!");

        let formattedItems = [];
        cart.forEach(c => {
            formattedItems.push({
                id: c.menu_id,
                packages: c.packages.map(p => ({
                    id: p.package_id,
                    quantity: p.qty,
                    note: p.note
                }))
            });
        });

        const payload = {
            transaction_info: {
                shipping_cost: parseInt(document.getElementById('shippingCost').value) || 0,
                is_success: document.getElementById('isSuccess').checked,
                is_created: document.getElementById('isCreated').checked,
                created_at: document.getElementById('createdAt').value || null,
                payment_type: document.getElementById('paymentType').value,
                note: document.getElementById('transactionNote').value
            },
            items: formattedItems,
            user_info: {
                user_id: document.getElementById('userSelect').value,
                contact_email: document.getElementById('contactEmail').value
            },
            delivery_info: {
                delivery_at: document.getElementById('deliveryAt').value,
                user_address_id: document.getElementById('addressSelect').value || null,
                new_user_address: {
                    fullname: document.getElementById('newFullname').value,
                    phone: document.getElementById('newPhone').value,
                    label: document.getElementById('newLabel').value,
                    address: document.getElementById('newAddress').value,
                    note: document.getElementById('newAddressNote').value,
                    longitude: document.getElementById('newLongitude').value,
                    latitude: document.getElementById('newLatitude').value,
                    save_to_profile: document.getElementById('saveProfileCheck').checked
                }
            }
        };

        document.getElementById('checkoutPayloadInput').value = JSON.stringify(payload);
        document.getElementById('formCheckoutAdmin').submit();
    };

    window.onload = () => {
        initUsers();
        initMenus();
        recoverOldData();
    };
</script>