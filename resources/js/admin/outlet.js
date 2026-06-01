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
let activeOutlet   = null;
let editMode       = false;
let sortCol        = 'joined';
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
function formatRp(n)       { return 'Rp ' + n.toLocaleString('id-ID'); }
function statusBadge(isActive) {
    return isActive
        ? `<span class="status-badge status-aktif">🟢 Aktif</span>`
        : `<span class="status-badge status-tutup">🔴 Tutup</span>`;
}

function showSkeletonTable() {
    const tbody = document.getElementById('outletTableBody');
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
            <td>
                <div class="skeleton skeleton-text long" style="height:14px;width:80px"></div>
                <div class="skeleton skeleton-text medium" style="height:8px;width:140px;margin-top:4px"></div>
            </td>
            <td><div class="skeleton skeleton-text medium" style="width:80px;height:14px"></div></td>
            <td><div class="skeleton skeleton-badge" style="width:60px;height:24px"></div></td>
            <td>
                <div class="skeleton skeleton-text medium" style="width:90px;height:14px"></div>
                <div class="skeleton skeleton-text short" style="height:8px;width:60px;margin:0"></div>
            </td>
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
    const grid = document.getElementById('outletGrid');
    if (!grid) return;
    
    let html = '';
    for (let i = 0; i < 12; i++) {
        html += `
        <div class="outlet-grid-card grad-purple" style="pointer-events:none;opacity:0.7">
            <div class="outlet-grid-inner">
                <div class="outlet-grid-top">
                    <div class="skeleton" style="width:18px;height:18px;border-radius:5px"></div>
                    <div class="skeleton skeleton-badge" style="width:70px;height:24px"></div>
                </div>
                <div class="outlet-grid-avatar-wrap">
                    <div class="skeleton skeleton-circle" style="width:72px;height:72px;border-radius:18px;margin:0 auto 0.5rem"></div>
                    <div class="skeleton skeleton-text long" style="height:16px;width:120px;display:block;margin:0 auto"></div>
                    <div class="skeleton skeleton-text short" style="height:8px;width:60px;display:block;margin:6px auto 0"></div>
                </div>
                <div class="outlet-grid-stats">
                    <div class="outlet-grid-stat">
                        <div class="skeleton skeleton-text" style="width:30px;height:14px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text" style="width:25px;height:8px;margin:4px auto 0"></div>
                    </div>
                    <div class="outlet-grid-stat">
                        <div class="skeleton skeleton-text" style="width:40px;height:14px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text" style="width:25px;height:8px;margin:4px auto 0"></div>
                    </div>
                    <div class="outlet-grid-stat">
                        <div class="skeleton skeleton-text" style="width:30px;height:14px;margin:0 auto"></div>
                        <div class="skeleton skeleton-text" style="width:25px;height:8px;margin:4px auto 0"></div>
                    </div>
                </div>
            </div>
            <div class="outlet-grid-actions">
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
            </div>
        </div>`;
    }
    grid.innerHTML = html;
}

