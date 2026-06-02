// Setup CSRF token for jQuery AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let currentPage    = 1;
let perPage        = 10;
let selectedIds    = new Set();
let currentView    = 'table';
let activeCustomer = null;
let editMode       = false;
let sortCol        = 'name';
let sortDir        = 'asc';
let pageData       = [];
let totalItems     = 0;

function switchView(v) {
    currentView = v;
    document.getElementById('viewBtnTable').classList.toggle('active', v === 'table');
    document.getElementById('viewBtnGrid').classList.toggle('active',  v === 'grid');
    document.getElementById('tableView').style.display = v === 'table' ? '' : 'none';
    document.getElementById('gridView').style.display  = v === 'grid'  ? '' : 'none';
    applyFilters();
}

function getInitials(name) { return name.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase(); }
function formatRp(n)       { return 'Rp ' + n.toLocaleString('id-ID'); }
function tierBadge(tier) {
    const map = {VIP:'tier-vip',Premium:'tier-premium',Reguler:'tier-reguler',Baru:'tier-baru'};
    const icons = {VIP:'👑',Premium:'💎',Reguler:'✅',Baru:'🌱'};
    return `<span class="tier-badge ${map[tier] || 'tier-reguler'}">${icons[tier] || ''} ${tier}</span>`;
}
function relativeDate(dateStr) {
    if (!dateStr || dateStr === '-') return '-';
    const diff = Math.floor((new Date() - new Date(dateStr)) / 86400000);
    if (diff === 0) return 'Hari ini';
    if (diff === 1) return 'Kemarin';
    if (diff < 30)  return `${diff} hari lalu`;
    if (diff < 365) return `${Math.floor(diff/30)} bulan lalu`;
    return `${Math.floor(diff/365)} tahun lalu`;
}

function showSkeletonTable() {
    const tbody = document.getElementById('custTableBody');
    if (!tbody) return;
    
    let html = '';
    for (let i = 0; i < perPage; i++) {
        html += `
        <tr>
            <td class="cb-cell"><div class="skeleton" style="width:18px;height:18px;border-radius:5px"></div></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="skeleton skeleton-circle"></div>
                    <div style="flex:1">
                        <div class="skeleton skeleton-text medium" style="height:14px;width:120px"></div>
                        <div class="skeleton skeleton-text short" style="height:8px;width:60px;margin:0"></div>
                    </div>
                </div>
            </td>
            <td><div class="skeleton skeleton-badge" style="width:70px;height:24px"></div></td>
            <td>
                <div class="skeleton skeleton-text medium" style="height:14px;width:100px"></div>
                <div class="skeleton skeleton-text short" style="height:8px;width:120px;margin:0"></div>
            </td>
            <td><div class="skeleton skeleton-text medium" style="width:80px;height:14px"></div></td>
            <td><div class="skeleton skeleton-text short" style="width:30px;height:14px"></div></td>
            <td>
                <div class="skeleton skeleton-text medium" style="height:14px;width:85px"></div>
                <div class="skeleton skeleton-text short" style="height:8px;width:60px;margin:0"></div>
            </td>
            <td><div class="skeleton skeleton-text medium" style="width:50px;height:14px"></div></td>
            <td>
                <div class="skeleton skeleton-text medium" style="height:14px;width:80px"></div>
                <div class="skeleton skeleton-text short" style="height:8px;width:60px;margin:0"></div>
            </td>
            <td>
                <div class="action-cell">
                    <div class="skeleton skeleton-btn"></div>
                    <div class="skeleton skeleton-btn"></div>
                    <div class="skeleton skeleton-btn"></div>
                    <div class="skeleton skeleton-btn"></div>
                </div>
            </td>
        </tr>`;
    }
    tbody.innerHTML = html;
}

