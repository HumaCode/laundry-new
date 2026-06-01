// Setup CSRF token for jQuery AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let currentPage      = 1;
let perPage          = 10;
let selectedIds      = new Set();
let currentView      = 'table';
let activeEmployee   = null;
let editMode         = false;
let sortCol          = 'joined';
let sortDir          = 'desc';
let pageData         = [];
let totalItems       = 0;

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
    const tbody = document.getElementById('employeeTableBody');
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
            <td><div class="skeleton skeleton-text short" style="height:8px;width:60px;margin:0"></div></td>
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
    const grid = document.getElementById('employeeGrid');
    if (!grid) return;
    
    let html = '';
    for (let i = 0; i < 12; i++) {
        html += `
        <div class="employee-grid-card grad-purple" style="pointer-events:none;opacity:0.7">
            <div class="employee-grid-inner">
                <div class="employee-grid-top" style="margin-bottom:2.5rem">
                    <div class="skeleton" style="width:18px;height:18px;border-radius:5px"></div>
                    <div class="skeleton skeleton-badge" style="width:70px;height:24px"></div>
                </div>
                <div class="employee-grid-avatar-wrap">
                    <div class="skeleton skeleton-circle" style="width:72px;height:72px;border-radius:18px;margin:0 auto 0.5rem"></div>
                    <div class="skeleton skeleton-text long" style="height:16px;width:120px;display:block;margin:0 auto"></div>
                    <div class="skeleton skeleton-text short" style="height:8px;width:60px;display:block;margin:6px auto 0"></div>
                </div>
            </div>
            <div class="employee-grid-actions">
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
            </div>
        </div>`;
    }
    grid.innerHTML = html;
}

