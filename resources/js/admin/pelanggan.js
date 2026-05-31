const OUTLETS   = ['Outlet Pusat','Outlet Bandung','Outlet Surabaya','Outlet Yogyakarta','Outlet Semarang'];
const TIERS     = ['VIP','Premium','Reguler','Baru'];
const SERVICES  = ['Cuci Setrika','Cuci Kering','Express','Bed Cover','Jas & Blazer','Setrika Saja'];
const ADDRESSES = [
    'Jl. Sudirman No. 12, Jakarta Pusat',
    'Jl. Merdeka No. 45, Bandung',
    'Jl. Diponegoro No. 8, Surabaya',
    'Jl. Malioboro No. 100, Yogyakarta',
    'Jl. Pemuda No. 33, Semarang',
];
const GRADS     = ['grad-purple','grad-green','grad-orange','grad-pink','grad-blue','grad-teal'];
const AV_COLORS = ['#6366F1','#10B981','#F59E0B','#EC4899','#3B82F6','#8B5CF6','#F97316','#06B6D4','#14B8A6','#EF4444'];
const NAMES = ['Budi Santoso','Siti Rahayu','Ahmad Fauzi','Maya Anggraini','Rizki Pratama','Dewi Lestari','Hendra Wijaya','Nita Kusuma','Fajar Nugroho','Rini Susanti','Eko Prasetyo','Linda Wati','Joko Susilo','Ani Supriyati','Dedi Kurniawan','Sri Wahyuni','Agus Purnomo','Yuli Astuti','Bambang Setiawan','Rina Marlina','Sigit Prabowo','Endah Rahayu','Wahyu Hidayat','Mega Novita','Doni Firmansyah','Lina Susanti','Aris Setiadi','Wulan Sari','Fandi Ahmad','Tiara Kusuma','Hendri Gunawan','Suci Ramdani','Dika Pratama','Putri Handayani','Roni Santoso','Fitri Rahayu','Bowo Nugroho','Sinta Dewi','Galih Permana','Eka Suryana'];

let allCustomers = NAMES.map((name, i) => {
    const tier    = TIERS[i % TIERS.length];
    const orders  = 3 + Math.floor(Math.abs(Math.sin(i * 7.3)) * 80);
    const avgAmt  = 25000 + Math.floor(Math.abs(Math.cos(i * 3.1)) * 150000);
    const total   = orders * avgAmt;
    const rating  = (3.5 + (i % 15) * 0.1).toFixed(1);
    const day     = 1  + (i % 28);
    const month   = 1  + (i % 12);
    const year    = 2022 + (i % 3);
    const lDay    = 20 + (i % 5);
    const recentOrders = Array.from({length:3}, (_,j) => ({
        id: `ORD-2024-${String(1248 - i * 3 - j).padStart(4,'0')}`,
        service: SERVICES[(i + j) % SERVICES.length],
        amount: avgAmt + j * 5000,
        date: `${2024 - (j > 1 ? 1 : 0)}-12-${String(24 - j * 3).padStart(2,'0')}`,
    }));
    return {
        id:   `CUS-${String(1000 + i).padStart(4,'0')}`,
        name, tier,
        phone: `08${String(10000000 + i * 12345678).slice(0,10)}`,
        email: name.toLowerCase().replace(/ /g,'.') + '@email.com',
        outlet: OUTLETS[i % OUTLETS.length],
        orders, total, avgOrder: avgAmt,
        rating: parseFloat(rating),
        joined: `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`,
        lastOrder: `2024-12-${String(lDay).padStart(2,'0')}`,
        address: ADDRESSES[i % ADDRESSES.length],
        favService: SERVICES[i % SERVICES.length],
        color:   AV_COLORS[i % AV_COLORS.length],
        grad:    GRADS[i % GRADS.length],
        notes:   i % 5 === 0 ? 'Pelanggan setia, selalu tepat waktu' : '',
        recentOrders,
    };
});