function showSkeletonGrid() {
    const grid = document.getElementById('custGrid');
    if (!grid) return;
    
    let html = '';
    for (let i = 0; i < 12; i++) {
        html += `
        <div class="cust-grid-card grad-purple" style="pointer-events:none;opacity:0.7">
            <div class="cust-grid-inner">
                <div class="cust-grid-top" style="margin-bottom:2.5rem">
                    <div class="skeleton" style="width:18px;height:18px;border-radius:5px"></div>
                    <div class="skeleton skeleton-badge" style="width:70px;height:24px"></div>
                </div>
                <div class="cust-grid-avatar-wrap">
                    <div class="skeleton skeleton-circle" style="width:72px;height:72px;border-radius:18px;margin:0 auto 0.5rem"></div>
                    <div class="skeleton skeleton-text long" style="height:16px;width:120px;display:block;margin:0 auto"></div>
                    <div class="skeleton skeleton-text short" style="height:8px;width:60px;display:block;margin:6px auto 0"></div>
                </div>
                <div class="cust-grid-stats" style="margin-top:1.5rem">
                    <div class="cust-grid-stat"><div class="skeleton" style="width:25px;height:14px;margin:0 auto 4px"></div><div class="skeleton" style="width:35px;height:8px;margin:0 auto"></div></div>
                    <div class="cust-grid-stat"><div class="skeleton" style="width:25px;height:14px;margin:0 auto 4px"></div><div class="skeleton" style="width:35px;height:8px;margin:0 auto"></div></div>
                    <div class="cust-grid-stat"><div class="skeleton" style="width:25px;height:14px;margin:0 auto 4px"></div><div class="skeleton" style="width:35px;height:8px;margin:0 auto"></div></div>
                </div>
            </div>
            <div class="cust-grid-actions">
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
            </div>
        </div>`;
    }
    grid.innerHTML = html;
}

function applyFilters() {
    const q       = document.getElementById('searchInput').value;
    const tier    = document.getElementById('filterTier').value;
    const outlet  = document.getElementById('filterOutlet').value;
    const sortVal = document.getElementById('filterSort').value;

    const limit = currentView === 'table' ? perPage : 12;

    if (currentView === 'table') showSkeletonTable();
    else showSkeletonGrid();

    $.ajax({
        url: '/customers',
        method: 'GET',
        data: {
            search: q,
            tier: tier,
            outlet_id: outlet,
            sort: sortVal,
            per_page: limit,
            page: currentPage
        },
        success: function(res) {
            if (res.success) {
                pageData = res.data.data;
                const meta = res.data.meta;
                totalItems = meta.total;
                currentPage = meta.current_page;
                
                // Update dynamic stat cards
                if (res.data.stats) {
                    const stats = res.data.stats;
                    
                    const totalCustomersEl = document.getElementById('statTotalCustomers');
                    if (totalCustomersEl) totalCustomersEl.textContent = stats.total_customers.toLocaleString('id-ID');
                    
                    const activeCustomersEl = document.getElementById('statActiveCustomers');
                    if (activeCustomersEl) activeCustomersEl.textContent = stats.active_customers.toLocaleString('id-ID');
                    
                    const vipCustomersEl = document.getElementById('statVipCustomers');
                    if (vipCustomersEl) vipCustomersEl.textContent = stats.vip_customers.toLocaleString('id-ID');
                    
                    const newCustomersEl = document.getElementById('statNewCustomers');
                    if (newCustomersEl) newCustomersEl.textContent = stats.new_customers.toLocaleString('id-ID');
                }
                
                render(meta);
            }
        },
        error: function() {
            showToast('error', 'Error', 'Gagal memuat data pelanggan dari server');
        }
    });
}

function handleTopSearch(v) { 
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = v; 
        applyFilters(); 
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTier').value  = '';
    document.getElementById('filterOutlet').value = '';
    document.getElementById('filterSort').value   = 'name-asc';
    currentPage = 1;
    selectedIds.clear();
    updateBulkBar();
    applyFilters();
}