function applyFilters() {
    const q        = document.getElementById('searchInput').value;
    const status   = document.getElementById('filterStatus').value;
    const outletId = document.getElementById('filterOutlet').value;
    const role     = document.getElementById('filterRole').value;
    const sortVal  = document.getElementById('filterSort').value;
    
    const limit = currentView === 'table' ? perPage : 12;

    if (currentView === 'table') showSkeletonTable();
    else showSkeletonGrid();

    $.ajax({
        url: '/employees',
        method: 'GET',
        data: {
            search: q,
            status: status,
            outlet_id: outletId,
            role: role,
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
                    
                    const totalEmployeesEl = document.getElementById('statTotalEmployees');
                    if (totalEmployeesEl) totalEmployeesEl.textContent = stats.total_employees;
                    
                    const activeEmployeesEl = document.getElementById('statActiveEmployees');
                    if (activeEmployeesEl) activeEmployeesEl.textContent = stats.active_employees;
                    
                    const trendActiveEl = document.getElementById('statTrendActive');
                    if (trendActiveEl) trendActiveEl.innerHTML = `<i class="fas fa-arrow-up"></i> ${stats.active_percentage}%`;
                    
                    const rolesCountEl = document.getElementById('statRolesCount');
                    if (rolesCountEl) rolesCountEl.textContent = stats.roles_count;
                    
                    const inactiveEmployeesEl = document.getElementById('statInactiveEmployees');
                    if (inactiveEmployeesEl) inactiveEmployeesEl.textContent = stats.inactive_employees;

                    // Rebuild roles dropdown options
                    const filterRole = document.getElementById('filterRole');
                    if (filterRole && stats.roles) {
                        const currentVal = filterRole.value;
                        let optionsHtml = '<option value="">Semua Peran</option>';
                        stats.roles.forEach(r => {
                            optionsHtml += `<option value="${r}" ${r === currentVal ? 'selected' : ''}>${r}</option>`;
                        });
                        filterRole.innerHTML = optionsHtml;
                    }
                }
                
                render(meta);
            }
        },
        error: function() {
            showToast('Gagal memuat data karyawan dari server', 'error', 'Error');
        }
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterOutlet').value = '';
    document.getElementById('filterRole').value   = '';
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
    const tbody = document.getElementById('employeeTableBody');
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
                    <div class="employee-avatar" style="background:#6366F1">
                        ${getInitials(c.name)}
                        <span class="employee-avatar-status ${c.is_active?'aktif':'nonaktif'}"></span>
                    </div>
                    <div>
                        <div class="employee-name">${c.name}</div>
                        <div class="employee-id">${c.code}</div>
                    </div>
                </div>
            </td>
            <td>${statusBadge(c.is_active)}</td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.phone}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${c.email || '-'}</div>
            </td>
            <td>
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${c.outlet ? c.outlet.name : 'Belum Ditentukan'}</div>
            </td>
            <td><span style="font-weight:600;color:var(--dark)">${c.role}</span></td>
            <td><span style="font-size:.85rem;color:var(--gray)">${c.joined_at_formatted || '-'}</span></td>
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
    const grid  = document.getElementById('employeeGrid');
    const empty = document.getElementById('emptyStateGrid');
    if (!grid) return;

    if (!pageData.length) { grid.innerHTML = ''; empty.style.display = ''; renderGridPagination(1); return; }
    empty.style.display = 'none';

    const grads = ['grad-purple', 'grad-green', 'grad-orange', 'grad-pink', 'grad-blue', 'grad-teal'];

    grid.innerHTML = pageData.map((c, i) => `
        <div class="employee-grid-card ${grads[i % grads.length]}">
            <div class="employee-grid-inner">
                <div class="employee-grid-top">
                    <input type="checkbox" class="custom-cb employee-grid-cb row-cb" data-id="${c.id}" ${selectedIds.has(c.id)?'checked':''} onchange="toggleRowCheck(this)">
                    ${statusBadge(c.is_active)}
                </div>
                <div class="employee-grid-avatar-wrap">
                    <div class="employee-grid-avatar" style="background:#6366F1">${getInitials(c.name)}</div>
                    <div class="employee-grid-name">${c.name}</div>
                    <div class="employee-grid-id">${c.code}</div>
                </div>
                <div class="employee-grid-stats" style="display:flex;flex-direction:column;gap:.25rem;align-items:center;margin-top:1rem;border-top:1px solid var(--border);padding-top:.75rem">
                    <div style="font-size:.8rem;color:var(--gray)"><i class="fas fa-store"></i> ${c.outlet ? c.outlet.name : '-'}</div>
                    <div style="font-size:.8rem;font-weight:700;color:var(--dark)"><i class="fas fa-user-tag"></i> ${c.role}</div>
                </div>
            </div>
            <div class="employee-grid-actions">
                <button class="employee-grid-btn employee-grid-btn-outline" onclick="openEditModal('${c.id}')"><i class="fas fa-pen"></i> Edit</button>
                <button class="employee-grid-btn employee-grid-btn-primary" onclick="openDrawer('${c.id}')"><i class="fas fa-eye"></i> Detail</button>
            </div>
        </div>`).join('');

    renderGridPagination(meta.last_page);
}

function renderPagination(lastPage) {
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent  = lastPage;
    buildPageControls('paginationControls', lastPage, 'goPage');
}

function renderGridPagination(lastPage) {
    document.getElementById('gridCurPage').textContent   = currentPage;
    document.getElementById('gridTotalPages').textContent = lastPage;
    buildPageControls('gridPaginationControls', lastPage, 'goPage');
}

