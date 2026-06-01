// Setup CSRF token for jQuery AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

const CATEGORIES = [
    {key:'all',   label:'Semua',        icon:'fas fa-layer-group'},
    {key:'kiloan',label:'Kiloan',       icon:'fas fa-weight-hanging'},
    {key:'satuan',label:'Satuan',       icon:'fas fa-tshirt'},
    {key:'paket', label:'Paket',        icon:'fas fa-box'},
    {key:'antar', label:'Antar Jemput', icon:'fas fa-motorcycle'},
];

let activeTab      = 'all';
let editMode       = false;
let activeId       = null;
let priceTiers     = [];
let featuresList   = [];
let searchQuery    = '';
let servicesData   = [];
let currentPage    = 1;
let perPage        = 6;
let totalItems     = 0;
let categoryCounts = { all: 0, kiloan: 0, satuan: 0, paket: 0, antar: 0 };

// Initialize Page
$(document).ready(function() {
    loadServices();

    // Hook search inputs
    const searchEl = document.getElementById('topbarSearch');
    if (searchEl) {
        searchEl.addEventListener('input', function(e) {
            handleSearch(e.target.value);
        });
    }

    // Scroll top button
    window.addEventListener('scroll', () => {
        const btn = document.getElementById('scrollTopBtn');
        if (btn) {
            btn.classList.toggle('visible', window.scrollY > 300);
        }
    });
});

/* ============================================================
   LOAD DATA
============================================================ */
function loadServices() {
    showSkeletonGrid();
    
    $.ajax({
        url: '/services',
        method: 'GET',
        data: {
            search: searchQuery,
            category: activeTab,
            sort: 'recent',
            per_page: perPage,
            page: currentPage
        },
        success: function(res) {
            if (res.success) {
                if (res.data && res.data.meta) {
                    servicesData = res.data.data;
                    totalItems = res.data.meta.total;
                    currentPage = res.data.meta.current_page;
                    
                    updateStats(res.data.stats);
                    renderTabs();
                    renderServices();
                    renderPagination(res.data.meta);
                } else {
                    // Fallback
                    servicesData = res.data.services || [];
                    updateStats(res.data.stats);
                    renderTabs();
                    renderServices();
                    
                    const pagBar = document.getElementById('paginationBar');
                    if (pagBar) pagBar.style.display = 'none';
                }
            }
        },
        error: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal memuat data layanan dari server', 'error', 'Error');
            }
        }
    });
}

function showSkeletonGrid() {
    const container = document.getElementById('servicesContainer');
    const emptyState = document.getElementById('emptyState');
    if (!container) return;

    if (emptyState) emptyState.style.display = 'none';
    container.style.display = '';

    let html = '';
    for (let i = 0; i < perPage; i++) {
        html += `
        <div class="service-card" style="pointer-events:none;opacity:0.7">
            <div class="sc-body">
                <div class="sc-top" style="margin-bottom:1.5rem">
                    <div class="skeleton skeleton-circle" style="width:52px;height:52px;border-radius:14px;"></div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.35rem">
                        <div class="skeleton" style="width:60px;height:22px;border-radius:6px"></div>
                        <div class="skeleton" style="width:80px;height:18px;border-radius:6px"></div>
                    </div>
                </div>
                <div class="skeleton skeleton-text medium" style="height:16px;width:150px;margin-bottom:0.5rem"></div>
                <div class="skeleton skeleton-text long" style="height:12px;width:100%;margin-bottom:1.5rem"></div>
                <div class="skeleton" style="height:80px;border-radius:12px;width:100%;margin-bottom:1.5rem"></div>
            </div>
            <div class="sc-footer" style="gap:0.5rem">
                <div class="skeleton" style="flex:1;height:35px;border-radius:10px"></div>
                <div class="skeleton" style="flex:1;height:35px;border-radius:10px"></div>
            </div>
        </div>`;
    }
    container.innerHTML = html;
}