function sortBy(col) {
    if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortCol = col; sortDir = 'asc'; }
    document.querySelectorAll('.sort-icon').forEach(i => i.classList.remove('active'));
    const el = document.getElementById('si-' + col);
    if (el) el.classList.add('active');

    const mapping = {
        'name': sortDir === 'asc' ? 'name-asc' : 'name-desc',
        'joined': 'recent'
    };

    document.getElementById('filterSort').value = mapping[col] || 'name-asc';
    applyFilters();
}

function render(meta) {
    if (currentView === 'table') renderTable(meta);
    else renderGrid(meta);
}

function renderTable(meta) {
    const tbody = document.getElementById('custTableBody');
    const empty = document.getElementById('emptyState');
    if (!tbody) return;

    document.getElementById('totalCount').textContent = totalItems.toLocaleString('id-ID');
    document.getElementById('showCount').textContent  = pageData.length.toLocaleString('id-ID');

    if (!pageData.length) { tbody.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';

    tbody.innerHTML = pageData.map(c => `
        <tr>
            <td class="cb-cell"><input type="checkbox" class="custom-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)"></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="cust-avatar" style="background:${c.color}">
                        ${getInitials(c.name)}
                        <span class="cust-avatar-status ${c.tier==='VIP'?'vip':c.tier==='Baru'?'baru':c.tier==='Premium'?'vip':'aktif'}"></span>
                    </div>
                    <div>
                        <div class="cust-name">${c.name}</div>
                        <div class="cust-id" style="font-size:.7rem">${c.id.substring(0,8)}...</div>
                    </div>
                </div>
            </td>
            <td>${tierBadge(c.tier)}</td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email || '-'}</div>
            </td>
            <td><span style="font-size:.8rem;color:var(--gray)">${c.outlet}</span></td>
            <td><span class="order-count-badge">${c.orders}</span></td>
            <td>
                <div class="amount-total">${formatRp(c.total)}</div>
                <div class="amount-avg">avg ${formatRp(c.avgOrder)}</div>
            </td>
            <td>
                <div class="star-rating">
                    <span class="stars">★</span>
                    <span class="star-val">${c.rating}</span>
                    <span class="star-count">(${c.orders})</span>
                </div>
            </td>
            <td>
                <div class="last-order-date">${c.lastOrder}</div>
                <div class="last-order-rel">${relativeDate(c.lastOrder)}</div>
            </td>
            <td>
                <div class="action-cell">
                    <button class="act-btn act-btn-view"  title="Detail"   onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i></button>
                    <button class="act-btn act-btn-edit"  title="Edit"     onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i></button>
                    <button class="act-btn act-btn-order" title="Order"    onclick="newOrderFor('${c.id}')"><i class="fas fa-plus-circle"></i></button>
                    <button class="act-btn act-btn-delete" title="Hapus"   onclick="deleteById('${c.id}')"><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
        </tr>`).join('');

    syncCheckAll();
    renderPagination(meta);
}

function renderGrid(meta) {
    const grid  = document.getElementById('custGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;

    if (!pageData.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(meta); return; }
    empty.style.display = 'none';

    grid.innerHTML = pageData.map(c => `
        <div class="cust-grid-card ${c.grad}">
            <div class="cust-grid-inner">
                <div class="cust-grid-top">
                    <input type="checkbox" class="custom-cb cust-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${tierBadge(c.tier)}
                </div>
                <div class="cust-grid-avatar-wrap">
                    <div class="cust-grid-avatar" style="background:${c.color}">${getInitials(c.name)}</div>
                    <div class="cust-grid-name">${c.name}</div>
                    <div class="cust-grid-id" style="font-size:.7rem">${c.id.substring(0,8)}...</div>
                </div>
                <div class="cust-grid-stats" style="margin-top:1.5rem">
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">${c.orders}</div><div class="cust-grid-stat-lbl">Order</div></div>
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">${(c.total/1000000).toFixed(1)}jt</div><div class="cust-grid-stat-lbl">Transaksi</div></div>
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">★ ${c.rating}</div><div class="cust-grid-stat-lbl">Rating</div></div>
                </div>
            </div>
            <div class="cust-grid-actions">
                <button class="cust-grid-btn cust-grid-btn-outline" onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="cust-grid-btn cust-grid-btn-primary" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i> Detail</button>
            </div>
        </div>`).join('');

    renderGridPagination(meta);
}

function renderPagination(meta) {
    document.getElementById('currentPage').textContent = meta.current_page;
    document.getElementById('totalPages').textContent  = meta.last_page;
    buildPageControls('paginationControls', meta.last_page, 'goPage');
}

function renderGridPagination(meta) {
    document.getElementById('gridCurPage').textContent   = meta.current_page;
    document.getElementById('gridTotalPages').textContent = meta.last_page;
    buildPageControls('gridPaginationControls', meta.last_page, 'goPage');
}

function buildPageControls(containerId, total, fnName) {
    const el = document.getElementById(containerId);
    if (!el) return;
    let html = `<button class="page-btn" onclick="${fnName}(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    
    const current = currentPage;
    const last = total;
    const delta = 1;
    const range = [];
    const rangeWithDots = [];
    let l;

    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
            range.push(i);
        }
    }

    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l > 2) {
                rangeWithDots.push('…');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    for (let i of rangeWithDots) {
        if (i === '…') {
            html += `<span class="page-ellipsis">…</span>`;
        } else {
            html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="${fnName}(${i})">${i}</button>`;
        }
    }

    html += `<button class="page-btn" onclick="${fnName}(${currentPage+1})" ${currentPage>=total?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
    el.innerHTML = html;
}

function goPage(p) {
    const limit = currentView === 'table' ? perPage : 12;
    const total = Math.ceil(totalItems / limit) || 1;
    if (p < 1 || p > total) return;
    currentPage = p;
    applyFilters();
}

function changePerPage(val) { perPage = parseInt(val); currentPage = 1; applyFilters(); }

function toggleAllCheck() {
    const checked = document.getElementById('checkAll').checked;
    pageData.forEach(c => { checked ? selectedIds.add(c.id) : selectedIds.delete(c.id); });
    render( { current_page: currentPage, last_page: Math.ceil(totalItems / (currentView === 'table' ? perPage : 12)) || 1 } );
    updateBulkBar();
}

function toggleRowCheck(cb) {
    const id = cb.dataset.id;
    cb.checked ? selectedIds.add(id) : selectedIds.delete(id);
    syncCheckAll();
    updateBulkBar();
}

function syncCheckAll() {
    const all   = pageData.length > 0 && pageData.every(c => selectedIds.has(c.id));
    const cb    = document.getElementById('checkAll');
    if (cb) cb.checked = all;
}

function clearSelection() { selectedIds.clear(); applyFilters(); updateBulkBar(); }

function updateBulkBar() {
    const n = selectedIds.size;
    const bulkCountText = document.getElementById('bulkCountText');
    const bulkBar = document.getElementById('bulkBar');
    if (bulkCountText) bulkCountText.textContent = n;
    if (bulkBar) bulkBar.classList.toggle('show', n > 0);
}

function bulkExport()  { showToast('info','Export',`Mengekspor ${selectedIds.size} data pelanggan`); }
function bulkMessage() { showToast('info','Pesan',`Mengirim pesan ke ${selectedIds.size} pelanggan`); }
function bulkDelete() {
    if (selectedIds.size === 0) return;
    if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.size} pelanggan yang dipilih?`)) return;

    let promises = [];
    selectedIds.forEach(id => {
        promises.push(
            $.ajax({
                url: '/customers/' + id,
                method: 'DELETE'
            })
        );
    });

    Promise.all(promises).then(() => {
        selectedIds.clear();
        applyFilters();
        showToast('success', 'Dihapus', 'Pelanggan terpilih berhasil dihapus');
    }).catch(() => {
        showToast('error', 'Error', 'Beberapa pelanggan gagal dihapus');
    });
}