function buildPageControls(containerId, total, fnName) {
    const el = document.getElementById(containerId);
    if (!el) return;
    let html = `<button class="page-btn" onclick="${fnName}(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    let s = Math.max(1, currentPage-2), e = Math.min(total, s+4);
    if (e-s<4) s = Math.max(1, e-4);
    if (s>1) { html += `<button class="page-btn" onclick="${fnName}(1)">1</button>`; if(s>2) html += `<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`; }
    for (let i=s; i<=e; i++) html += `<button class="page-btn ${i===currentPage?'active':''}" onclick="${fnName}(${i})">${i}</button>`;
    if (e<total) { if(e<total-1) html += `<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`; html += `<button class="page-btn" onclick="${fnName}(${total})">${total}</button>`; }
    html += `<button class="page-btn" onclick="${fnName}(${currentPage+1})" ${currentPage>=total?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
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

function bulkExport()  { showToast('Mengekspor data karyawan...', 'info', 'Export'); }
function bulkDelete() {
    showConfirm('Konfirmasi Hapus', `Apakah Anda yakin ingin menghapus ${selectedIds.size} karyawan yang dipilih?`, () => {
        let promises = [];
        selectedIds.forEach(id => {
            promises.push(
                $.ajax({
                    url: '/employees/' + id,
                    method: 'DELETE'
                })
            );
        });
        
        return Promise.all(promises).then(() => {
            selectedIds.clear();
            applyFilters();
            showToast('Karyawan berhasil dihapus', 'success', 'Dihapus');
        }).catch(() => {
            showToast('Beberapa karyawan gagal dihapus', 'error', 'Error');
            throw new Error();
        });
    });
}

function openDrawer(id) {
    $.ajax({
        url: '/employees/' + id,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const c = res.data;
                activeEmployee = c;

                document.getElementById('d-avatar').textContent      = getInitials(c.name);
                document.getElementById('d-name').textContent        = c.name;
                document.getElementById('d-id').textContent          = c.code;
                document.getElementById('d-status-wrap').innerHTML   = statusBadge(c.is_active);
                document.getElementById('d-phone').textContent       = c.phone;
                document.getElementById('d-email').textContent       = c.email || '-';
                document.getElementById('d-joined').textContent      = c.joined_at_formatted || '-';
                document.getElementById('d-outlet').textContent      = c.outlet ? c.outlet.name : 'Belum Ditentukan';
                document.getElementById('d-role').textContent        = c.role;
                document.getElementById('d-address').textContent     = c.address || '-';

                document.getElementById('drawerOverlay').classList.add('show');
            }
        }
    });
}

function closeDrawer() { document.getElementById('drawerOverlay').classList.remove('show'); }
function closeDrawerOutside(e) { if (e.target === e.currentTarget) closeDrawer(); }
function deleteCurrentEmployee() { if (!activeEmployee) return; deleteById(activeEmployee.id); closeDrawer(); }
function editCurrentEmployee()   { if (!activeEmployee) return; openEditModal(activeEmployee.id); }

function openAddModal() {
    editMode = false;
    activeEmployee = null;
    document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-user-check"></i>';
    document.getElementById('modalTitle').textContent    = 'Tambah Karyawan';
    document.getElementById('modalSubtitle').textContent = 'Isi data karyawan baru';
    ['f-name','f-phone','f-email','f-address'].forEach(id => document.getElementById(id).value = '');
    
    // Reset Select2 dropdowns
    $('#f-outlet').val('').trigger('change');
    $('#f-role').val('').trigger('change');

    // Clear Flatpickr date
    if (joinedAtPicker) {
        joinedAtPicker.clear();
        const clearBtn = document.getElementById('f-joined_at-clear');
        if (clearBtn) clearBtn.classList.remove('visible');
    }
    
    document.getElementById('f-status').value = 'Aktif';
    document.getElementById('employeeModal').classList.add('show');
}

function openEditModal(id) {
    $.ajax({
        url: '/employees/' + id,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const c = res.data;
                editMode = true; activeEmployee = c;
                document.getElementById('modalIcon').innerHTML       = '<i class="fas fa-pen"></i>';
                document.getElementById('modalTitle').textContent    = 'Edit Karyawan';
                document.getElementById('modalSubtitle').textContent = 'Perbarui data karyawan';
                document.getElementById('f-name').value      = c.name;
                document.getElementById('f-phone').value     = c.phone;
                document.getElementById('f-email').value     = c.email || '';
                document.getElementById('f-address').value   = c.address || '';
                
                // Update Select2 outlet
                $('#f-outlet').val(c.outlet_id || '').trigger('change');
                
                // Update Select2 role (dynamic tagging support)
                if (c.role) {
                    if ($('#f-role').find("option[value='" + c.role + "']").length === 0) {
                        var newOption = new Option(c.role, c.role, true, true);
                        $('#f-role').append(newOption).trigger('change');
                    } else {
                        $('#f-role').val(c.role).trigger('change');
                    }
                } else {
                    $('#f-role').val('').trigger('change');
                }

                // Set Flatpickr date
                const clearBtn = document.getElementById('f-joined_at-clear');
                if (joinedAtPicker) {
                    if (c.joined_at) {
                        joinedAtPicker.setDate(c.joined_at, false);
                        if (clearBtn) clearBtn.classList.add('visible');
                    } else {
                        joinedAtPicker.clear();
                        if (clearBtn) clearBtn.classList.remove('visible');
                    }
                }
                
                document.getElementById('f-status').value = c.is_active ? 'Aktif' : 'Tidak Aktif';
                document.getElementById('employeeModal').classList.add('show');
            }
        }
    });
}