/* ============================================================
   RENDER TABS
============================================================ */
function renderTabs() {
    const bar = document.getElementById('tabsBar');
    if (!bar) return;

    bar.innerHTML = CATEGORIES.map(cat => {
        let count = categoryCounts[cat.key] || 0;
        return `
            <button class="tab-btn ${activeTab === cat.key ? 'active' : ''}" onclick="switchTab('${cat.key}')">
                <i class="${cat.icon}"></i>
                <span>${cat.label}</span>
                <span class="tab-count">${count}</span>
            </button>
        `;
    }).join('');
}

function switchTab(key) {
    activeTab = key;
    currentPage = 1;
    renderTabs();
    loadServices();
}

function handleSearch(v) {
    searchQuery = v.trim();
    currentPage = 1;
    loadServices();
}

/* ============================================================
   RENDER SERVICES
============================================================ */
function renderServices() {
    const container = document.getElementById('servicesContainer');
    const emptyState = document.getElementById('emptyState');
    if (!container) return;

    if (!servicesData.length) {
        container.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    container.style.display = '';
    if (emptyState) emptyState.style.display = 'none';

    container.innerHTML = servicesData.map(s => renderCard(s)).join('');
}

function renderCard(s) {
    const iconColor = s.color ? s.color.replace('sc-', '') : 'purple';
    const formatPrice = formatRp(s.price);

    const tiersHtml = s.tiers && s.tiers.length ? `
        <div class="sc-price-tiers">
            ${s.tiers.map(t => `
                <div class="sc-price-tier">
                    <span class="sc-tier-label">${t.label}</span>
                    <span class="sc-tier-price">${formatRp(t.price)}${s.unit}</span>
                    <button type="button" class="sc-tier-edit" onclick="openEditModal('${s.id}')" title="Edit harga"><i class="fas fa-pen"></i></button>
                </div>
            `).join('')}
        </div>` : '';

    const featuresHtml = s.features && s.features.length ? `
        <div class="sc-features">
            ${s.features.slice(0, 3).map(f => `<div class="sc-feature"><i class="fas fa-check-circle"></i>${f}</div>`).join('')}
            ${s.features.length > 3 ? `<div class="sc-feature" style="color:var(--primary)"><i class="fas fa-plus-circle"></i>+${s.features.length - 3} fitur lainnya</div>` : ''}
        </div>` : '';

    return `
    <div class="service-card ${s.color || 'sc-purple'} ${s.status ? '' : 'inactive'}">
        <div class="sc-body">
            <div class="sc-top">
                <div class="sc-icon ${iconColor}">${s.emoji || '🧺'}</div>
                <div class="sc-badges">
                    <span class="sc-status ${s.status ? 'aktif' : 'nonaktif'}">${s.status ? 'Aktif' : 'Nonaktif'}</span>
                    <span class="sc-type-pill ${s.category}">${s.category.charAt(0).toUpperCase() + s.category.slice(1)}</span>
                </div>
            </div>
            <div class="sc-name">${s.name}</div>
            <div class="sc-desc">${s.description || 'Tidak ada deskripsi'}</div>

            <div class="sc-price-section">
                <div class="sc-price-label">Harga Mulai Dari</div>
                <div class="sc-price-main">${formatPrice}</div>
                <div class="sc-price-unit">${s.unit}</div>
                <div class="sc-price-est">
                    <i class="fas fa-clock"></i> Estimasi: ${s.eta || '1-2 hari'}
                    ${s.express ? '<span style="margin-left:auto;background:rgba(245,158,11,.1);color:#D97706;padding:.15rem .5rem;border-radius:5px;font-size:.68rem;font-weight:700">⚡ Express</span>' : ''}
                </div>
            </div>

            ${tiersHtml}

            <div class="sc-stats">
                <div class="sc-stat">
                    <div class="sc-stat-val">${s.orders || 0}</div>
                    <div class="sc-stat-lbl">Order</div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-val">${s.revenue >= 1000000 ? (s.revenue / 1000000).toFixed(1) + 'jt' : formatRpCompact(s.revenue)}</div>
                    <div class="sc-stat-lbl">Revenue</div>
                </div>
                <div class="sc-stat">
                    <div class="sc-stat-val">${s.target > 0 ? Math.round((s.orders || 0) / s.target * 100) : 0}%</div>
                    <div class="sc-stat-lbl">Target</div>
                </div>
            </div>

            ${featuresHtml}
        </div>
        <div class="sc-footer">
            <button type="button" class="sc-btn sc-btn-outline" onclick="toggleStatus('${s.id}')" title="${s.status ? 'Nonaktifkan' : 'Aktifkan'}">
                <i class="fas fa-${s.status ? 'eye-slash' : 'eye'}"></i> ${s.status ? 'Nonaktifkan' : 'Aktifkan'}
            </button>
            <button type="button" class="sc-btn sc-btn-primary" onclick="openEditModal('${s.id}')"><i class="fas fa-pen"></i> Edit</button>
            <button type="button" class="sc-btn sc-btn-danger" onclick="deleteService('${s.id}')" title="Hapus"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>`;
}

function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function formatRpCompact(n) {
    if (n >= 1000) {
        return 'Rp ' + Math.round(n / 1000) + 'rb';
    }
    return 'Rp ' + n;
}

function updateStats(stats) {
    if (!stats) return;
    if (stats.counts) {
        categoryCounts = stats.counts;
    }
    if (document.getElementById('statTotal')) document.getElementById('statTotal').textContent = stats.total ?? 0;
    if (document.getElementById('statAktif')) document.getElementById('statAktif').textContent = stats.active ?? 0;
    if (document.getElementById('statTerlaris')) document.getElementById('statTerlaris').textContent = stats.terlaris ?? '—';
    if (document.getElementById('statRevenue')) document.getElementById('statRevenue').textContent = stats.revenue_max ?? '—';
}

/* ============================================================
   PAGINATION
============================================================ */
function renderPagination(meta) {
    const pagBar = document.getElementById('paginationBar');
    if (!pagBar) return;

    if (!servicesData.length || meta.last_page <= 1) {
        pagBar.style.display = 'none';
        return;
    }

    pagBar.style.display = 'flex';
    document.getElementById('currentPage').textContent = meta.current_page;
    document.getElementById('totalPages').textContent  = meta.last_page;
    document.getElementById('showCount').textContent   = servicesData.length;
    document.getElementById('totalCount').textContent  = meta.total;

    buildPageControls('paginationControls', meta.last_page, 'goPage');
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
    const total = Math.ceil(totalItems / perPage) || 1;
    if (p < 1 || p > total) return;
    currentPage = p;
    loadServices();
    
    // Scroll smoothly to top of cards grid
    const target = document.getElementById('servicesContainer');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/* ============================================================
   TOGGLE STATUS
============================================================ */
function toggleStatus(id) {
    $.ajax({
        url: `/services/${id}/toggle-status`,
        method: 'PATCH',
        success: function(res) {
            if (res.success) {
                const s = res.data;
                if (typeof window.showToast === 'function') {
                    window.showToast(`${s.name} berhasil ${s.status ? 'diaktifkan' : 'dinonaktifkan'}`, 'success', s.status ? 'Aktif' : 'Nonaktif');
                }
                loadServices();
            }
        },
        error: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal mengubah status layanan', 'error', 'Error');
            }
        }
    });
}