function applyFilters() {
    const q       = document.getElementById('searchInput').value;
    const status  = document.getElementById('filterStatus').value;
    const city    = document.getElementById('filterCity').value;
    const sortVal = document.getElementById('filterSort').value;
    
    const limit = currentView === 'table' ? perPage : 12;

    if (currentView === 'table') showSkeletonTable();
    else showSkeletonGrid();

    $.ajax({
        url: '/outlets',
        method: 'GET',
        data: {
            search: q,
            status: status,
            city: city,
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
                    
                    const totalOutletsEl = document.getElementById('statTotalOutlets');
                    if (totalOutletsEl) totalOutletsEl.textContent = stats.total_outlets;
                    
                    const trendCitiesEl = document.getElementById('statTrendCities');
                    if (trendCitiesEl) trendCitiesEl.innerHTML = `<i class="fas fa-city"></i> ${stats.cities_count} Kota`;
                    
                    const footerCitiesEl = document.getElementById('statFooterCities');
                    if (footerCitiesEl) footerCitiesEl.textContent = `Tersebar di ${stats.cities_count} kota besar`;
                    
                    const activeOutletsEl = document.getElementById('statActiveOutlets');
                    if (activeOutletsEl) activeOutletsEl.textContent = stats.active_outlets;
                    
                    const trendActiveEl = document.getElementById('statTrendActive');
                    if (trendActiveEl) trendActiveEl.innerHTML = `<i class="fas fa-arrow-up"></i> ${stats.active_percentage}%`;
                    
                    const maintenanceOutletsEl = document.getElementById('statMaintenanceOutlets');
                    if (maintenanceOutletsEl) maintenanceOutletsEl.textContent = stats.maintenance_outlets;
                    
                    const trendMaintenanceEl = document.getElementById('statTrendMaintenance');
                    if (trendMaintenanceEl) trendMaintenanceEl.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${stats.maintenance_outlets} Unit`;
                    
                    const totalEmployeesEl = document.getElementById('statTotalEmployees');
                    if (totalEmployeesEl) totalEmployeesEl.textContent = stats.total_employees;
                    
                    const trendEmployeesEl = document.getElementById('statTrendEmployees');
                    if (trendEmployeesEl) trendEmployeesEl.innerHTML = `<i class="fas fa-users"></i> ${stats.total_outlets > 0 ? 'Aktif' : '0%'}`;

                    // Rebuild cities dropdown options
                    const filterCity = document.getElementById('filterCity');
                    if (filterCity && stats.cities) {
                        const currentVal = filterCity.value;
                        let optionsHtml = '<option value="">Semua Lokasi</option>';
                        stats.cities.forEach(c => {
                            optionsHtml += `<option value="${c}" ${c === currentVal ? 'selected' : ''}>${c}</option>`;
                        });
                        filterCity.innerHTML = optionsHtml;
                    }
                }
                
                render(meta);
            }
        },
        error: function() {
            showToast('Gagal memuat data outlet dari server', 'error', 'Error');
        }
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterCity').value   = '';
    document.getElementById('filterSort').value   = 'recent';
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
        'revenue': 'revenue-desc',
        'staffCount': 'staff-desc',
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
    const tbody = document.getElementById('outletTableBody');
    const empty = document.getElementById('emptyState');
    if (!tbody) return;

    document.getElementById('totalCount').textContent = meta.total.toLocaleString();
    document.getElementById('showCount').textContent  = pageData.length;

    if (!pageData.length) { tbody.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';

    tbody.innerHTML = pageData.map(c => `
        <tr>
            <td class="cb-cell"><input type="checkbox" class="custom-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)"></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="outlet-avatar" style="background:#6366F1">
                        ${getInitials(c.name)}
                        <span class="outlet-avatar-status ${c.is_active?'aktif':'tutup'}"></span>
                    </div>
                    <div>
                        <div class="outlet-name">${c.name}</div>
                        <div class="outlet-id">${c.code}</div>
                    </div>
                </div>
            </td>
            <td>${statusBadge(c.is_active)}</td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email || '-'}</div>
            </td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.city}</div>
                <div style="font-size:.72rem;color:var(--gray-light);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${c.address}">${c.address || '-'}</div>
            </td>
            <td><span style="font-weight:600;color:var(--dark)">${c.manager || '-'}</span></td>
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
    renderPagination(meta.last_page);
}

function renderGrid(meta) {
    const grid  = document.getElementById('outletGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;

    if (!pageData.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(1); return; }
    empty.style.display = 'none';

    const grads = ['grad-purple', 'grad-green', 'grad-orange', 'grad-pink', 'grad-blue', 'grad-teal'];

    grid.innerHTML = pageData.map((c, i) => `
        <div class="outlet-grid-card ${grads[i % grads.length]}">
            <div class="outlet-grid-inner">
                <div class="outlet-grid-top">
                    <input type="checkbox" class="custom-cb outlet-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${statusBadge(c.is_active)}
                </div>
                <div class="outlet-grid-avatar-wrap">
                    <div class="outlet-grid-avatar" style="background:#6366F1">${getInitials(c.name)}</div>
                    <div class="outlet-grid-name">${c.name}</div>
                    <div class="outlet-grid-id">${c.code}</div>
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

    renderGridPagination(meta.last_page);
}

function renderPagination(lastPage) {
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent  = lastPage;
    buildPageControls('paginationControls', lastPage, goPage);
}

function renderGridPagination(lastPage) {
    document.getElementById('gridCurPage').textContent   = currentPage;
    document.getElementById('gridTotalPages').textContent = lastPage;
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

function goPage(p) {
    currentPage = p;
    applyFilters();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function changePerPage(val) { perPage = parseInt(val); currentPage = 1; applyFilters(); }

function toggleAllCheck() {
    const checked = document.getElementById('checkAll').checked;
    pageData.forEach(c => { checked ? selectedIds.add(c.id) : selectedIds.delete(c.id); });
    renderTable({ total: totalItems, last_page: Math.ceil(totalItems/perPage) });
    updateBulkBar();
}

function toggleRowCheck(cb) {
    const id = cb.dataset.id;
    cb.checked ? selectedIds.add(id) : selectedIds.delete(id);
    syncCheckAll();
    updateBulkBar();
}

// Global exposure
window.toggleRowCheck = toggleRowCheck;

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

function bulkExport()  { showToast('info','Export',`Mengekspor ${selectedIds.size} data outlet`); }
function bulkDelete() {
    showConfirm('Konfirmasi Hapus', `Apakah Anda yakin ingin menghapus ${selectedIds.size} outlet yang dipilih?`, () => {
        let promises = [];
        selectedIds.forEach(id => {
            promises.push(
                $.ajax({
                    url: '/outlets/' + id,
                    method: 'DELETE'
                })
            );
        });
        
        return Promise.all(promises).then(() => {
            selectedIds.clear();
            applyFilters();
            showToast('Outlet berhasil dihapus', 'success', 'Dihapus');
        }).catch(() => {
            showToast('Beberapa outlet gagal dihapus', 'error', 'Error');
            throw new Error();
        });
    });
}

function openDrawer(id) {
    $.ajax({
        url: '/outlets/' + id,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const c = res.data;
                activeOutlet = c;

                document.getElementById('d-avatar').textContent      = getInitials(c.name);
                document.getElementById('d-name').textContent        = c.name;
                document.getElementById('d-id').textContent          = c.code;
                document.getElementById('d-status-wrap').innerHTML   = statusBadge(c.is_active);
                document.getElementById('d-totalorders').textContent = c.orders;
                document.getElementById('d-totalrevenue').textContent = formatRp(c.revenue);
                document.getElementById('d-staffcount').textContent  = c.staffCount;
                document.getElementById('d-phone').textContent       = c.phone;
                document.getElementById('d-email').textContent       = c.email || '-';
                document.getElementById('d-joined').textContent      = c.joined;
                document.getElementById('d-city').textContent        = c.city;
                document.getElementById('d-address').textContent     = c.address || '-';
                document.getElementById('d-manager').textContent     = c.manager || '-';

                let employeesHtml = '';
                if (c.employees && c.employees.length > 0) {
                    employeesHtml = c.employees.map(o => `
                        <div class="drawer-employee-item">
                            <div class="drawer-employee-avatar">${getInitials(o.name)}</div>
                            <div class="drawer-employee-info">
                                <div class="drawer-employee-name">${o.name}</div>
                                <div class="drawer-employee-role">${o.role}</div>
                            </div>
                            <div>
                                ${statusBadge(o.is_active)}
                            </div>
                        </div>`).join('');
                } else {
                    employeesHtml = `
                        <div style="text-align: center; padding: 2rem 1rem; color: var(--gray-light); font-size: 0.875rem;">
                            <i class="fas fa-users-slash" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                            Belum ada karyawan terdaftar
                        </div>`;
                }

                document.getElementById('d-recent-employees').innerHTML = employeesHtml;

                document.getElementById('drawerOverlay').classList.add('show');
            }
        }
    });
}

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentOutlet() { if (!activeOutlet) return; deleteById(activeOutlet.id); closeDrawer(); }
function editCurrentOutlet()   { if (!activeOutlet) return; openEditModal(activeOutlet.id); }

function openAddModal() {
    editMode = false;
    activeOutlet = null;
    document.getElementById('modalIcon').innerHTML     = '<i class="fas fa-store"></i>';
    document.getElementById('modalTitle').textContent  = 'Tambah Outlet';
    document.getElementById('modalSubtitle').textContent = 'Isi data outlet baru';
    ['f-name','f-phone','f-email','f-address','f-manager','f-notes'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-city').value   = '';
    document.getElementById('f-status').value = 'Aktif';
    document.getElementById('outletModal').classList.add('show');
}

function openEditModal(id) {
    $.ajax({
        url: '/outlets/' + id,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const c = res.data;
                editMode = true; activeOutlet = c;
                document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
                document.getElementById('modalTitle').textContent    = 'Edit Outlet';
                document.getElementById('modalSubtitle').textContent = 'Perbarui data outlet';
                document.getElementById('f-name').value    = c.name;
                document.getElementById('f-phone').value   = c.phone;
                document.getElementById('f-email').value   = c.email || '';
                document.getElementById('f-address').value = c.address || '';
                document.getElementById('f-city').value    = c.city;
                document.getElementById('f-manager').value = c.manager || '';
                document.getElementById('f-status').value  = c.is_active ? 'Aktif' : 'Tutup';
                document.getElementById('f-notes').value   = '';
                document.getElementById('outletModal').classList.add('show');
            }
        }
    });
}

function saveOutlet() {
    const name  = document.getElementById('f-name').value.trim();
    const phone = document.getElementById('f-phone').value.trim();
    const city  = document.getElementById('f-city').value.trim();
    if (!name || !phone || !city) { showToast('Nama, telepon, dan kota wajib diisi', 'error', 'Validasi'); return; }

    const statusVal = document.getElementById('f-status').value;

    const payload = {
        name: name,
        phone: phone,
        email: document.getElementById('f-email').value,
        address: document.getElementById('f-address').value,
        city: city,
        manager: document.getElementById('f-manager').value,
        is_active: statusVal === 'Aktif' ? 1 : 0
    };

    const btn = document.getElementById('saveOutletBtn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';

    const url = editMode && activeOutlet ? '/outlets/' + activeOutlet.id : '/outlets';
    const method = editMode && activeOutlet ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: payload,
        success: function(res) {
            if (res.success) {
                showToast(res.message, 'success', editMode ? 'Diperbarui' : 'Ditambahkan');
                closeModal('outletModal');
                applyFilters();
            }
        },
        error: function(xhr) {
            const err = xhr.responseJSON;
            showToast(err && err.message ? err.message : 'Terjadi kesalahan sistem', 'error', 'Gagal');
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

function deleteById(id) {
    showConfirm('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus outlet ini?', () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/outlets/' + id,
                method: 'DELETE',
                success: function(res) {
                    if (res.success) {
                        applyFilters();
                        showToast('Outlet berhasil dihapus', 'success', 'Dihapus');
                        resolve();
                    } else {
                        reject();
                    }
                },
                error: function() {
                    showToast('Gagal menghapus outlet', 'error', 'Error');
                    reject();
                }
            });
        });
    });
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function closeModalOutside(e, id) { if (e.target === e.currentTarget) closeModal(id); }

function exportData() { showToast('Mengekspor data outlet...', 'info', 'Export'); }

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