function saveEmployee() {
    const name     = document.getElementById('f-name').value.trim();
    const phone    = document.getElementById('f-phone').value.trim();
    const outletId = document.getElementById('f-outlet').value;
    const role     = document.getElementById('f-role').value.trim();
    if (!name || !phone || !outletId || !role) { showToast('Nama, telepon, outlet, dan peran wajib diisi', 'error', 'Validasi'); return; }

    const statusVal = document.getElementById('f-status').value;

    const payload = {
        name: name,
        phone: phone,
        email: document.getElementById('f-email').value,
        address: document.getElementById('f-address').value,
        outlet_id: outletId,
        role: role,
        joined_at: document.getElementById('f-joined_at').value,
        is_active: statusVal === 'Aktif' ? 1 : 0
    };

    const btn = document.getElementById('saveEmployeeBtn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';

    const url = editMode && activeEmployee ? '/employees/' + activeEmployee.id : '/employees';
    const method = editMode && activeEmployee ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: payload,
        success: function(res) {
            if (res.success) {
                showToast(res.message, 'success', editMode ? 'Diperbarui' : 'Ditambahkan');
                closeModal('employeeModal');
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
    showConfirm('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus karyawan ini?', () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/employees/' + id,
                method: 'DELETE',
                success: function(res) {
                    if (res.success) {
                        applyFilters();
                        showToast('Karyawan berhasil dihapus', 'success', 'Dihapus');
                        resolve();
                    } else {
                        reject();
                    }
                },
                error: function() {
                    showToast('Gagal menghapus karyawan', 'error', 'Error');
                    reject();
                }
            });
        });
    });
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function closeModalOutside(e, id) { if (e.target === e.currentTarget) closeModal(id); }

function exportData() { showToast('Mengekspor data karyawan...', 'info', 'Export'); }

let joinedAtPicker = null;

document.addEventListener('DOMContentLoaded', () => {
    applyFilters();
    
    // Initialize Select2 dropdowns
    $('#f-outlet').select2({
        dropdownParent: $('#employeeModal'),
        placeholder: 'Pilih Cabang Outlet',
        allowClear: true
    });

    $('#f-role').select2({
        dropdownParent: $('#employeeModal'),
        placeholder: 'Pilih atau Ketik Peran',
        allowClear: true,
        tags: true
    });

    // Initialize Flatpickr for Tanggal Masuk Bekerja
    joinedAtPicker = flatpickr('#f-joined_at', {
        locale: 'id',
        dateFormat: 'Y-m-d',
        altInput: false,
        allowInput: false,
        disableMobile: true,
        maxDate: 'today',
        animate: false,
        monthSelectorType: 'static',
        showMonths: 1,
        static: false,
        appendTo: document.body,
        onReady(selectedDates, dateStr, instance) {
            instance.calendarContainer.style.zIndex = '99999';
        },
        onChange(selectedDates, dateStr) {
            const clearBtn = document.getElementById('f-joined_at-clear');
            if (clearBtn) {
                clearBtn.classList.toggle('visible', selectedDates.length > 0);
            }
        },
        onClose(selectedDates, dateStr, instance) {
            instance.input.classList.remove('active');
        },
        onOpen(selectedDates, dateStr, instance) {
            instance.input.classList.add('active');
        }
    });
    
    window.addEventListener('scroll', () => {
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        if (scrollTopBtn) {
            scrollTopBtn.classList.toggle('visible', window.scrollY > 300);
        }
    });
});

function clearDate() {
    if (joinedAtPicker) {
        joinedAtPicker.clear();
        const clearBtn = document.getElementById('f-joined_at-clear');
        if (clearBtn) clearBtn.classList.remove('visible');
    }
}


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
window.deleteCurrentEmployee = deleteCurrentEmployee;
window.editCurrentEmployee = editCurrentEmployee;
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.saveEmployee = saveEmployee;
window.deleteById = deleteById;
window.closeModal = closeModal;
window.closeModalOutside = closeModalOutside;
window.exportData = exportData;
window.clearDate = clearDate;