/* ============================================================
   ADD / EDIT MODAL
============================================================ */
function openAddModal() {
    editMode = false;
    activeId = null;
    priceTiers = [];
    featuresList = [''];

    document.getElementById('modalIconEl').innerHTML = '<i class="fas fa-concierge-bell"></i>';
    document.getElementById('modalTitleEl').textContent = 'Tambah Layanan';
    document.getElementById('modalSubEl').textContent = 'Isi detail layanan baru';

    // Reset fields
    document.getElementById('f-name').value = '';
    selectEmoji('🧺');
    document.getElementById('f-category').value = 'kiloan';
    document.getElementById('f-desc').value = '';
    document.getElementById('f-price').value = '';
    document.getElementById('f-unit').value = '/kg';
    document.getElementById('f-eta').value = '';
    document.getElementById('f-target').value = '';
    document.getElementById('f-min').value = '';
    document.getElementById('f-color').value = 'sc-purple';
    document.getElementById('f-aktif').checked = true;
    document.getElementById('f-express').checked = false;
    document.getElementById('f-pickup').checked = false;

    renderTiersEditor();
    renderFeaturesEditor();
    
    document.getElementById('serviceModal').classList.add('show');
}

function openEditModal(id) {
    $.ajax({
        url: `/services/${id}`,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                const s = res.data;
                editMode = true;
                activeId = id;
                priceTiers = s.tiers ? [...s.tiers] : [];
                featuresList = s.features ? [...s.features] : [];

                document.getElementById('modalIconEl').innerHTML = '<i class="fas fa-pen"></i>';
                document.getElementById('modalTitleEl').textContent = 'Edit Layanan';
                document.getElementById('modalSubEl').textContent = 'Perbarui detail layanan';

                document.getElementById('f-name').value = s.name;
                selectEmoji(s.emoji || '🧺');
                document.getElementById('f-category').value = s.category;
                document.getElementById('f-desc').value = s.description || '';
                document.getElementById('f-price').value = s.price;
                document.getElementById('f-unit').value = s.unit;
                document.getElementById('f-eta').value = s.eta || '';
                document.getElementById('f-target').value = s.target || '';
                document.getElementById('f-min').value = s.min_qty || '';
                document.getElementById('f-color').value = s.color || 'sc-purple';
                document.getElementById('f-aktif').checked = s.status;
                document.getElementById('f-express').checked = s.express;
                document.getElementById('f-pickup').checked = s.pickup;

                renderTiersEditor();
                renderFeaturesEditor();

                document.getElementById('serviceModal').classList.add('show');
            }
        },
        error: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal memuat detail layanan', 'error', 'Error');
            }
        }
    });
}

