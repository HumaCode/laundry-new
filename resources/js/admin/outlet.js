const CITIES     = ['Jakarta Pusat','Bandung Kota','Surabaya Barat','Yogyakarta','Semarang Tengah'];
const STATUSES   = ['Aktif','Tutup','Maintenance'];
const MANAGERS   = ['Hendra Wijaya','Dewi Lestari','Nita Kusuma','Fajar Nugroho','Rini Susanti'];
const ADDRESSES = [
    'Jl. Sudirman No. 12, Jakarta Pusat',
    'Jl. Merdeka No. 45, Bandung',
    'Jl. Diponegoro No. 8, Surabaya',
    'Jl. Malioboro No. 100, Yogyakarta',
    'Jl. Pemuda No. 33, Semarang',
];
const GRADS     = ['grad-purple','grad-green','grad-orange','grad-pink','grad-blue','grad-teal'];
const AV_COLORS = ['#6366F1','#10B981','#F59E0B','#EC4899','#3B82F6','#8B5CF6','#F97316','#06B6D4','#14B8A6','#EF4444'];
const NAMES     = ['Outlet Pusat','Outlet Bandung','Outlet Surabaya','Outlet Yogyakarta','Outlet Semarang'];

let allOutlets = NAMES.map((name, i) => {
    const status      = i === 3 ? 'Maintenance' : (i === 4 ? 'Tutup' : 'Aktif');
    const staffCount  = 8 + Math.floor(Math.abs(Math.sin(i * 3.7)) * 10);
    const orders      = 120 + Math.floor(Math.abs(Math.cos(i * 1.5)) * 350);
    const revenue     = orders * (45000 + Math.floor(Math.abs(Math.sin(i)) * 25000));
    const day         = 10 + (i % 18);
    const month       = 1 + (i % 12);
    const year        = 2022 + (i % 2);
    const recentEmployees = [
        { name: `${MANAGERS[i]} (Manager)`, role: 'Kepala Outlet', status: 'Aktif' },
        { name: 'Karyawan A', role: 'Kasir', status: 'Aktif' },
        { name: 'Karyawan B', role: 'Kurir', status: 'Aktif' },
        { name: 'Karyawan C', role: 'Operator', status: 'Aktif' }
    ];
    return {
        id:      `OUT-${String(1000 + i).padStart(4,'0')}`,
        name, status,
        phone:   `02${String(10000000 + i * 23456789).slice(0,9)}`,
        email:   name.toLowerCase().replace(/ /g,'') + '@laundrypro.com',
        city:    CITIES[i % CITIES.length],
        address: ADDRESSES[i % ADDRESSES.length],
        manager: MANAGERS[i % MANAGERS.length],
        staffCount, orders, revenue,
        joined:  `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`,
        color:   AV_COLORS[i % AV_COLORS.length],
        grad:    GRADS[i % GRADS.length],
        notes:   i % 3 === 0 ? 'Kapasitas cuci harian optimal.' : 'Memerlukan perawatan mesin cuci tambahan.',
        recentEmployees,
    };
});

let filtered       = [...allOutlets];
let currentPage    = 1;
let perPage        = 10;
let selectedIds    = new Set();
let currentView    = 'table';
let activeOutlet   = null;
let editMode       = false;
let sortCol        = 'name';
let sortDir        = 'asc';

function switchView(v) {
    currentView = v;
    document.getElementById('viewBtnTable').classList.toggle('active', v === 'table');
    document.getElementById('viewBtnGrid').classList.toggle('active',  v === 'grid');
    document.getElementById('tableView').style.display = v === 'table' ? '' : 'none';
    document.getElementById('gridView').style.display  = v === 'grid'  ? '' : 'none';
    render();
}

