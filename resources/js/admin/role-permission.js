// Constants
const ROLE_COLORS = ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6', '#EC4899', '#F97316', '#14B8A6', '#06B6D4'];
const ROLE_ICONS = [
    'fa-user-shield', 'fa-crown', 'fa-user-cog', 'fa-user-tie', 'fa-user-check', 
    'fa-user-lock', 'fa-user-tag', 'fa-users-cog', 'fa-shield-alt', 'fa-key',
    'fa-user-ninja', 'fa-star', 'fa-gem', 'fa-store', 'fa-truck', 
    'fa-chart-line', 'fa-wallet', 'fa-percent', 'fa-gift', 'fa-database', 
    'fa-cogs', 'fa-sliders-h', 'fa-address-card', 'fa-briefcase', 'fa-folder-open', 
    'fa-heart', 'fa-circle-check', 'fa-building', 'fa-user-astronaut', 'fa-user-graduate'
];

// State
let roles = [];
let menus = [];
let users = [];
let roleUsers = {};

let activeRoleId = null;
let editRoleId = null;
let selectedColor = '#6366F1';
let selectedIcon = 'fa-user-shield';
let copySourceId = null;
let selectedUsersToAssign = new Set();
let activeRolePermissions = new Set(); // Permissions currently checked for active role

// Fetch CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Document Ready / Init
document.addEventListener('DOMContentLoaded', () => {
    // Bootstrap state from PHP globals
    roles = window.INITIAL_ROLES || [];
    menus = window.INITIAL_MENUS || [];
    users = window.INITIAL_USERS || [];
    roleUsers = window.INITIAL_ROLE_USERS || {};

    // Build options inside modals
    const colorOptsEl = document.getElementById('colorOptions');
    if (colorOptsEl) colorOptsEl.innerHTML = buildColorOptions();

    const iconOptsEl = document.getElementById('iconOptions');
    if (iconOptsEl) iconOptsEl.innerHTML = buildIconOptions();

    // Auto-fill slug from name field
    const nameInput = document.getElementById('rm-name');
    const slugInput = document.getElementById('rm-slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', () => {
            slugInput.value = nameInput.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // remove non-alphanumeric except spaces & hyphens
                .replace(/\s+/g, '-')          // replace spaces with hyphens
                .replace(/-+/g, '-');          // replace multiple hyphens with single hyphen
        });
    }

    // Setup scroll to top listener
    window.addEventListener('scroll', () => {
        const scrollBtn = document.getElementById('scrollTopBtn');
        if (scrollBtn) {
            scrollBtn.classList.toggle('visible', window.scrollY > 300);
        }
    });

    renderRoles();

    // Auto-select first role if available
    if (roles.length > 0) {
        selectRole(roles[0].id);
    }
});

/* ─── HELPERS ─── */
function getInitials(name) {
    if (!name) return 'U';
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function showToast(type, title, msg) {
    if (typeof window.showToast === 'function') {
        window.showToast(msg, type, title);
    } else {
        const wrap = document.getElementById('toastWrap');
        if (!wrap) return;
        const t = document.createElement('div');
        t.className = 'toast';
        t.innerHTML = `
            <div class="toast-icon ${type}">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            </div>
            <div style="flex:1">
                <div class="toast-title">${title}</div>
                <div class="toast-msg">${msg}</div>
            </div>
            <button class="toast-x" onclick="this.closest('.toast').remove()">
                <i class="fas fa-times"></i>
            </button>`;
        wrap.appendChild(t);
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => {
            t.classList.remove('show');
            setTimeout(() => t.remove(), 400);
        }, 4500);
    }
}

