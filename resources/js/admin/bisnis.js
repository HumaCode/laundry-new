$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

let currentPage    = 1;
let perPage        = 10;
let selectedIds    = new Set();
let currentView    = 'table';
let activeBusiness = null;
let editMode       = false;
let sortCol        = 'recent';
let sortDir        = 'desc';
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
function statusBadge(isActive) {
    return isActive
        ? `<span class="status-badge status-aktif">🟢 Aktif</span>`
        : `<span class="status-badge status-nonaktif">🔴 Tidak Aktif</span>`;
}

function showSkeletonTable() {
    const tbody = document.getElementById('businessTableBody');
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
            <td><div class="skeleton skeleton-text medium" style="height:14px;width:100px"></div></td>
            <td>
                <div class="skeleton skeleton-text medium" style="height:14px;width:100px"></div>
                <div class="skeleton skeleton-text short" style="height:8px;width:120px;margin:0"></div>
            </td>
            <td><div class="skeleton skeleton-badge" style="width:60px;height:24px"></div></td>
            <td><div class="skeleton skeleton-text medium" style="width:80px;height:14px"></div></td>
            <td>
                <div class="action-cell">
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
    const grid = document.getElementById('businessGrid');
    if (!grid) return;
    
    let html = '';
    for (let i = 0; i < 12; i++) {
        html += `
        <div class="business-grid-card grad-purple" style="pointer-events:none;opacity:0.7">
            <div class="business-grid-inner">
                <div class="business-grid-top" style="margin-bottom:2.5rem">
                    <div class="skeleton" style="width:18px;height:18px;border-radius:5px"></div>
                    <div class="skeleton skeleton-badge" style="width:70px;height:24px"></div>
                </div>
                <div class="business-grid-avatar-wrap">
                    <div class="skeleton skeleton-circle" style="width:72px;height:72px;border-radius:18px;margin:0 auto 0.5rem"></div>
                    <div class="skeleton skeleton-text long" style="height:16px;width:120px;display:block;margin:0 auto"></div>
                    <div class="skeleton skeleton-text short" style="height:8px;width:60px;display:block;margin:6px auto 0"></div>
                </div>
                <div class="business-grid-stats" style="margin-top:1.5rem">
                    <div class="business-grid-stat">
                        <div class="skeleton skeleton-text short" style="height:14px;width:30px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text short" style="height:8px;width:40px;margin:4px auto 0"></div>
                    </div>
                    <div class="business-grid-stat">
                        <div class="skeleton skeleton-text short" style="height:14px;width:30px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text short" style="height:8px;width:40px;margin:4px auto 0"></div>
                    </div>
                    <div class="business-grid-stat">
                        <div class="skeleton skeleton-text short" style="height:14px;width:30px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text short" style="height:8px;width:40px;margin:4px auto 0"></div>
                    </div>
                </div>
            </div>
            <div class="business-grid-actions">
                <div class="skeleton skeleton-btn" style="flex:1;height:32px"></div>
                <div class="skeleton skeleton-btn" style="flex:1;height:32px"></div>
            </div>
        </div>`;
    }
    grid.innerHTML = html;
}

function applyFilters() {
    const q       = document.getElementById('searchInput').value;
    const status  = document.getElementById('filterStatus').value;
    const sortVal = document.getElementById('filterSort').value;
    const limit   = currentView === 'table' ? perPage : 12;

    if (currentView === 'table') {
        showSkeletonTable();
    } else {
        showSkeletonGrid();
    }

    $.ajax({
        url: '/businesses',
        method: 'GET',
        data: { search: q, status, sort: sortVal, per_page: limit, page: currentPage },
        success: function(res) {
            if (!res.success) return;
            pageData   = res.data.data;
            const meta = res.data.meta;
            totalItems = meta.total;
            currentPage = meta.current_page;

            if (res.data.stats) {
                const s = res.data.stats;
                setEl('statTotalBusinesses',  s.total_businesses);
                setEl('statActiveBusinesses', s.active_businesses);
                setEl('statInactiveBusinesses', s.inactive_businesses);
                setEl('statTotalOutlets',     s.total_outlets);
                const trendEl = document.getElementById('statTrendActive');
                if (trendEl) trendEl.innerHTML = `<i class="fas fa-arrow-up"></i> ${s.active_percentage}%`;
                const citiesEl = document.getElementById('statTrendCities');
                if (citiesEl) citiesEl.innerHTML = `<i class="fas fa-city"></i> ${s.cities_count} Kota`;
            }
            render(meta);
        },
        error: () => showToast('Gagal memuat data bisnis', 'error', 'Error')
    });
}