function openDrawer(id) {
    $.ajax({
        url: '/customers/' + id,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const c = res.data;
                activeCustomer = c;

                document.getElementById('d-avatar').textContent      = getInitials(c.name);
                document.getElementById('d-name').textContent        = c.name;
                document.getElementById('d-id').textContent          = c.id;
                document.getElementById('d-tier-wrap').innerHTML     = tierBadge(c.tier);
                document.getElementById('d-totalorders').textContent = c.orders;
                document.getElementById('d-totalspend').textContent  = (c.total/1000000).toFixed(1)+'jt';
                document.getElementById('d-rating').textContent      = '★ '+c.rating;
                document.getElementById('d-phone').textContent       = c.phone;
                document.getElementById('d-email').textContent       = c.email || '-';
                document.getElementById('d-joined').textContent      = c.joined;
                document.getElementById('d-outlet').textContent      = c.outlet;
                document.getElementById('d-address').textContent     = c.address || '-';
                document.getElementById('d-favservice').textContent  = c.favService;
                document.getElementById('d-avgorder').textContent    = formatRp(c.avgOrder);

                document.getElementById('d-recent-orders').innerHTML = c.recentOrders.map(o => `
                    <div class="drawer-order-item">
                        <div class="drawer-order-icon"><i class="fas fa-receipt"></i></div>
                        <div class="drawer-order-info">
                            <div class="drawer-order-id">#${o.id}</div>
                            <div class="drawer-order-service">${o.service}</div>
                        </div>
                        <div>
                            <div class="drawer-order-amount">${formatRp(o.amount)}</div>
                            <div class="drawer-order-date">${o.date}</div>
                        </div>
                    </div>`).join('');

                document.getElementById('drawerOverlay').classList.add('show');
            }
        },
        error: function() {
            showToast('error', 'Error', 'Gagal memuat detail pelanggan');
        }
    });
}

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentCustomer() { if (!activeCustomer) return; deleteById(activeCustomer.id); closeDrawer(); }
function editCurrentCustomer()   { if (!activeCustomer) return; openEditModal(activeCustomer.id); }
function newOrderFor(id) {
    const c = pageData.find(x => x.id === id);
    if (!c) return;
    showToast('info','Order Baru',`Membuat order baru untuk ${c.name}`);
}