/* ─── RENDER ROLES ─── */
function renderRoles() {
    const container = document.getElementById('roleList');
    if (!container) return;

    container.innerHTML = roles.map(r => {
        // Calculate perm count
        let activePermsCount = 0;
        if (r.id === activeRoleId) {
            activePermsCount = activeRolePermissions.size;
        } else {
            activePermsCount = Array.isArray(r.permissions) ? r.permissions.length : 0;
        }

        const totalPermsCount = menus.reduce((acc, m) => acc + (m.permissions ? m.permissions.length : 0), 0);
        const userCount = roleUsers[r.id] ? roleUsers[r.id].length : 0;

        return `
        <div class="role-card ${activeRoleId === r.id ? 'selected' : ''}" style="--role-color: ${r.color}" onclick="selectRole('${r.id}')">
            <div class="role-card-top">
                <div class="role-icon" style="background:${r.color}18;color:${r.color}">
                    <i class="fas ${r.icon || 'fa-user-shield'}"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">${r.name}</div>
                    <div class="role-desc">${r.description || 'Tidak ada deskripsi'}</div>
                </div>
                <div class="role-actions">
                    <button class="role-act-btn role-act-btn-copy" title="Salin Kehalaman" onclick="event.stopPropagation(); openCopyModalForRole('${r.id}')">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button class="role-act-btn role-act-btn-edit" title="Edit" onclick="event.stopPropagation(); openEditRoleModal('${r.id}')">
                        <i class="fas fa-pen"></i>
                    </button>
                    ${r.slug !== 'dev' && r.slug !== 'admin' ? `
                    <button class="role-act-btn role-act-btn-delete" title="Hapus" onclick="event.stopPropagation(); deleteRole('${r.id}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>` : ''}
                </div>
            </div>
            <div class="role-card-footer">
                <span class="role-users-badge"><i class="fas fa-users"></i> ${userCount} pengguna</span>
                <span class="role-perms-count">${activePermsCount}/${totalPermsCount} permission aktif</span>
            </div>
        </div>`;
    }).join('');
}

/* ─── SELECT ROLE ─── */
window.selectRole = function(id) {
    activeRoleId = id;
    const role = roles.find(r => r.id === id);
    if (!role) return;

    // Load permissions for this role
    activeRolePermissions = new Set(
        Array.isArray(role.permissions) ? role.permissions.map(p => p.name) : []
    );

    renderRoles();
    renderPermissions();
    renderUsers();
}

/* ─── RENDER PERMISSIONS ─── */
function renderPermissions() {
    const role = roles.find(r => r.id === activeRoleId);
    if (!role) return;

    // Update header info
    const srIcon = document.getElementById('sr-icon');
    if (srIcon) {
        srIcon.style.background = role.color + '22';
        srIcon.style.color = role.color;
        srIcon.innerHTML = `<i class="fas ${role.icon || 'fa-user-shield'}"></i>`;
    }
    const srName = document.getElementById('sr-name');
    if (srName) srName.textContent = role.name;

    const srDesc = document.getElementById('sr-desc');
    if (srDesc) srDesc.textContent = role.description || 'Tidak ada deskripsi';

    const body = document.getElementById('permBody');
    if (!body) return;

    if (menus.length === 0) {
        body.innerHTML = `
        <div style="padding:3rem;text-align:center;color:var(--gray-light)">
            <div style="font-size:3rem;margin-bottom:1rem;opacity:.25"><i class="fas fa-key"></i></div>
            <div style="font-size:1rem;font-weight:600;color:var(--gray)">Belum ada data permission/menu</div>
        </div>`;
        return;
    }

    body.innerHTML = menus.map(m => {
        const menuPermissions = m.permissions || [];
        const activeCount = menuPermissions.filter(p => activeRolePermissions.has(p.name)).length;

        // Custom category colors
        let catColor = 'var(--primary)';
        let catBg = 'rgba(99,102,241,.1)';
        if (m.category === 'OPERASIONAL') { catColor = 'var(--secondary)'; catBg = 'rgba(16,185,129,.1)'; }
        else if (m.category === 'KEUANGAN') { catColor = 'var(--pink)'; catBg = 'rgba(236,72,153,.1)'; }
        else if (m.category === 'ROLE PERMISSION') { catColor = 'var(--warning)'; catBg = 'rgba(245,158,11,.1)'; }
        else if (m.category === 'SISTEM') { catColor = 'var(--danger)'; catBg = 'rgba(239,68,68,.1)'; }

        return `
        <div class="perm-section open" id="ps-${m.id}">
            <div class="perm-section-header" onclick="toggleSection('${m.id}')">
                <div class="perm-section-icon" style="background:${catBg};color:${catColor}">
                    <i class="fas fa-${m.icon || 'circle'}"></i>
                </div>
                <span class="perm-section-name">${m.name} <small style="font-size:0.7rem; font-weight:normal; color:var(--gray-light)">(${m.category})</small></span>
                <span class="perm-section-count" id="count-${m.id}">${activeCount}/${menuPermissions.length}</span>
                <button class="perm-section-toggle-all" onclick="event.stopPropagation(); toggleSectionPerms('${m.id}', ${activeCount < menuPermissions.length})">
                    ${activeCount === menuPermissions.length ? '<i class="fas fa-minus-circle"></i> Matikan' : '<i class="fas fa-check-circle"></i> Aktifkan'} Semua
                </button>
                <i class="fas fa-chevron-down perm-chevron"></i>
            </div>
            <div class="perm-items">
                ${menuPermissions.map(p => renderPermItem(p)).join('')}
            </div>
        </div>`;
    }).join('');
}

function renderPermItem(permission) {
    const isOn = activeRolePermissions.has(permission.name);
    
    // Human friendly display name
    // e.g. "create orders" -> "Create (orders)" or "Buat Order"
    const nameParts = permission.name.split(' ');
    const action = nameParts[0] || '';
    const target = nameParts.slice(1).join(' ') || '';
    
    let friendlyAction = action.toUpperCase();
    if (action === 'menu') friendlyAction = 'Akses Menu';
    else if (action === 'create') friendlyAction = 'Tambah (Create)';
    else if (action === 'read') friendlyAction = 'Lihat (Read)';
    else if (action === 'show') friendlyAction = 'Detail (Show)';
    else if (action === 'update') friendlyAction = 'Ubah (Update)';
    else if (action === 'delete') friendlyAction = 'Hapus (Delete)';

    return `
    <div class="perm-item" id="pi-${permission.id}">
        <div class="perm-item-icon" style="background:${isOn ? 'rgba(16,185,129,.1)' : '#F3F4F6'};color:${isOn ? 'var(--secondary)' : 'var(--gray-light)'}">
            <i class="fas ${isOn ? 'fa-check-circle' : 'fa-circle'}" style="font-size:.7rem"></i>
        </div>
        <div class="perm-item-info">
            <div class="perm-item-name">${friendlyAction}</div>
            <div class="perm-item-desc">Izin melakukan operasi ${action} pada ${target}</div>
        </div>
        <div class="perm-toggles">
            <label class="perm-toggle">
                <input type="checkbox" ${isOn ? 'checked' : ''} onchange="toggleSinglePerm('${permission.name}', '${permission.id}', this.checked)">
                <span class="perm-slider"></span>
            </label>
        </div>
    </div>`;
}

window.toggleSection = function(id) {
    const sec = document.getElementById('ps-' + id);
    if (sec) sec.classList.toggle('open');
}

window.toggleSinglePerm = function(permName, permId, isChecked) {
    if (isChecked) {
        activeRolePermissions.add(permName);
    } else {
        activeRolePermissions.delete(permName);
    }

    // Update permission item style dynamically
    const pi = document.getElementById('pi-' + permId);
    if (pi) {
        const icon = pi.querySelector('.perm-item-icon');
        if (icon) {
            icon.style.background = isChecked ? 'rgba(16,185,129,.1)' : '#F3F4F6';
            icon.style.color = isChecked ? 'var(--secondary)' : 'var(--gray-light)';
            icon.innerHTML = `<i class="fas ${isChecked ? 'fa-check-circle' : 'fa-circle'}" style="font-size:.7rem"></i>`;
        }
    }

    // Recalculate section count
    menus.forEach(m => {
        const menuPermissions = m.permissions || [];
        if (menuPermissions.some(p => p.id === permId)) {
            const countEl = document.getElementById('count-' + m.id);
            const activeCount = menuPermissions.filter(p => activeRolePermissions.has(p.name)).length;
            if (countEl) countEl.textContent = `${activeCount}/${menuPermissions.length}`;

            const header = document.getElementById('ps-' + m.id);
            if (header) {
                const btn = header.querySelector('.perm-section-toggle-all');
                if (btn) {
                    btn.innerHTML = activeCount === menuPermissions.length 
                        ? '<i class="fas fa-minus-circle"></i> Matikan Semua' 
                        : '<i class="fas fa-check-circle"></i> Aktifkan Semua';
                }
            }
        }
    });

    renderRoles();
}

window.toggleSectionPerms = function(menuId, val) {
    const menu = menus.find(m => m.id === menuId);
    if (!menu) return;

    (menu.permissions || []).forEach(p => {
        if (val) {
            activeRolePermissions.add(p.name);
        } else {
            activeRolePermissions.delete(p.name);
        }
    });

    renderPermissions();
    renderRoles();
}

window.toggleAllPerms = function(val) {
    if (!activeRoleId) return;
    
    menus.forEach(m => {
        (m.permissions || []).forEach(p => {
            if (val) {
                activeRolePermissions.add(p.name);
            } else {
                activeRolePermissions.delete(p.name);
            }
        });
    });

    renderPermissions();
    renderRoles();
    showToast('info', 'Permission Matrix', val ? 'Semua permission diaktifkan' : 'Semua permission dimatikan');
}

window.setReadOnly = function() {
    if (!activeRoleId) return;

    menus.forEach(m => {
        (m.permissions || []).forEach(p => {
            if (p.name.startsWith('read ') || p.name.startsWith('show ') || p.name.startsWith('menu ')) {
                activeRolePermissions.add(p.name);
            } else {
                activeRolePermissions.delete(p.name);
            }
        });
    });

    renderPermissions();
    renderRoles();
    showToast('info', 'Permission Matrix', 'Hanya permission baca/tampil yang diaktifkan (View Only)');
}

window.resetPerms = function() {
    if (!activeRoleId) return;
    if (typeof window.showConfirm === 'function') {
        window.showConfirm(
            'Reset Perubahan',
            'Apakah Anda yakin ingin membatalkan perubahan yang belum disimpan?',
            () => {
                selectRole(activeRoleId);
                showToast('success', 'Reset', 'Perubahan permission berhasil dibatalkan');
                return Promise.resolve();
            },
            {
                confirmText: 'Ya, Reset',
                confirmIcon: 'fa-undo',
                confirmBg: 'linear-gradient(135deg, var(--warning), #D97706)',
                confirmShadow: '0 4px 15px rgba(245, 158, 11, 0.3)',
                icon: 'fa-history',
                pulseBg: 'linear-gradient(135deg, var(--warning), #F59E0B)',
                pulseShadow: '0 8px 20px rgba(245, 158, 11, 0.35)',
                ringColor: 'rgba(245, 158, 11, 0.12)'
            }
        );
    } else {
        if (confirm('Batalkan perubahan yang belum disimpan?')) {
            selectRole(activeRoleId);
            showToast('success', 'Reset', 'Perubahan permission berhasil dibatalkan');
        }
    }
}

window.savePerms = function() {
    if (!activeRoleId) {
        showToast('error', 'Error', 'Silakan pilih role terlebih dahulu');
        return;
    }

    const saveBtn = document.querySelector('.pab-save');
    const originalHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const payload = {
        permissions: Array.from(activeRolePermissions),
        _token: csrfToken
    };

    fetch(`/roles/${activeRoleId}/permissions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update in local roles array
            const roleIdx = roles.findIndex(r => r.id === activeRoleId);
            if (roleIdx !== -1) {
                roles[roleIdx].permissions = payload.permissions.map(pName => ({ name: pName }));
            }
            showToast('success', 'Disimpan', data.message);
            renderRoles();
        } else {
            showToast('error', 'Gagal', data.message);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('error', 'Error', 'Terjadi kesalahan sistem saat menyimpan permission.');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHtml;
    });
}