function getInitials(name) { return name.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase(); }
function formatRp(n)       { return 'Rp ' + n.toLocaleString('id-ID'); }
function statusBadge(status) {
    const map = {Aktif:'status-aktif',Tutup:'status-tutup',Maintenance:'status-maintenance'};
    const icons = {Aktif:'🟢',Tutup:'🔴',Maintenance:'🛠️'};
    return `<span class="status-badge ${map[status] || 'status-aktif'}">${icons[status] || ''} ${status}</span>`;
}

function applyFilters() {
    const q       = document.getElementById('searchInput').value.toLowerCase();
    const status  = document.getElementById('filterStatus').value;
    const city    = document.getElementById('filterCity').value;
    const sortVal = document.getElementById('filterSort').value;

    filtered = allOutlets.filter(c => {
        const mq = !q || c.name.toLowerCase().includes(q) || c.address.toLowerCase().includes(q) || c.id.toLowerCase().includes(q) || c.manager.toLowerCase().includes(q);
        const mt = !status || c.status === status;
        const mo = !city   || c.city === city;
        return mq && mt && mo;
    });

    if (sortVal === 'name-asc')     filtered.sort((a,b) => a.name.localeCompare(b.name));
    else if (sortVal === 'name-desc')  filtered.sort((a,b) => b.name.localeCompare(a.name));
    else if (sortVal === 'staff-desc') filtered.sort((a,b) => b.staffCount - a.staffCount);
    else if (sortVal === 'revenue-desc') filtered.sort((a,b) => b.revenue - a.revenue);
    else if (sortVal === 'recent')     filtered.sort((a,b) => new Date(b.joined) - new Date(a.joined));

    currentPage = 1;
    selectedIds.clear();
    updateBulkBar();
    render();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterCity').value   = '';
    document.getElementById('filterSort').value   = 'name-asc';
    filtered = [...allOutlets];
    currentPage = 1;
    selectedIds.clear();
    updateBulkBar();
    render();
}

function sortBy(col) {
    if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortCol = col; sortDir = 'asc'; }
    document.querySelectorAll('.sort-icon').forEach(i => i.classList.remove('active'));
    const el = document.getElementById('si-' + col);
    if (el) el.classList.add('active');

    filtered.sort((a, b) => {
        let va = a[col], vb = b[col];
        if (col === 'joined') { va = new Date(va); vb = new Date(vb); }
        if (typeof va === 'string') return sortDir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
        return sortDir === 'asc' ? va - vb : vb - va;
    });
    currentPage = 1;
    render();
}

function render() {
    if (currentView === 'table') renderTable();
    else renderGrid();
}