function setEl(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterSort').value   = 'recent';
    currentPage = 1; selectedIds.clear(); updateBulkBar(); applyFilters();
}

function render(meta) { currentView === 'table' ? renderTable(meta) : renderGrid(meta); }

function renderTable(meta) {
    const tbody = document.getElementById('businessTableBody');
    const empty = document.getElementById('emptyState');
    if (!tbody) return;
    document.getElementById('totalCount').textContent = meta.total.toLocaleString();
    document.getElementById('showCount').textContent  = pageData.length;
    if (!pageData.length) { tbody.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';

    const grads = ['#6366F1','#10B981','#F59E0B','#EC4899','#3B82F6','#14B8A6'];
    tbody.innerHTML = pageData.map((c, i) => `
        <tr>
            <td class="cb-cell"><input type="checkbox" class="custom-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)"></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="business-avatar" style="background:${grads[i%grads.length]}">
                        ${getInitials(c.name)}
                        <span class="business-avatar-status ${c.is_active?'aktif':'nonaktif'}"></span>
                    </div>
                    <div>
                        <div class="business-name">${c.name}</div>
                        <div class="business-id">${c.code}</div>
                    </div>
                </div>
            </td>
            <td>${statusBadge(c.is_active)}</td>
            <td><span style="font-weight:600;color:var(--dark)">${c.owner || '-'}</span></td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone || '-'}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email || '-'}</div>
            </td>
            <td><span class="staff-count-badge">${c.outlets_count} outlet</span></td>
            <td><span style="font-size:.875rem;color:var(--gray)">${c.city || '-'}</span></td>
            <td>
                <div class="action-cell">
                    <button class="act-btn act-btn-view"   title="Detail" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i></button>
                    <button class="act-btn act-btn-edit"   title="Edit"   onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i></button>
                    <button class="act-btn act-btn-delete" title="Hapus"  onclick="deleteById('${c.id}')"><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
        </tr>`).join('');

    syncCheckAll();
    renderPagination(meta.last_page);
}

function renderGrid(meta) {
    const grid  = document.getElementById('businessGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;
    if (!pageData.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(1); return; }
    empty.style.display = 'none';

    const grads = ['grad-purple','grad-green','grad-orange','grad-pink','grad-blue','grad-teal'];
    grid.innerHTML = pageData.map((c, i) => `
        <div class="business-grid-card ${grads[i%grads.length]}">
            <div class="business-grid-inner">
                <div class="business-grid-top">
                    <input type="checkbox" class="custom-cb business-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${statusBadge(c.is_active)}
                </div>
                <div class="business-grid-avatar-wrap">
                    <div class="business-grid-avatar" style="background:#6366F1">${getInitials(c.name)}</div>
                    <div class="business-grid-name">${c.name}</div>
                    <div class="business-grid-id">${c.code}</div>
                </div>
                <div class="business-grid-stats">
                    <div class="business-grid-stat">
                        <div class="business-grid-stat-val">${c.outlets_count}</div>
                        <div class="business-grid-stat-lbl">Outlet</div>
                    </div>
                    <div class="business-grid-stat">
                        <div class="business-grid-stat-val" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80px" title="${c.owner || '-'}">${c.owner || '-'}</div>
                        <div class="business-grid-stat-lbl">Pemilik</div>
                    </div>
                    <div class="business-grid-stat">
                        <div class="business-grid-stat-val" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80px" title="${c.city || '-'}">${c.city || '-'}</div>
                        <div class="business-grid-stat-lbl">Kota</div>
                    </div>
                </div>
            </div>
            <div class="business-grid-actions">
                <button class="business-grid-btn business-grid-btn-outline" onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="business-grid-btn business-grid-btn-primary" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i> Detail</button>
            </div>
        </div>`).join('');

    renderGridPagination(meta.last_page);
}

function renderPagination(lastPage) {
    setEl('currentPage', currentPage); setEl('totalPages', lastPage);
    buildPageControls('paginationControls', lastPage, goPage);
}
function renderGridPagination(lastPage) {
    setEl('gridCurPage', currentPage); setEl('gridTotalPages', lastPage);
    buildPageControls('gridPaginationControls', lastPage, goPage);
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
function goPage(p) { currentPage = p; applyFilters(); window.scrollTo({top:0,behavior:'smooth'}); }
function changePerPage(val) { perPage = parseInt(val); currentPage = 1; applyFilters(); }

function toggleAllCheck() {
    const checked = document.getElementById('checkAll').checked;
    pageData.forEach(c => { checked ? selectedIds.add(c.id) : selectedIds.delete(c.id); });
    renderTable({ total: totalItems, last_page: Math.ceil(totalItems/perPage) });
    updateBulkBar();
}
function toggleRowCheck(cb) { const id = cb.dataset.id; cb.checked ? selectedIds.add(id) : selectedIds.delete(id); syncCheckAll(); updateBulkBar(); }
window.toggleRowCheck = toggleRowCheck;
function syncCheckAll() { const cb = document.getElementById('checkAll'); if (cb) cb.checked = pageData.length > 0 && pageData.every(c => selectedIds.has(c.id)); }
function clearSelection() { selectedIds.clear(); applyFilters(); updateBulkBar(); }
function updateBulkBar() {
    const n = selectedIds.size;
    setEl('bulkCountText', n);
    const bb = document.getElementById('bulkBar'); if (bb) bb.classList.toggle('show', n > 0);
}
function bulkExport() { showToast('Mengekspor data bisnis...', 'info', 'Export'); }
function bulkDelete() {
    showConfirm('Konfirmasi Hapus', `Apakah Anda yakin ingin menghapus ${selectedIds.size} bisnis yang dipilih?`, () => {
        return Promise.all([...selectedIds].map(id => $.ajax({ url: '/businesses/' + id, method: 'DELETE' })))
            .then(() => { selectedIds.clear(); applyFilters(); showToast('Bisnis berhasil dihapus', 'success', 'Dihapus'); })
            .catch(() => { showToast('Beberapa bisnis gagal dihapus', 'error', 'Error'); throw new Error(); });
    });
}

function openDrawer(id) {
    $.ajax({
        url: '/businesses/' + id,
        method: 'GET',
        success: function(res) {
            if (!res.success) return;
            const c = res.data;
            activeBusiness = c;
            document.getElementById('d-avatar').textContent    = getInitials(c.name);
            document.getElementById('d-name').textContent      = c.name;
            document.getElementById('d-id').textContent        = c.code;
            document.getElementById('d-status-wrap').innerHTML = statusBadge(c.is_active);
            setEl('d-outletcount', c.outlets_count);
            setEl('d-activeoutlets', c.active_outlets);
            setEl('d-employees', c.total_employees);
            setEl('d-owner', c.owner || '-');
            setEl('d-phone', c.phone || '-');
            setEl('d-email', c.email || '-');
            setEl('d-city',  c.city  || '-');
            setEl('d-description', c.description || '-');
            setEl('d-address', c.address || '-');

            let outletHtml = '';
            if (c.outlets && c.outlets.length > 0) {
                outletHtml = c.outlets.map(o => `
                    <div class="drawer-employee-item">
                        <div class="drawer-employee-avatar"><i class="fas fa-store"></i></div>
                        <div class="drawer-employee-info">
                            <div class="drawer-employee-name">${o.name}</div>
                            <div class="drawer-employee-role">${o.code} · ${o.city || '-'} · ${o.employees_count} karyawan</div>
                        </div>
                        <div>${statusBadge(o.is_active)}</div>
                    </div>`).join('');
            } else {
                outletHtml = `<div style="text-align:center;padding:2rem 1rem;color:var(--gray-light);font-size:.875rem"><i class="fas fa-store-slash" style="font-size:1.5rem;margin-bottom:.5rem;display:block;opacity:.5"></i>Belum ada outlet terdaftar</div>`;
            }
            document.getElementById('d-outlet-list').innerHTML = outletHtml;
            document.getElementById('drawerOverlay').classList.add('show');
        }
    });
}

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentBusiness() { if (!activeBusiness) return; deleteById(activeBusiness.id); closeDrawer(); }
function editCurrentBusiness()   { if (!activeBusiness) return; openEditModal(activeBusiness.id); }

function openAddModal() {
    editMode = false; activeBusiness = null;
    document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-building"></i>';
    document.getElementById('modalTitle').textContent    = 'Tambah Bisnis';
    document.getElementById('modalSubtitle').textContent = 'Isi data bisnis baru';
    ['f-name','f-owner','f-phone','f-email','f-city','f-description','f-address'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    document.getElementById('f-status').value = 'Aktif';
    document.getElementById('businessModal').classList.add('show');
}

function openEditModal(id) {
    $.ajax({
        url: '/businesses/' + id,
        method: 'GET',
        success: function(res) {
            if (!res.success) return;
            const c = res.data; editMode = true; activeBusiness = c;
            document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
            document.getElementById('modalTitle').textContent    = 'Edit Bisnis';
            document.getElementById('modalSubtitle').textContent = 'Perbarui data bisnis';
            document.getElementById('f-name').value        = c.name;
            document.getElementById('f-owner').value       = c.owner || '';
            document.getElementById('f-phone').value       = c.phone || '';
            document.getElementById('f-email').value       = c.email || '';
            document.getElementById('f-city').value        = c.city  || '';
            document.getElementById('f-description').value = c.description || '';
            document.getElementById('f-address').value     = c.address || '';
            document.getElementById('f-status').value      = c.is_active ? 'Aktif' : 'Tidak Aktif';
            document.getElementById('businessModal').classList.add('show');
        }
    });
}

function saveBusiness() {
    const name = document.getElementById('f-name').value.trim();
    if (!name) { showToast('Nama bisnis wajib diisi', 'error', 'Validasi'); return; }

    const statusVal = document.getElementById('f-status').value;
    const payload = {
        name,
        owner:       document.getElementById('f-owner').value,
        phone:       document.getElementById('f-phone').value,
        email:       document.getElementById('f-email').value,
        city:        document.getElementById('f-city').value,
        description: document.getElementById('f-description').value,
        address:     document.getElementById('f-address').value,
        is_active:   statusVal === 'Aktif' ? 1 : 0
    };

    const btn = document.getElementById('saveBusinessBtn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';

    $.ajax({
        url: editMode && activeBusiness ? '/businesses/' + activeBusiness.id : '/businesses',
        method: editMode && activeBusiness ? 'PUT' : 'POST',
        data: payload,
        success: function(res) {
            if (res.success) {
                showToast(res.message, 'success', editMode ? 'Diperbarui' : 'Ditambahkan');
                closeModal('businessModal'); applyFilters();
            }
        },
        error: function(xhr) {
            const err = xhr.responseJSON;
            showToast(err && err.message ? err.message : 'Terjadi kesalahan sistem', 'error', 'Gagal');
        },
        complete: function() { btn.disabled = false; btn.innerHTML = orig; }
    });
}

function deleteById(id) {
    showConfirm('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus bisnis ini?', () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/businesses/' + id,
                method: 'DELETE',
                success: function(res) { if (res.success) { applyFilters(); showToast('Bisnis berhasil dihapus', 'success', 'Dihapus'); resolve(); } else reject(); },
                error: function() { showToast('Gagal menghapus bisnis', 'error', 'Error'); reject(); }
            });
        });
    });
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function closeModalOutside(e, id) { if (e.target === e.currentTarget) closeModal(id); }
function exportData() { showToast('Mengekspor data bisnis...', 'info', 'Export'); }

document.addEventListener('DOMContentLoaded', () => {
    applyFilters();
    window.addEventListener('scroll', () => {
        const btn = document.getElementById('scrollTopBtn');
        if (btn) btn.classList.toggle('visible', window.scrollY > 300);
    });
});

window.switchView = switchView; window.applyFilters = applyFilters; window.resetFilters = resetFilters;
window.sortBy = sortBy; window.goPage = goPage; window.changePerPage = changePerPage;
window.toggleAllCheck = toggleAllCheck; window.clearSelection = clearSelection;
window.bulkExport = bulkExport; window.bulkDelete = bulkDelete;
window.openDrawer = openDrawer; window.closeDrawer = closeDrawer; window.closeDrawerOutside = closeDrawerOutside;
window.deleteCurrentBusiness = deleteCurrentBusiness; window.editCurrentBusiness = editCurrentBusiness;
window.openAddModal = openAddModal; window.openEditModal = openEditModal; window.saveBusiness = saveBusiness;
window.deleteById = deleteById; window.closeModal = closeModal; window.closeModalOutside = closeModalOutside;
window.exportData = exportData;

function sortBy(col) {
    if (sortCol === col) {
        sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    } else {
        sortCol = col;
        sortDir = 'asc';
    }

    document.querySelectorAll('.sort-icon').forEach(i => i.classList.remove('active'));
    const el = document.getElementById('si-' + col);
    if (el) el.classList.add('active');

    let sortVal = 'recent';
    if (col === 'name') {
        sortVal = sortDir === 'asc' ? 'name-asc' : 'name-desc';
    } else if (col === 'outlets') {
        sortVal = sortDir === 'asc' ? 'outlets-asc' : 'outlets-desc';
    } else if (col === 'status') {
        sortVal = sortDir === 'asc' ? 'status-asc' : 'status-desc';
    }

    const selectEl = document.getElementById('filterSort');
    if (selectEl) {
        selectEl.value = sortVal;
    }
    applyFilters();
}