/* ─── RENDER USERS IN ROLE ─── */
function renderUsers() {
    const panel = document.getElementById('usersPanel');
    if (!panel) return;

    const role = roles.find(r => r.id === activeRoleId);
    if (!role) {
        panel.style.display = 'none';
        return;
    }

    panel.style.display = '';
    const rNameSpan = document.getElementById('ur-role-name');
    if (rNameSpan) rNameSpan.textContent = role.name;

    const assignedUids = roleUsers[activeRoleId] || [];
    const countSpan = document.getElementById('ur-count');
    if (countSpan) countSpan.textContent = assignedUids.length;

    const list = document.getElementById('usersList');
    if (!list) return;

    if (assignedUids.length === 0) {
        list.innerHTML = '<div style="padding:2rem;text-align:center;color:var(--gray-light);font-size:.875rem">Belum ada pengguna dengan role ini</div>';
        return;
    }

    list.innerHTML = assignedUids.map(uid => {
        const u = users.find(x => x.id === uid);
        if (!u) return '';

        return `
        <div class="user-item">
            <div class="user-avatar" style="background:${u.color || '#6366F1'}">${getInitials(u.name)}</div>
            <div class="user-info">
                <div class="user-name">${u.name}</div>
                <div class="user-meta">${u.email}</div>
            </div>
            <span class="user-outlet">${u.outlet ? u.outlet.name : 'Semua Outlet'}</span>
            <div class="user-status online"></div>
            <button class="btn-remove-user" title="Hapus dari role" onclick="removeUserFromRole('${uid}')">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    }).join('');
}

window.removeUserFromRole = function(userId) {
    if (!activeRoleId) return;
    const role = roles.find(r => r.id === activeRoleId);
    const user = users.find(u => u.id === userId);
    if (!role || !user) return;

    if (role.slug === 'dev' && roleUsers[activeRoleId].length <= 1) {
        showToast('error', 'Gagal', 'Role developer minimal harus memiliki satu pengguna.');
        return;
    }

    if (typeof window.showConfirm === 'function') {
        window.showConfirm(
            'Hapus Pengguna dari Role',
            `Apakah Anda yakin ingin menghapus pengguna "${user.name}" dari role "${role.name}"?`,
            () => {
                return fetch(`/roles/${activeRoleId}/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update local state
                        roleUsers[activeRoleId] = (roleUsers[activeRoleId] || []).filter(id => id !== userId);
                        
                        // Decrease user count in role list
                        const roleIdx = roles.findIndex(r => r.id === activeRoleId);
                        if (roleIdx !== -1) roles[roleIdx].userCount = roleUsers[activeRoleId].length;

                        renderUsers();
                        renderRoles();
                        showToast('success', 'Dihapus', data.message);
                    } else {
                        showToast('error', 'Gagal', data.message);
                        throw new Error(data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Error', 'Gagal menghapus pengguna.');
                    throw err;
                });
            }
        );
    } else {
        if (!confirm(`Hapus pengguna ${user.name} dari role ${role.name}?`)) return;

        fetch(`/roles/${activeRoleId}/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update local state
                roleUsers[activeRoleId] = (roleUsers[activeRoleId] || []).filter(id => id !== userId);
                
                // Decrease user count in role list
                const roleIdx = roles.findIndex(r => r.id === activeRoleId);
                if (roleIdx !== -1) roles[roleIdx].userCount = roleUsers[activeRoleId].length;

                renderUsers();
                renderRoles();
                showToast('success', 'Dihapus', data.message);
            } else {
                showToast('error', 'Gagal', data.message);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Error', 'Gagal menghapus pengguna.');
        });
    }
}

/* ─── ADD / EDIT ROLE MODAL ─── */
function buildColorOptions() {
    return ROLE_COLORS.map(c => `
        <div class="color-opt ${selectedColor === c ? 'selected' : ''}" 
             style="background:${c}" 
             onclick="selectColorOpt('${c}', this)" 
             title="${c}">
            ${selectedColor === c ? '<i class="fas fa-check"></i>' : ''}
        </div>`).join('');
}

function buildIconOptions() {
    return ROLE_ICONS.map(ic => `
        <div class="icon-opt ${selectedIcon === ic ? 'selected' : ''}" 
             onclick="selectIconOpt('${ic}', this)">
            <i class="fas ${ic}"></i>
        </div>`).join('');
}

window.selectColorOpt = function(c, el) {
    selectedColor = c;
    document.querySelectorAll('.color-opt').forEach(x => {
        x.classList.remove('selected');
        x.innerHTML = '';
    });
    el.classList.add('selected');
    el.innerHTML = '<i class="fas fa-check"></i>';
}

window.selectIconOpt = function(ic, el) {
    selectedIcon = ic;
    document.querySelectorAll('.icon-opt').forEach(x => x.classList.remove('selected'));
    el.classList.add('selected');
}

window.openAddRoleModal = function() {
    editRoleId = null;
    selectedColor = '#6366F1';
    selectedIcon = 'fa-user-shield';

    const rmIcon = document.getElementById('rm-icon');
    if (rmIcon) rmIcon.style.background = 'linear-gradient(135deg, var(--primary), var(--purple))';

    const rmTitle = document.getElementById('rm-title');
    if (rmTitle) rmTitle.textContent = 'Buat Role Baru';

    const rmSub = document.getElementById('rm-sub');
    if (rmSub) rmSub.textContent = 'Tentukan nama, ikon, dan warna role';

    ['rm-name', 'rm-desc', 'rm-slug'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    const colorOptsEl = document.getElementById('colorOptions');
    if (colorOptsEl) colorOptsEl.innerHTML = buildColorOptions();

    const iconOptsEl = document.getElementById('iconOptions');
    if (iconOptsEl) iconOptsEl.innerHTML = buildIconOptions();

    openModal('roleModal');
}

window.openEditRoleModal = function(id) {
    const role = roles.find(r => r.id === id);
    if (!role) return;

    editRoleId = id;
    selectedColor = role.color || '#6366F1';
    selectedIcon = role.icon || 'fa-user-shield';

    const rmIcon = document.getElementById('rm-icon');
    if (rmIcon) rmIcon.style.background = `linear-gradient(135deg, ${selectedColor}, ${selectedColor}99)`;

    const rmTitle = document.getElementById('rm-title');
    if (rmTitle) rmTitle.textContent = 'Edit Role';

    const rmSub = document.getElementById('rm-sub');
    if (rmSub) rmSub.textContent = 'Perbarui konfigurasi role ' + role.name;

    const nameInput = document.getElementById('rm-name');
    if (nameInput) nameInput.value = role.name;

    const slugInput = document.getElementById('rm-slug');
    if (slugInput) slugInput.value = role.slug;

    const descInput = document.getElementById('rm-desc');
    if (descInput) descInput.value = role.description || '';

    const priorityInput = document.getElementById('rm-level');
    if (priorityInput) priorityInput.value = role.priority || 2;

    const colorOptsEl = document.getElementById('colorOptions');
    if (colorOptsEl) colorOptsEl.innerHTML = buildColorOptions();

    const iconOptsEl = document.getElementById('iconOptions');
    if (iconOptsEl) iconOptsEl.innerHTML = buildIconOptions();

    openModal('roleModal');
}

window.saveRole = function() {
    const nameEl = document.getElementById('rm-name');
    const name = nameEl ? nameEl.value.trim() : '';
    if (!name) {
        showToast('error', 'Validasi', 'Nama role wajib diisi');
        return;
    }

    const slugEl = document.getElementById('rm-slug');
    const slug = slugEl ? slugEl.value.trim() : '';

    const descEl = document.getElementById('rm-desc');
    const desc = descEl ? descEl.value.trim() : '';

    const priorityEl = document.getElementById('rm-level');
    const priority = priorityEl ? parseInt(priorityEl.value) : 2;

    const saveBtn = document.querySelector('#roleModal .modal-btn-primary');
    const originalHtml = saveBtn ? saveBtn.innerHTML : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';
    }

    const payload = {
        name: name,
        slug: slug,
        description: desc,
        color: selectedColor,
        icon: selectedIcon,
        priority: priority,
        _token: csrfToken
    };

    const isEdit = editRoleId !== null;
    const url = isEdit ? `/roles/${editRoleId}` : '/roles';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (isEdit) {
                const roleIdx = roles.findIndex(r => r.id === editRoleId);
                if (roleIdx !== -1) {
                    roles[roleIdx] = {
                        ...roles[roleIdx],
                        ...data.data,
                        permissions: roles[roleIdx].permissions // Keep existing permissions locally
                    };
                }
            } else {
                const newRole = {
                    ...data.data,
                    permissions: []
                };
                roles.push(newRole);
                roleUsers[newRole.id] = [];
            }
            
            renderRoles();
            closeModal('roleModal');
            showToast('success', isEdit ? 'Diperbarui' : 'Ditambahkan', data.message);

            if (!isEdit) {
                // Auto-select newly created role
                selectRole(data.data.id);
            } else if (activeRoleId === editRoleId) {
                // Re-select active role to refresh headings/states
                selectRole(editRoleId);
            }
        } else {
            showToast('error', 'Gagal', data.message);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('error', 'Error', 'Terjadi kesalahan sistem saat menyimpan role.');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalHtml;
        }
    });
}

window.deleteRole = function(id) {
    const role = roles.find(r => r.id === id);
    if (!role) return;

    const assignedUsersCount = roleUsers[id] ? roleUsers[id].length : 0;
    if (assignedUsersCount > 0) {
        showToast('error', 'Gagal', `Role ${role.name} masih memiliki ${assignedUsersCount} pengguna. Kosongkan pengguna terlebih dahulu.`);
        return;
    }

    if (typeof window.showConfirm === 'function') {
        window.showConfirm(
            'Hapus Role',
            `Apakah Anda yakin ingin menghapus role "${role.name}" secara permanen? Tindakan ini tidak dapat dibatalkan.`,
            () => {
                return fetch(`/roles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        roles = roles.filter(r => r.id !== id);
                        delete roleUsers[id];

                        if (activeRoleId === id) {
                            activeRoleId = null;
                            const body = document.getElementById('permBody');
                            if (body) {
                                body.innerHTML = `
                                <div style="padding:3rem;text-align:center;color:var(--gray-light)">
                                    <div style="font-size:3rem;margin-bottom:1rem;opacity:.25"><i class="fas fa-user-shield"></i></div>
                                    <div style="font-size:1rem;font-weight:600;color:var(--gray);margin-bottom:.5rem">Pilih Role Terlebih Dahulu</div>
                                    <div style="font-size:.875rem">Klik salah satu role di panel kiri untuk mengatur permission</div>
                                </div>`;
                            }
                            const srIcon = document.getElementById('sr-icon');
                            if (srIcon) {
                                srIcon.style.background = '';
                                srIcon.style.color = '';
                                srIcon.innerHTML = `<i class="fas fa-user-shield"></i>`;
                            }
                            const srName = document.getElementById('sr-name');
                            if (srName) srName.textContent = 'Pilih Role';
                            const srDesc = document.getElementById('sr-desc');
                            if (srDesc) srDesc.textContent = 'Klik role di sebelah kiri untuk melihat permission';

                            const usersPanel = document.getElementById('usersPanel');
                            if (usersPanel) usersPanel.style.display = 'none';
                        }

                        renderRoles();
                        showToast('success', 'Dihapus', data.message);
                    } else {
                        showToast('error', 'Gagal', data.message);
                        throw new Error(data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Error', 'Gagal menghapus role.');
                    throw err;
                });
            }
        );
    } else {
        if (!confirm(`Apakah Anda yakin ingin menghapus role "${role.name}" secara permanen? Tindakan ini tidak dapat dibatalkan.`)) return;

        fetch(`/roles/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                roles = roles.filter(r => r.id !== id);
                delete roleUsers[id];

                if (activeRoleId === id) {
                    activeRoleId = null;
                    const body = document.getElementById('permBody');
                    if (body) {
                        body.innerHTML = `
                        <div style="padding:3rem;text-align:center;color:var(--gray-light)">
                            <div style="font-size:3rem;margin-bottom:1rem;opacity:.25"><i class="fas fa-user-shield"></i></div>
                            <div style="font-size:1rem;font-weight:600;color:var(--gray);margin-bottom:.5rem">Pilih Role Terlebih Dahulu</div>
                            <div style="font-size:.875rem">Klik salah satu role di panel kiri untuk mengatur permission</div>
                        </div>`;
                    }
                    const srIcon = document.getElementById('sr-icon');
                    if (srIcon) {
                        srIcon.style.background = '';
                        srIcon.style.color = '';
                        srIcon.innerHTML = `<i class="fas fa-user-shield"></i>`;
                    }
                    const srName = document.getElementById('sr-name');
                    if (srName) srName.textContent = 'Pilih Role';
                    const srDesc = document.getElementById('sr-desc');
                    if (srDesc) srDesc.textContent = 'Klik role di sebelah kiri untuk melihat permission';

                    const usersPanel = document.getElementById('usersPanel');
                    if (usersPanel) usersPanel.style.display = 'none';
                }

                renderRoles();
                showToast('success', 'Dihapus', data.message);
            } else {
                showToast('error', 'Gagal', data.message);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Error', 'Gagal menghapus role.');
        });
    }
}

/* ─── ASSIGN USER MODAL ─── */
window.openAssignModal = function() {
    if (!activeRoleId) {
        showToast('error', 'Error', 'Silakan pilih role terlebih dahulu');
        return;
    }
    const role = roles.find(r => r.id === activeRoleId);
    const amName = document.getElementById('am-role-name');
    if (amName) amName.textContent = 'Tambah pengguna ke role ' + role.name;

    selectedUsersToAssign.clear();
    const searchInput = document.getElementById('userSearchInput');
    if (searchInput) searchInput.value = '';

    renderAssignUsers(users);
    openModal('assignModal');
}

function renderAssignUsers(list) {
    const assigned = roleUsers[activeRoleId] || [];
    const container = document.getElementById('userSearchResults');
    if (!container) return;

    if (list.length === 0) {
        container.innerHTML = '<div style="padding:1.5rem;text-align:center;color:var(--gray-light);font-size:.875rem">Tidak ada pengguna ditemukan</div>';
        return;
    }

    container.innerHTML = list.map(u => {
        const isAssigned = assigned.includes(u.id);
        return `
        <div class="user-search-item ${isAssigned ? 'assigned' : ''}">
            ${!isAssigned ? `
            <input type="checkbox" class="user-search-cb" data-uid="${u.id}" ${selectedUsersToAssign.has(u.id) ? 'checked' : ''} onchange="toggleAssignUser('${u.id}', this.checked)">` : `
            <i class="fas fa-check" style="width:18px;color:var(--secondary)"></i>`}
            <div class="user-avatar" style="background:${u.color || '#6366F1'};width:32px;height:32px;border-radius:8px;flex-shrink:0;font-size:.7rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center">
                ${getInitials(u.name)}
            </div>
            <div style="flex:1">
                <div style="font-size:.875rem;font-weight:600;color:var(--dark)">${u.name}</div>
                <div style="font-size:.72rem;color:var(--gray-light)">${u.email}</div>
            </div>
            <span style="font-size:.7rem;padding:.2rem .5rem;border-radius:5px;background:rgba(99,102,241,.08);color:var(--primary)">
                ${u.outlet ? u.outlet.name : 'Semua Outlet'}
            </span>
        </div>`;
    }).join('');
}

window.filterAssignUsers = function(query) {
    const q = query.toLowerCase();
    const filtered = users.filter(u => 
        u.name.toLowerCase().includes(q) || 
        u.email.toLowerCase().includes(q)
    );
    renderAssignUsers(filtered);
}

window.toggleAssignUser = function(uid, checked) {
    if (checked) {
        selectedUsersToAssign.add(uid);
    } else {
        selectedUsersToAssign.delete(uid);
    }
}

window.confirmAssign = function() {
    if (selectedUsersToAssign.size === 0) {
        showToast('error', 'Pilih', 'Pilih minimal satu pengguna untuk ditambahkan');
        return;
    }

    const assignBtn = document.querySelector('#assignModal .modal-btn-primary');
    const originalHtml = assignBtn.innerHTML;
    assignBtn.disabled = true;
    assignBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambahkan...';

    const payload = {
        user_ids: Array.from(selectedUsersToAssign),
        _token: csrfToken
    };

    fetch(`/roles/${activeRoleId}/users`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update local state
            payload.user_ids.forEach(uid => {
                // Remove from all other roles (since users only have one role at a time)
                Object.keys(roleUsers).forEach(rId => {
                    if (rId !== activeRoleId) {
                        roleUsers[rId] = (roleUsers[rId] || []).filter(id => id !== uid);
                        
                        // Update counts for other roles
                        const otherIdx = roles.findIndex(r => r.id === rId);
                        if (otherIdx !== -1) {
                            roles[otherIdx].userCount = roleUsers[rId].length;
                        }
                    }
                });

                // Add to active role
                if (!roleUsers[activeRoleId]) roleUsers[activeRoleId] = [];
                if (!roleUsers[activeRoleId].includes(uid)) {
                    roleUsers[activeRoleId].push(uid);
                }
            });

            // Update userCount in local roles array for the active role
            const roleIdx = roles.findIndex(r => r.id === activeRoleId);
            if (roleIdx !== -1) roles[roleIdx].userCount = roleUsers[activeRoleId].length;

            renderUsers();
            renderRoles();
            closeModal('assignModal');
            showToast('success', 'Ditambahkan', data.message);
        } else {
            showToast('error', 'Gagal', data.message);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('error', 'Error', 'Terjadi kesalahan sistem saat menambahkan pengguna.');
    })
    .finally(() => {
        assignBtn.disabled = false;
        assignBtn.innerHTML = originalHtml;
    });
}

/* ─── COPY PERMISSION MODAL ─── */
window.openCopyModalForRole = function(id) {
    activeRoleId = id;
    copySourceId = null;

    const role = roles.find(r => r.id === id);
    
    const optionsContainer = document.getElementById('copyRoleOptions');
    if (!optionsContainer) return;

    optionsContainer.innerHTML = roles.filter(r => r.id !== id).map(r => {
        const totalPerms = menus.reduce((acc, m) => acc + (m.permissions ? m.permissions.length : 0), 0);
        const activeCount = Array.isArray(r.permissions) ? r.permissions.length : 0;

        return `
        <div class="user-search-item" style="border: 2px solid var(--border); border-radius: 14px; padding: .875rem 1rem; margin-bottom: .5rem; cursor: pointer;" 
             onclick="selectCopySource('${r.id}', this)" id="cr-${r.id}">
            <div style="width:40px;height:40px;border-radius:12px;background:${r.color}18;color:${r.color};display:flex;align-items:center;justify-content:center;font-size:.9rem">
                <i class="fas ${r.icon || 'fa-user-shield'}"></i>
            </div>
            <div style="flex:1; margin-left: 0.875rem;">
                <div style="font-weight:600;color:var(--dark)">${r.name}</div>
                <div style="font-size:.75rem;color:var(--gray-light)">${activeCount}/${totalPerms} permission aktif</div>
            </div>
        </div>`;
    }).join('');

    openModal('copyModal');
}

window.selectCopySource = function(id, el) {
    copySourceId = id;
    document.querySelectorAll('[id^="cr-"]').forEach(x => {
        x.style.borderColor = 'var(--border)';
        x.style.background = '';
    });
    el.style.borderColor = 'var(--primary)';
    el.style.background = 'rgba(99, 102, 241, 0.04)';
}

window.confirmCopy = function() {
    if (!copySourceId) {
        showToast('error', 'Pilih', 'Pilih role sumber terlebih dahulu');
        return;
    }

    const srcRole = roles.find(r => r.id === copySourceId);
    if (!srcRole) return;

    const copyBtn = document.querySelector('#copyModal .modal-btn-primary');
    const originalHtml = copyBtn ? copyBtn.innerHTML : '';
    if (copyBtn) {
        copyBtn.disabled = true;
        copyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang proses...';
    }

    const srcPermissions = Array.isArray(srcRole.permissions) ? srcRole.permissions.map(p => p.name) : [];

    const payload = {
        permissions: srcPermissions,
        _token: csrfToken
    };

    fetch(`/roles/${activeRoleId}/permissions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Map source permissions locally
            activeRolePermissions = new Set(srcPermissions);

            // Update in local roles array
            const roleIdx = roles.findIndex(r => r.id === activeRoleId);
            if (roleIdx !== -1) {
                roles[roleIdx].permissions = srcPermissions.map(pName => ({ name: pName }));
            }

            renderPermissions();
            renderRoles();
            closeModal('copyModal');
            showToast('success', 'Disalin', `Berhasil menyalin dan mensinkronkan permission dari ${srcRole.name}.`);
        } else {
            showToast('error', 'Gagal', data.message);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('error', 'Error', 'Gagal menyalin permission.');
    })
    .finally(() => {
        if (copyBtn) {
            copyBtn.disabled = false;
            copyBtn.innerHTML = originalHtml;
        }
    });
}