function renderTable() {
    const tbody = document.getElementById('outletTableBody');
    const empty = document.getElementById('emptyState');
    if (!tbody) return;
    const start = (currentPage - 1) * perPage;
    const page  = filtered.slice(start, start + perPage);

    document.getElementById('totalCount').textContent = filtered.length.toLocaleString();
    document.getElementById('showCount').textContent  = Math.min(perPage, filtered.length - start);

    if (!filtered.length) { tbody.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';

    tbody.innerHTML = page.map(c => `
        <tr>
            <td class="cb-cell"><input type="checkbox" class="custom-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)"></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="outlet-avatar" style="background:${c.color}">
                        ${getInitials(c.name)}
                        <span class="outlet-avatar-status ${c.status==='Aktif'?'aktif':c.status==='Tutup'?'tutup':'maintenance'}"></span>
                    </div>
                    <div>
                        <div class="outlet-name">${c.name}</div>
                        <div class="outlet-id">${c.id}</div>
                    </div>
                </div>
            </td>
            <td>${statusBadge(c.status)}</td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email}</div>
            </td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.city}</div>
                <div style="font-size:.72rem;color:var(--gray-light);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${c.address}">${c.address}</div>
            </td>
            <td><span style="font-weight:600;color:var(--dark)">${c.manager}</span></td>
            <td><span class="staff-count-badge">${c.staffCount} orang</span></td>
            <td>
                <div class="amount-total">${formatRp(c.revenue)}</div>
                <div class="amount-avg">${c.orders} order bulan ini</div>
            </td>
            <td>
                <div class="action-cell">
                    <button class="act-btn act-btn-view"  title="Detail"   onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i></button>
                    <button class="act-btn act-btn-edit"  title="Edit"     onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i></button>
                    <button class="act-btn act-btn-delete" title="Hapus"   onclick="deleteById('${c.id}')"><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
        </tr>`).join('');

    syncCheckAll();
    renderPagination();
}

function renderGrid() {
    const grid  = document.getElementById('outletGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;
    const start = (currentPage - 1) * 12;
    const page  = filtered.slice(start, start + 12);

    if (!filtered.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(); return; }
    empty.style.display = 'none';

    grid.innerHTML = page.map(c => `
        <div class="outlet-grid-card ${c.grad}">
            <div class="outlet-grid-inner">
                <div class="outlet-grid-top">
                    <input type="checkbox" class="custom-cb outlet-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${statusBadge(c.status)}
                </div>
                <div class="outlet-grid-avatar-wrap">
                    <div class="outlet-grid-avatar" style="background:${c.color}">${getInitials(c.name)}</div>
                    <div class="outlet-grid-name">${c.name}</div>
                    <div class="outlet-grid-id">${c.id}</div>
                </div>
                <div class="outlet-grid-stats">
                    <div class="outlet-grid-stat"><div class="outlet-grid-stat-val">${c.staffCount}</div><div class="outlet-grid-stat-lbl">Staff</div></div>
                    <div class="outlet-grid-stat"><div class="outlet-grid-stat-val">${(c.revenue/1000000).toFixed(1)}jt</div><div class="outlet-grid-stat-lbl">Omset</div></div>
                    <div class="outlet-grid-stat"><div class="outlet-grid-stat-val">${c.orders}</div><div class="outlet-grid-stat-lbl">Order</div></div>
                </div>
            </div>
            <div class="outlet-grid-actions">
                <button class="outlet-grid-btn outlet-grid-btn-outline" onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="outlet-grid-btn outlet-grid-btn-primary" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i> Detail</button>
            </div>
        </div>`).join('');

    renderGridPagination();
}

function renderPagination() {
    const total = Math.ceil(filtered.length / perPage) || 1;
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent  = total;
    buildPageControls('paginationControls', total, goPage);
}

function renderGridPagination() {
    const total = Math.ceil(filtered.length / 12) || 1;
    document.getElementById('gridCurPage').textContent   = currentPage;
    document.getElementById('gridTotalPages').textContent = total;
    buildPageControls('gridPaginationControls', total, goPage);
}

function buildPageControls(containerId, total, fn) {
    const el = document.getElementById(containerId);
    if (!el) return;
    let html = `<button class="page-btn" onclick="${fn.name}(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    let s = Math.max(1, currentPage-2), e = Math.min(total, s+4);
    if (e-s<4) s = Math.max(1, e-4);
    if (s>1) { html += `<button class="page-btn" onclick="${fn.name}(1)">1</button>`; if(s>2) html += `<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`; }
    for (let i=s; i<=e; i++) html += `<button class="page-btn ${i===currentPage?'active':''}" onclick="${fn.name}(${i})">${i}</button>`;
    if (e<total) { if(e<total-1) html += `<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`; html += `<button class="page-btn" onclick="${fn.name}(${total})">${total}</button>`; }
    html += `<button class="page-btn" onclick="${fn.name}(${currentPage+1})" ${currentPage>=total?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
    el.innerHTML = html;
}

function goPage(p) {
    const total = Math.ceil(filtered.length / (currentView === 'table' ? perPage : 12)) || 1;
    if (p < 1 || p > total) return;
    currentPage = p;
    render();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function changePerPage(val) { perPage = parseInt(val); currentPage = 1; render(); }

function toggleAllCheck() {
    const checked = document.getElementById('checkAll').checked;
    const start = (currentPage-1) * perPage;
    filtered.slice(start, start+perPage).forEach(c => { checked ? selectedIds.add(c.id) : selectedIds.delete(c.id); });
    render();
    updateBulkBar();
}

function toggleRowCheck(cb) {
    const id = cb.dataset.id;
    cb.checked ? selectedIds.add(id) : selectedIds.delete(id);
    syncCheckAll();
    updateBulkBar();
}

function syncCheckAll() {
    const start = (currentPage-1)*perPage;
    const page  = filtered.slice(start, start+perPage);
    const all   = page.length > 0 && page.every(c => selectedIds.has(c.id));
    const cb    = document.getElementById('checkAll');
    if (cb) cb.checked = all;
}

function clearSelection() { selectedIds.clear(); render(); updateBulkBar(); }

function updateBulkBar() {
    const n = selectedIds.size;
    const bulkCountText = document.getElementById('bulkCountText');
    const bulkBar = document.getElementById('bulkBar');
    if (bulkCountText) bulkCountText.textContent = n;
    if (bulkBar) bulkBar.classList.toggle('show', n > 0);
}

function bulkExport()  { showToast('info','Export',`Mengekspor ${selectedIds.size} data outlet`); }
function bulkDelete() {
    if (!confirm(`Hapus ${selectedIds.size} outlet yang dipilih?`)) return;
    selectedIds.forEach(id => { const i = allOutlets.findIndex(c => c.id === id); if(i>-1) allOutlets.splice(i,1); });
    selectedIds.clear();
    applyFilters();
    showToast('success','Dihapus','Outlet berhasil dihapus');
}

function openDrawer(id) {
    const c = allOutlets.find(x => x.id === id);
    if (!c) return;
    activeOutlet = c;

    document.getElementById('d-avatar').textContent      = getInitials(c.name);
    document.getElementById('d-avatar').style.background = c.color;
    document.getElementById('d-name').textContent        = c.name;
    document.getElementById('d-id').textContent          = c.id;
    document.getElementById('d-status-wrap').innerHTML   = statusBadge(c.status);
    document.getElementById('d-totalorders').textContent = c.orders;
    document.getElementById('d-totalrevenue').textContent = (c.revenue/1000000).toFixed(1)+'jt';
    document.getElementById('d-staffcount').textContent  = c.staffCount;
    document.getElementById('d-phone').textContent       = c.phone;
    document.getElementById('d-email').textContent       = c.email;
    document.getElementById('d-joined').textContent      = c.joined;
    document.getElementById('d-city').textContent        = c.city;
    document.getElementById('d-address').textContent     = c.address;
    document.getElementById('d-manager').textContent     = c.manager;

    document.getElementById('d-recent-employees').innerHTML = c.recentEmployees.map(o => `
        <div class="drawer-employee-item">
            <div class="drawer-employee-avatar">${getInitials(o.name)}</div>
            <div class="drawer-employee-info">
                <div class="drawer-employee-name">${o.name}</div>
                <div class="drawer-employee-role">${o.role}</div>
            </div>
            <div>
                <div class="drawer-employee-status">${o.status}</div>
            </div>
        </div>`).join('');

    document.getElementById('drawerOverlay').classList.add('show');
}

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentOutlet() { if (!activeOutlet) return; deleteById(activeOutlet.id); closeDrawer(); }
function editCurrentOutlet()   { if (!activeOutlet) return; openEditModal(activeOutlet.id); }

function openAddModal() {
    editMode = false;
    activeOutlet = null;
    document.getElementById('modalIcon').innerHTML     = '<i class="fas fa-store-alt-slash"></i>';
    document.getElementById('modalTitle').textContent  = 'Tambah Outlet';
    document.getElementById('modalSubtitle').textContent = 'Isi data outlet baru';
    ['f-name','f-phone','f-email','f-address','f-manager','f-notes'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-city').value   = '';
    document.getElementById('f-status').value = 'Aktif';
    document.getElementById('outletModal').classList.add('show');
}

function openEditModal(id) {
    const c = allOutlets.find(x => x.id === id);
    if (!c) return;
    editMode = true; activeOutlet = c;
    document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
    document.getElementById('modalTitle').textContent    = 'Edit Outlet';
    document.getElementById('modalSubtitle').textContent = 'Perbarui data outlet';
    document.getElementById('f-name').value    = c.name;
    document.getElementById('f-phone').value   = c.phone;
    document.getElementById('f-email').value   = c.email;
    document.getElementById('f-address').value = c.address;
    document.getElementById('f-city').value    = c.city;
    document.getElementById('f-manager').value = c.manager;
    document.getElementById('f-status').value  = c.status;
    document.getElementById('f-notes').value   = c.notes;
    document.getElementById('outletModal').classList.add('show');
}

function saveOutlet() {
    const name  = document.getElementById('f-name').value.trim();
    const phone = document.getElementById('f-phone').value.trim();
    const city  = document.getElementById('f-city').value.trim();
    if (!name || !phone || !city) { showToast('error','Validasi','Nama, telepon, dan kota wajib diisi'); return; }

    if (editMode && activeOutlet) {
        activeOutlet.name    = name;
        activeOutlet.phone   = phone;
        activeOutlet.email   = document.getElementById('f-email').value;
        activeOutlet.address = document.getElementById('f-address').value;
        activeOutlet.city    = city;
        activeOutlet.manager = document.getElementById('f-manager').value;
        activeOutlet.status  = document.getElementById('f-status').value;
        activeOutlet.notes   = document.getElementById('f-notes').value;
        showToast('success','Diperbarui','Data outlet berhasil diperbarui');
    } else {
        const newC = {
            id: `OUT-${String(1000 + allOutlets.length).padStart(4,'0')}`,
            name, phone,
            email:   document.getElementById('f-email').value,
            city,
            address: document.getElementById('f-address').value,
            manager: document.getElementById('f-manager').value || '-',
            status:  document.getElementById('f-status').value,
            notes:   document.getElementById('f-notes').value,
            staffCount: 0, orders: 0, revenue: 0,
            joined: new Date().toISOString().slice(0,10),
            color: AV_COLORS[allOutlets.length % AV_COLORS.length],
            grad:  GRADS[allOutlets.length % GRADS.length],
            recentEmployees: [],
        };
        allOutlets.unshift(newC);
        showToast('success','Ditambahkan','Outlet baru berhasil ditambahkan');
    }
    closeModal('outletModal');
    applyFilters();
}

function deleteById(id) {
    const c = allOutlets.find(x => x.id === id);
    if (!c) return;
    if (!confirm(`Hapus outlet ${c.name}?`)) return;
    const i = allOutlets.indexOf(c);
    if (i > -1) allOutlets.splice(i, 1);
    applyFilters();
    showToast('success','Dihapus',`${c.name} berhasil dihapus`);
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function closeModalOutside(e, id) { if (e.target === e.currentTarget) closeModal(id); }

function exportData() { showToast('info','Export','Mengekspor data outlet...'); }

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
window.resetFilters = resetFilters;
window.sortBy = sortBy;
window.goPage = goPage;
window.changePerPage = changePerPage;
window.toggleAllCheck = toggleAllCheck;
window.toggleRowCheck = toggleRowCheck;
window.clearSelection = clearSelection;
window.bulkExport = bulkExport;
window.bulkDelete = bulkDelete;
window.openDrawer = openDrawer;
window.closeDrawer = closeDrawer;
window.closeDrawerOutside = closeDrawerOutside;
window.deleteCurrentOutlet = deleteCurrentOutlet;
window.editCurrentOutlet = editCurrentOutlet;
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.saveOutlet = saveOutlet;
window.deleteById = deleteById;
window.closeModal = closeModal;
window.closeModalOutside = closeModalOutside;
window.exportData = exportData;
window.showToast = showToast;