let filtered       = [...allCustomers];
let currentPage    = 1;
let perPage        = 10;
let selectedIds    = new Set();
let currentView    = 'table';
let activeCustomer = null;
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
function tierBadge(tier) {
    const map = {VIP:'tier-vip',Premium:'tier-premium',Reguler:'tier-reguler',Baru:'tier-baru'};
    const icons = {VIP:'👑',Premium:'💎',Reguler:'✅',Baru:'🌱'};
    return `<span class="tier-badge ${map[tier] || 'tier-reguler'}">${icons[tier] || ''} ${tier}</span>`;
}
function relativeDate(dateStr) {
    const diff = Math.floor((new Date() - new Date(dateStr)) / 86400000);
    if (diff === 0) return 'Hari ini';
    if (diff === 1) return 'Kemarin';
    if (diff < 30)  return `${diff} hari lalu`;
    if (diff < 365) return `${Math.floor(diff/30)} bulan lalu`;
    return `${Math.floor(diff/365)} tahun lalu`;
}

function applyFilters() {
    const q       = document.getElementById('searchInput').value.toLowerCase();
    const tier    = document.getElementById('filterTier').value;
    const outlet  = document.getElementById('filterOutlet').value;
    const sortVal = document.getElementById('filterSort').value;

    filtered = allCustomers.filter(c => {
        const mq = !q || c.name.toLowerCase().includes(q) || c.phone.includes(q) || c.email.toLowerCase().includes(q) || c.id.toLowerCase().includes(q);
        const mt = !tier   || c.tier === tier;
        const mo = !outlet || c.outlet === outlet;
        return mq && mt && mo;
    });

    if (sortVal === 'name-asc')     filtered.sort((a,b) => a.name.localeCompare(b.name));
    else if (sortVal === 'name-desc')  filtered.sort((a,b) => b.name.localeCompare(a.name));
    else if (sortVal === 'order-desc') filtered.sort((a,b) => b.orders - a.orders);
    else if (sortVal === 'total-desc') filtered.sort((a,b) => b.total - a.total);
    else if (sortVal === 'recent')     filtered.sort((a,b) => new Date(b.joined) - new Date(a.joined));

    currentPage = 1;
    selectedIds.clear();
    updateBulkBar();
    render();
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
    filtered = [...allCustomers];
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
        if (col === 'lastOrder' || col === 'joined') { va = new Date(va); vb = new Date(vb); }
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
    const tbody = document.getElementById('custTableBody');
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
                    <div class="cust-avatar" style="background:${c.color}">
                        ${getInitials(c.name)}
                        <span class="cust-avatar-status ${c.tier==='VIP'?'vip':c.tier==='Baru'?'baru':c.tier==='Premium'?'vip':'aktif'}"></span>
                    </div>
                    <div>
                        <div class="cust-name">${c.name}</div>
                        <div class="cust-id">${c.id}</div>
                    </div>
                </div>
            </td>
            <td>${tierBadge(c.tier)}</td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email}</div>
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
    renderPagination();
}