/* ─── MODAL UTILS ─── */
window.openModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('show');
}

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('show');
}

window.closeModalOut = function(e, id) {
    if (e.target === e.currentTarget) {
        closeModal(id);
    }
}

window.scrollToTop = function(event) {
    const btn = document.getElementById('scrollTopBtn');
    if (!btn) return;
    
    const r = document.createElement('span');
    r.style.cssText = 'position:absolute;border-radius:50%;background:rgba(255,255,255,.4);width:52px;height:52px;left:0;top:0;transform:scale(0);animation:pB .6s ease-out';
    btn.appendChild(r);
    setTimeout(() => r.remove(), 600);

    const start = window.scrollY;
    const t0 = performance.now();
    
    function bounce(t) {
        const p = Math.min((t - t0) / 800, 1);
        const n = 7.5625;
        const d = 2.75;
        let e;
        if (p < 1 / d) e = n * p * p;
        else if (p < 2 / d) e = n * (p - 1.5 / d) * (p - 1.5 / d) + .75;
        else if (p < 2.5 / d) e = n * (p - 2.25 / d) * (p - 2.25 / d) + .9375;
        else e = n * (p - 2.625 / d) * (p - 2.625 / d) + .984375;
        window.scrollTo(0, start * (1 - e));
        if (p < 1) requestAnimationFrame(bounce);
    }
    requestAnimationFrame(bounce);
}