/* Price Tiers Editor */
function addPriceTier() {
    priceTiers.push({ label: '', price: 0 });
    renderTiersEditor();
}

function removeTier(i) {
    priceTiers.splice(i, 1);
    renderTiersEditor();
}

function renderTiersEditor() {
    const el = document.getElementById('priceTiersEditor');
    if (!el) return;

    el.innerHTML = priceTiers.map((t, i) => `
        <div class="price-tier-row">
            <input class="form-control" type="text" placeholder="cth. 1-5 kg" value="${t.label}" oninput="updatePriceTierLabel(${i}, this.value)" style="font-size:.875rem;padding:.6rem .875rem" required>
            <input class="form-control" type="number" placeholder="Harga" value="${t.price || ''}" oninput="updatePriceTierPrice(${i}, this.value)" style="font-size:.875rem;padding:.6rem .875rem" required>
            <button type="button" class="btn-rm-tier" onclick="removeTier(${i})"><i class="fas fa-times"></i></button>
        </div>
    `).join('');
}

/* Features Editor */
function addFeatureRow() {
    featuresList.push('');
    renderFeaturesEditor();
}

function removeFeature(i) {
    featuresList.splice(i, 1);
    renderFeaturesEditor();
}

function renderFeaturesEditor() {
    const el = document.getElementById('featuresEditor');
    if (!el) return;

    el.innerHTML = featuresList.map((f, i) => `
        <div style="display:flex;gap:.5rem;align-items:center;margin-bottom:0.5rem">
            <input class="form-control" type="text" placeholder="cth. Deterjen premium" value="${f}" oninput="updateFeature(${i}, this.value)" style="font-size:.875rem;padding:.6rem .875rem" required>
            <button type="button" class="btn-rm-tier" onclick="removeFeature(${i})"><i class="fas fa-times"></i></button>
        </div>
    `).join('');
}