function openAddModal() {
    editMode = false;
    activeCustomer = null;
    document.getElementById('modalIcon').innerHTML     = '<i class="fas fa-user-plus"></i>';
    document.getElementById('modalTitle').textContent  = 'Tambah Pelanggan';
    document.getElementById('modalSubtitle').textContent = 'Isi data pelanggan baru';
    ['f-name','f-phone','f-email','f-address','f-notes'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-dob').value    = '';
    document.getElementById('f-gender').value = '';
    document.getElementById('f-outlet').value = '';
    document.getElementById('f-tier').value   = 'Baru';
    document.getElementById('custModal').classList.add('show');
}

function openEditModal(id) {
    const c = pageData.find(x => x.id === id);
    if (!c) return;
    editMode = true; activeCustomer = c;
    document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
    document.getElementById('modalTitle').textContent    = 'Edit Pelanggan';
    document.getElementById('modalSubtitle').textContent = 'Perbarui data pelanggan';
    
    document.getElementById('f-name').value    = c.name;
    document.getElementById('f-phone').value   = c.phone;
    document.getElementById('f-email').value   = c.email || '';
    document.getElementById('f-address').value = c.address === '-' ? '' : c.address;
    document.getElementById('f-notes').value   = c.notes || '';
    
    // Map gender back to select
    let genderVal = c.gender || '';
    if (genderVal === 'male') genderVal = 'Laki-laki';
    if (genderVal === 'female') genderVal = 'Perempuan';
    document.getElementById('f-gender').value = genderVal;

    document.getElementById('f-dob').value     = c.dob || '';
    document.getElementById('f-outlet').value  = c.outlet_id || '';
    document.getElementById('f-tier').value    = c.tier;
    document.getElementById('custModal').classList.add('show');
}

function saveCustomer() {
    const name  = document.getElementById('f-name').value.trim();
    const phone = document.getElementById('f-phone').value.trim();
    const email = document.getElementById('f-email').value.trim();
    const dob = document.getElementById('f-dob').value;
    const gender = document.getElementById('f-gender').value;
    const outlet_id = document.getElementById('f-outlet').value;
    const tier = document.getElementById('f-tier').value;
    const address = document.getElementById('f-address').value.trim();
    const notes = document.getElementById('f-notes').value.trim();

    if (!name || !phone) { showToast('error','Validasi','Nama dan telepon wajib diisi'); return; }

    const url = editMode ? '/customers/' + activeCustomer.id : '/customers';
    const method = editMode ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: {
            name: name,
            phone: phone,
            email: email,
            dob: dob,
            gender: gender,
            outlet_id: outlet_id,
            tier: tier,
            address: address,
            notes: notes
        },
        success: function(res) {
            if (res.success) {
                showToast('success', editMode ? 'Diperbarui' : 'Ditambahkan', editMode ? 'Data pelanggan berhasil diperbarui' : 'Pelanggan baru berhasil ditambahkan');
                closeModal('custModal');
                applyFilters();
            }
        },
        error: function(err) {
            let msg = 'Gagal menyimpan data pelanggan';
            if (err.responseJSON && err.responseJSON.message) {
                msg = err.responseJSON.message;
            }
            showToast('error', 'Error', msg);
        }
    });
}