function renderGrid() {
    const grid  = document.getElementById('custGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;
    const start = (currentPage - 1) * 12;
    const page  = filtered.slice(start, start + 12);

    if (!filtered.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(); return; }
    empty.style.display = 'none';

    grid.innerHTML = page.map(c => `
        <div class="cust-grid-card ${c.grad}">
            <div class="cust-grid-inner">
                <div class="cust-grid-top">
                    <input type="checkbox" class="custom-cb cust-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${tierBadge(c.tier)}
                </div>
                <div class="cust-grid-avatar-wrap">
                    <div class="cust-grid-avatar" style="background:${c.color}">${getInitials(c.name)}</div>
                    <div class="cust-grid-name">${c.name}</div>
                    <div class="cust-grid-id">${c.id}</div>
                </div>
                <div class="cust-grid-stats">
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">${c.orders}</div><div class="cust-grid-stat-lbl">Order</div></div>
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">${(c.total/1000000).toFixed(1)}jt</div><div class="cust-grid-stat-lbl">Belanja</div></div>
                    <div class="cust-grid-stat"><div class="cust-grid-stat-val">★ ${c.rating}</div><div class="cust-grid-stat-lbl">Rating</div></div>
                </div>
            </div>
            <div class="cust-grid-actions">
                <button class="cust-grid-btn cust-grid-btn-outline" onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="cust-grid-btn cust-grid-btn-primary" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i> Detail</button>
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

function bulkExport()  { showToast('info','Export',`Mengekspor ${selectedIds.size} data pelanggan`); }
function bulkMessage() { showToast('info','Pesan',`Mengirim pesan ke ${selectedIds.size} pelanggan`); }
function bulkDelete() {
    if (!confirm(`Hapus ${selectedIds.size} pelanggan yang dipilih?`)) return;
    selectedIds.forEach(id => { const i = allCustomers.findIndex(c => c.id === id); if(i>-1) allCustomers.splice(i,1); });
    selectedIds.clear();
    applyFilters();
    showToast('success','Dihapus','Pelanggan berhasil dihapus');
}

function openDrawer(id) {
    const c = allCustomers.find(x => x.id === id);
    if (!c) return;
    activeCustomer = c;

    document.getElementById('d-avatar').textContent      = getInitials(c.name);
    document.getElementById('d-avatar').style.background = c.color;
    document.getElementById('d-name').textContent        = c.name;
    document.getElementById('d-id').textContent          = c.id;
    document.getElementById('d-tier-wrap').innerHTML     = tierBadge(c.tier);
    document.getElementById('d-totalorders').textContent = c.orders;
    document.getElementById('d-totalspend').textContent  = (c.total/1000000).toFixed(1)+'jt';
    document.getElementById('d-rating').textContent      = '★ '+c.rating;
    document.getElementById('d-phone').textContent       = c.phone;
    document.getElementById('d-email').textContent       = c.email;
    document.getElementById('d-joined').textContent      = c.joined;
    document.getElementById('d-outlet').textContent      = c.outlet;
    document.getElementById('d-address').textContent     = c.address;
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

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentCustomer() { if (!activeCustomer) return; deleteById(activeCustomer.id); closeDrawer(); }
function editCurrentCustomer()   { if (!activeCustomer) return; openEditModal(activeCustomer.id); }
function newOrderFor(id) {
    const c = allCustomers.find(x => x.id === id);
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
    const c = allCustomers.find(x => x.id === id);
    if (!c) return;
    editMode = true; activeCustomer = c;
    document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
    document.getElementById('modalTitle').textContent    = 'Edit Pelanggan';
    document.getElementById('modalSubtitle').textContent = 'Perbarui data pelanggan';
    document.getElementById('f-name').value    = c.name;
    document.getElementById('f-phone').value   = c.phone;
    document.getElementById('f-email').value   = c.email;
    document.getElementById('f-address').value = c.address;
    document.getElementById('f-notes').value   = c.notes;
    document.getElementById('f-outlet').value  = c.outlet;
    document.getElementById('f-tier').value    = c.tier;
    document.getElementById('custModal').classList.add('show');
}

function saveCustomer() {
    const name  = document.getElementById('f-name').value.trim();
    const phone = document.getElementById('f-phone').value.trim();
    if (!name || !phone) { showToast('error','Validasi','Nama dan telepon wajib diisi'); return; }

    if (editMode && activeCustomer) {
        activeCustomer.name    = name;
        activeCustomer.phone   = phone;
        activeCustomer.email   = document.getElementById('f-email').value;
        activeCustomer.address = document.getElementById('f-address').value;
        activeCustomer.outlet  = document.getElementById('f-outlet').value;
        activeCustomer.tier    = document.getElementById('f-tier').value;
        activeCustomer.notes   = document.getElementById('f-notes').value;
        showToast('success','Diperbarui','Data pelanggan berhasil diperbarui');
    } else {
        const newC = {
            id: `CUS-${String(1000 + allCustomers.length).padStart(4,'0')}`,
            name, phone,
            email:   document.getElementById('f-email').value,
            outlet:  document.getElementById('f-outlet').value || OUTLETS[0],
            tier:    document.getElementById('f-tier').value,
            address: document.getElementById('f-address').value,
            notes:   document.getElementById('f-notes').value,
            orders: 0, total: 0, avgOrder: 0, rating: 5.0,
            joined: new Date().toISOString().slice(0,10),
            lastOrder: '-',
            favService: '-',
            color: AV_COLORS[allCustomers.length % AV_COLORS.length],
            grad:  GRADS[allCustomers.length % GRADS.length],
            recentOrders: [],
        };
        allCustomers.unshift(newC);
        showToast('success','Ditambahkan','Pelanggan baru berhasil ditambahkan');
    }
    closeModal('custModal');
    applyFilters();
}

function deleteById(id) {
    const c = allCustomers.find(x => x.id === id);
    if (!c) return;
    if (!confirm(`Hapus pelanggan ${c.name}?`)) return;
    const i = allCustomers.indexOf(c);
    if (i > -1) allCustomers.splice(i, 1);
    applyFilters();
    showToast('success','Dihapus',`${c.name} berhasil dihapus`);
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