/* ============================================================
   SAVE / STORE / UPDATE
============================================================ */
function saveService() {
    const name = document.getElementById('f-name').value.trim();
    const price = parseInt(document.getElementById('f-price').value) || 0;

    if (!name) {
        if (typeof window.showToast === 'function') window.showToast('Nama layanan wajib diisi', 'error', 'Validasi');
        return;
    }
    if (!price) {
        if (typeof window.showToast === 'function') window.showToast('Harga wajib diisi', 'error', 'Validasi');
        return;
    }

    const saveBtn = document.getElementById('btnSaveService');
    const originalHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.classList.add('loading');
    saveBtn.querySelector('.btn-text').textContent = 'Sedang proses...';

    const url = editMode ? `/services/${activeId}` : '/services';
    const method = editMode ? 'PUT' : 'POST';

    const data = {
        name: name,
        emoji: document.getElementById('f-emoji').value || '🧺',
        category: document.getElementById('f-category').value,
        description: document.getElementById('f-desc').value,
        price: price,
        unit: document.getElementById('f-unit').value,
        eta: document.getElementById('f-eta').value || '1-2 hari',
        color: document.getElementById('f-color').value,
        target: parseInt(document.getElementById('f-target').value) || 100,
        min_qty: document.getElementById('f-min').value || '1',
        status: document.getElementById('f-aktif').checked ? 1 : 0,
        express: document.getElementById('f-express').checked ? 1 : 0,
        pickup: document.getElementById('f-pickup').checked ? 1 : 0,
        tiers: priceTiers.filter(t => t.label.trim() !== ''),
        features: featuresList.filter(f => f.trim() !== '')
    };

    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function(res) {
            if (res.success) {
                if (typeof window.showToast === 'function') {
                    window.showToast(
                        editMode ? 'Layanan berhasil diperbarui' : 'Layanan baru berhasil ditambahkan',
                        'success',
                        editMode ? 'Diperbarui' : 'Ditambahkan'
                    );
                }
                closeModal('serviceModal');
                loadServices();
            }
        },
        error: function(err) {
            let msg = 'Terjadi kesalahan sistem';
            if (err.responseJSON && err.responseJSON.message) {
                msg = err.responseJSON.message;
            }
            if (typeof window.showToast === 'function') {
                window.showToast(msg, 'error', 'Error');
            }
        },
        complete: function() {
            saveBtn.disabled = false;
            saveBtn.classList.remove('loading');
            saveBtn.innerHTML = originalHtml;
        }
    });
}

/* ============================================================
   DELETE SERVICE
============================================================ */
function deleteService(id) {
    const service = servicesData.find(s => s.id === id);
    if (!service) return;

    if (typeof window.showConfirm === 'function') {
        window.showConfirm(
            'Hapus Layanan?',
            `Apakah Anda yakin ingin menghapus layanan "${service.name}" secara permanen?`,
            function() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `/services/${id}`,
                        method: 'DELETE',
                        success: function(res) {
                            if (res.success) {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Layanan berhasil dihapus', 'success', 'Dihapus');
                                }
                                loadServices();
                                resolve();
                            } else {
                                reject('Gagal menghapus');
                            }
                        },
                        error: function() {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal menghapus layanan', 'error', 'Error');
                            }
                            reject('Error');
                        }
                    });
                });
            }
        );
    } else {
        if (confirm(`Apakah Anda yakin ingin menghapus layanan "${service.name}"?`)) {
            $.ajax({
                url: `/services/${id}`,
                method: 'DELETE',
                success: function(res) {
                    if (res.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Layanan berhasil dihapus', 'success', 'Dihapus');
                        }
                        loadServices();
                    }
                }
            });
        }
    }
}

/* Modal Helpers */
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('show');
}

function closeModalOutside(e, id) {
    if (e.target === e.currentTarget) closeModal(id);
}