function deleteById(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) return;

    $.ajax({
        url: '/customers/' + id,
        method: 'DELETE',
        success: function(res) {
            if (res.success) {
                showToast('success', 'Dihapus', 'Pelanggan berhasil dihapus');
                applyFilters();
            }
        },
        error: function() {
            showToast('error', 'Error', 'Gagal menghapus pelanggan');
        }
    });
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function closeModalOutside(e, id) { if (e.target === e.currentTarget) closeModal(id); }

function exportData() { showToast('info','Export','Mengekspor data pelanggan ke Excel...'); }

function showToast(type, title, msg) {
    const wrap = document.getElementById('toastWrap');
    if (!wrap) return;
    const t    = document.createElement('div');
    t.className = 'toast';
    t.innerHTML = `
        <div class="toast-icon ${type}"><i class="fas fa-${type==='success'?'check':type==='error'?'times':'info-circle'}"></i></div>
        <div style="flex:1"><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div>
        <button class="toast-x" onclick="this.closest('.toast').remove()"><i class="fas fa-times"></i></button>`;
    wrap.appendChild(t);
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => { t.classList.remove('show'); setTimeout(()=>t.remove(),400); }, 4000);
}

document.addEventListener('DOMContentLoaded', () => {
    applyFilters();
    
    window.addEventListener('scroll', () => {
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        if (scrollTopBtn) {
            scrollTopBtn.classList.toggle('visible', window.scrollY > 300);
        }
    });
});

// Expose functions globally for Blade template onclick events
window.switchView = switchView;
window.applyFilters = applyFilters;
window.handleTopSearch = handleTopSearch;
window.resetFilters = resetFilters;
window.sortBy = sortBy;
window.goPage = goPage;
window.changePerPage = changePerPage;
window.toggleAllCheck = toggleAllCheck;
window.toggleRowCheck = toggleRowCheck;
window.clearSelection = clearSelection;
window.bulkExport = bulkExport;
window.bulkMessage = bulkMessage;
window.bulkDelete = bulkDelete;
window.openDrawer = openDrawer;
window.closeDrawer = closeDrawer;
window.closeDrawerOutside = closeDrawerOutside;
window.deleteCurrentCustomer = deleteCurrentCustomer;
window.editCurrentCustomer = editCurrentCustomer;
window.newOrderFor = newOrderFor;
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.saveCustomer = saveCustomer;
window.deleteById = deleteById;
window.closeModal = closeModal;
window.closeModalOutside = closeModalOutside;
window.exportData = exportData;
window.showToast = showToast;