// Scroll back to top
function scrollToTop(event) {
    const btn = document.getElementById('scrollTopBtn');
    if (!btn) return;
    
    const r = document.createElement('span');
    r.style.cssText = 'position:absolute;border-radius:50%;background:rgba(255,255,255,.4);width:52px;height:52px;left:0;top:0;transform:scale(0);animation:rippleA .6s ease-out';
    btn.appendChild(r); 
    setTimeout(() => r.remove(), 600);
    
    const start = window.scrollY, t0 = performance.now();
    function bounce(t) {
        let p = Math.min((t - t0) / 800, 1);
        const n = 7.5625, d = 2.75;
        let e;
        if (p < 1 / d) e = n * p * p;
        else if (p < 2 / d) e = n * (p -= 1.5 / d) * p + 0.75;
        else if (p < 2.5 / d) e = n * (p -= 2.25 / d) * p + 0.9375;
        else e = n * (p -= 2.625 / d) * p + 0.984375;
        window.scrollTo(0, start * (1 - e));
        if (p < 1) requestAnimationFrame(bounce);
    }
    requestAnimationFrame(bounce);
}

function selectEmoji(emoji, btnEl) {
    const hiddenEl = document.getElementById('f-emoji');
    if (hiddenEl) hiddenEl.value = emoji;
    
    $('.emoji-btn-item').removeClass('active');
    
    if (btnEl) {
        $(btnEl).addClass('active');
    } else {
        $('.emoji-btn-item').each(function() {
            if ($(this).text().trim() === emoji) {
                $(this).addClass('active');
            }
        });
    }
}

function updatePriceTierLabel(i, val) {
    if (priceTiers[i]) priceTiers[i].label = val;
}
function updatePriceTierPrice(i, val) {
    if (priceTiers[i]) priceTiers[i].price = parseInt(val) || 0;
}
function updateFeature(i, val) {
    featuresList[i] = val;
}

// Global functions exports
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.addPriceTier = addPriceTier;
window.removeTier = removeTier;
window.addFeatureRow = addFeatureRow;
window.removeFeature = removeFeature;
window.saveService = saveService;
window.deleteService = deleteService;
window.toggleStatus = toggleStatus;
window.updatePriceTierLabel = updatePriceTierLabel;
window.updatePriceTierPrice = updatePriceTierPrice;
window.updateFeature = updateFeature;
window.switchTab = switchTab;
window.scrollToTop = scrollToTop;
window.closeModal = closeModal;
window.closeModalOutside = closeModalOutside;
window.goPage = goPage;
window.selectEmoji = selectEmoji;
window.openBulkPriceModal = openBulkPriceModal;
window.saveBulkPrice = saveBulkPrice;

function openBulkPriceModal() {
    document.getElementById('bp-category').value = 'all';
    document.getElementById('bp-type').value = 'up';
    document.getElementById('bp-adjustment-type').value = 'percentage';
    document.getElementById('bp-value').value = '';
    
    document.getElementById('bulkPriceModal').classList.add('show');
}

function saveBulkPrice() {
    const category = document.getElementById('bp-category').value;
    const type = document.getElementById('bp-type').value;
    const adjustment_type = document.getElementById('bp-adjustment-type').value;
    const value = document.getElementById('bp-value').value;

    const btn = document.getElementById('btnSaveBulkPrice');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('loading');
    btn.querySelector('.btn-text').textContent = 'Sedang proses...';

    $.ajax({
        url: '/services/bulk-price',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            category: category,
            type: type,
            adjustment_type: adjustment_type,
            value: value
        },
        success: function(res) {
            if (res.success) {
                closeModal('bulkPriceModal');
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message, 'success', 'Berhasil');
                }
                loadServices();
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message || 'Gagal mengubah harga', 'error', 'Error');
                }
            }
        },
        error: function(xhr) {
            let errMsg = 'Terjadi kesalahan sistem';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            if (typeof window.showToast === 'function') {
                window.showToast(errMsg, 'error', 'Error');
            }
        },
        complete: function() {
            btn.disabled = false;
            btn.classList.remove('loading');
            btn.innerHTML = originalHtml;
        }
    });
}
