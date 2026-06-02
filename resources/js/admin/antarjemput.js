// Setup CSRF token for jQuery AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let tripsData = [];
let currentPage = 1;
let perPage = 6;
let totalItems = 0;
let totalPages = 1;
let activeStatus = 'all';
let searchQuery = '';
let activeTrip = null;
let selectedStatus = null;
let selectedDriver = null;

// Initialize
$(document).ready(function() {
    // Initialize Select2 dropdowns
    if ($.fn.select2) {
        $('#filterOutlet').select2({
            placeholder: 'Semua Outlet',
            allowClear: true
        }).on('change', function() {
            updateDriverFilterOptions();
            applyFilters();
        });

        $('#filterDriver').select2({
            placeholder: 'Semua Kurir',
            allowClear: true
        }).on('change', function() {
            applyFilters();
        });

        $('#a-outlet').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Outlet',
            allowClear: true
        }).on('change', function() {
            updateAddModalDriverOptions();
        });

        $('#a-driver').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Kurir',
            allowClear: true
        });
    }

    updateDriverFilterOptions();
    loadPickups();

    // Hook search inputs
    const searchEl = document.getElementById('searchInput');
    if (searchEl) {
        searchEl.addEventListener('input', function(e) {
            searchQuery = e.target.value;
            currentPage = 1;
            loadPickups();
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
function loadPickups() {
    showSkeletonGrid();

    const outletId = document.getElementById('filterOutlet') ? document.getElementById('filterOutlet').value : '';
    const driverId = document.getElementById('filterDriver') ? document.getElementById('filterDriver').value : '';
    const dateVal = document.getElementById('filterDate') ? document.getElementById('filterDate').value : '';

    $.ajax({
        url: '/shuttles',
        method: 'GET',
        data: {
            search: searchQuery,
            status: activeStatus,
            outlet_id: outletId,
            employee_id: driverId,
            date: dateVal,
            per_page: perPage,
            page: currentPage
        },
        success: function(res) {
            if (res.success) {
                tripsData = res.data.data || [];
                totalItems = res.data.meta.total;
                currentPage = res.data.meta.current_page;
                totalPages = res.data.meta.last_page;

                updateCounters(res.data.stats);
                renderTrips();
                renderDrivers();
                renderSchedule();
                renderPagination(res.data.meta);
            }
        },
        error: function() {
            showToast('error', 'Gagal', 'Gagal memuat data antar jemput dari server');
        }
    });
}

function showSkeletonGrid() {
    const container = document.getElementById('tripCardsGrid');
    if (!container) return;

    let html = '';
    for (let i = 0; i < perPage; i++) {
        html += `
        <div class="trip-card" style="pointer-events:none;opacity:0.7">
            <div class="trip-top">
                <div class="skeleton" style="width:70px;height:16px;border-radius:4px"></div>
                <div class="skeleton" style="width:80px;height:16px;border-radius:4px"></div>
            </div>
            <div class="trip-customer" style="margin-top:0.5rem">
                <div class="skeleton skeleton-circle" style="width:36px;height:36px;border-radius:10px"></div>
                <div style="flex:1">
                    <div class="skeleton skeleton-text medium" style="height:14px;width:120px"></div>
                    <div class="skeleton skeleton-text short" style="height:8px;width:60px;margin-top:4px"></div>
                </div>
            </div>
            <div class="skeleton" style="height:70px;border-radius:12px;width:100%;margin-top:0.75rem;margin-bottom:0.75rem"></div>
            <div class="skeleton" style="height:30px;border-radius:8px;width:100%;margin-bottom:0.75rem"></div>
            <div class="sc-footer" style="display:flex;gap:0.5rem;margin-top:auto">
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
                <div class="skeleton" style="flex:1;height:32px;border-radius:10px"></div>
            </div>
        </div>`;
    }
    container.innerHTML = html;
}

/* ============================================================
   RENDER METHODS
============================================================ */
function renderTrips() {
    const container = document.getElementById('tripCardsGrid');
    if (!container) return;

    if (tripsData.length === 0) {
        container.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-route"></i></div>
            <div class="empty-title">Tidak ada trip ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci atau filter pencarian Anda</div>
        </div>`;
        return;
    }

    container.innerHTML = tripsData.map(t => renderTripCard(t)).join('');
}

function renderTripCard(t) {
    let driverSection = '';
    if (t.employee_id) {
        driverSection = `
            <div class="trip-driver-row">
                <div class="trip-driver-avatar">${getInitials(t.driver_name)}</div>
                <div style="flex:1">
                    <div class="trip-driver-name">${t.driver_name}</div>
                    <div class="trip-driver-vehicle">${t.driver_vehicle || 'Motor Kurir'}</div>
                </div>
            </div>`;
    } else {
        driverSection = `
            <div class="trip-no-driver">
                <i class="fas fa-exclamation-circle" style="color:var(--danger);font-size:.875rem"></i>
                <span class="trip-no-driver-text">Belum ada kurir</span>
                <button class="trip-assign-btn" onclick="event.stopPropagation(); window.openAssignForTrip('${t.id}')">Tugaskan</button>
            </div>`;
    }

    return `
    <div class="trip-card tc-${t.status}" onclick="window.openDetail('${t.id}')">
        <div class="trip-top">
            <div class="trip-id">#${t.trip_code}</div>
            <div class="trip-time"><i class="fas fa-clock"></i>${t.scheduled_time}</div>
        </div>
        <div class="trip-customer">
            <div class="trip-avatar" style="background:${t.avatar_color || '#6366F1'}">${getInitials(t.customer_name)}</div>
            <div>
                <div class="trip-cust-name">${t.customer_name}</div>
                <div class="trip-cust-phone">${t.customer_phone}</div>
            </div>
        </div>
        <div class="trip-route">
            <div class="trip-route-line"></div>
            <div class="trip-route-point">
                <div class="trip-dot trip-dot-from"></div>
                <div class="trip-addr"><strong>Jemput:</strong> ${t.address_from.length > 45 ? t.address_from.slice(0,45)+'…' : t.address_from}</div>
            </div>
            <div class="trip-route-point">
                <div class="trip-dot trip-dot-to"></div>
                <div class="trip-addr"><strong>Antar:</strong> ${t.address_to.length > 45 ? t.address_to.slice(0,45)+'…' : t.address_to}</div>
            </div>
        </div>
        <div class="trip-meta">
            <span class="trip-meta-item"><i class="fas fa-map-marker-alt"></i>${t.distance} km</span>
            <span class="trip-meta-item"><i class="fas fa-clock"></i>${t.eta}</span>
            <span class="trip-meta-item"><i class="fas fa-weight-hanging"></i>${t.weight || '—'}</span>
            <span class="trip-meta-item" style="color:var(--primary);background:rgba(99,102,241,.08)"><i class="fas fa-money-bill-wave"></i>${formatRp(t.fee)}</span>
        </div>
        ${driverSection}
        <div class="trip-card-actions">
            <button class="trip-act-btn trip-act-btn-outline" onclick="event.stopPropagation(); window.openStatusForTrip('${t.id}')"><i class="fas fa-exchange-alt"></i> Status</button>
            <button class="trip-act-btn trip-act-btn-primary" onclick="event.stopPropagation(); window.openDetail('${t.id}')"><i class="fas fa-eye"></i> Detail</button>
        </div>
    </div>`;
}

function renderDrivers() {
    const list = document.getElementById('driverList');
    if (!list) return;
    
    const tripCounts = {};
    tripsData.forEach(t => { if (t.employee_id) { tripCounts[t.employee_id] = (tripCounts[t.employee_id] || 0) + 1; } });

    if (!window.driversData || window.driversData.length === 0) {
        list.innerHTML = `<div style="text-align:center;padding:1rem;color:var(--gray-light);font-size:.8rem">Tidak ada kurir aktif</div>`;
        return;
    }

    list.innerHTML = window.driversData.map(d => {
        const count = tripCounts[d.id] || 0;
        const statusClass = count > 0 ? 'busy' : 'online';
        const badgeClass = count > 0 ? 'busy' : 'free';
        const badgeText = count > 0 ? `${count} trip` : 'Tersedia';
        const initials = getInitials(d.name);
        const color = '#6366F1';
        return `
        <div class="driver-item" onclick="window.showToast('info','Kurir','Detail kurir ${d.name}')">
            <div class="driver-avatar" style="background:${color}">
                ${initials}
                <span class="driver-status-dot ${statusClass}"></span>
            </div>
            <div class="driver-info">
                <div class="driver-name">${d.name}</div>
                <div class="driver-vehicle">${d.code ? 'Motor - ' + d.code : 'Motor Kurir'}</div>
            </div>
            <span class="driver-trips ${badgeClass}">${badgeText}</span>
        </div>`;
    }).join('');
}

function renderSchedule() {
    const el = document.getElementById('scheduleList');
    if (!el) return;
    const today = new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'});
    const dateEl = document.getElementById('scheduleDate');
    if (dateEl) dateEl.textContent = today;

    const items = tripsData.slice(0, 6);
    if (items.length === 0) {
        el.innerHTML = `<div style="text-align:center;padding:1.5rem;color:var(--gray-light);font-size:.8rem">Tidak ada jadwal hari ini</div>`;
        return;
    }
    el.innerHTML = items.map(t => `
        <div class="schedule-item">
            <span class="schedule-time">${t.scheduled_time}</span>
            <div class="schedule-info">
                <div class="schedule-cust">${t.customer_name}</div>
                <div class="schedule-addr">${t.address_from.slice(0, 35)}…</div>
            </div>
            <span class="schedule-type ${t.status === 'antar' ? 'antar' : 'jemput'}">${t.status === 'antar' ? 'Antar' : 'Jemput'}</span>
        </div>`).join('');
}

function updateCounters(stats) {
    if (!stats) return;
    if (document.getElementById('sc-all')) document.getElementById('sc-all').textContent = stats.all;
    if (document.getElementById('sc-menunggu')) document.getElementById('sc-menunggu').textContent = stats.menunggu;
    if (document.getElementById('sc-jemput')) document.getElementById('sc-jemput').textContent = stats.jemput;
    if (document.getElementById('sc-proses')) document.getElementById('sc-proses').textContent = stats.proses;
    if (document.getElementById('sc-antar')) document.getElementById('sc-antar').textContent = stats.antar;
    if (document.getElementById('ls-trip-hari')) document.getElementById('ls-trip-hari').textContent = stats.all;
}

/* ============================================================
   PAGINATION
============================================================ */
function renderPagination(meta) {
    const pagBar = document.getElementById('paginationBar');
    if (!pagBar) return;

    if (!tripsData.length || meta.last_page <= 1) {
        pagBar.style.display = 'none';
        return;
    }

    pagBar.style.display = 'flex';
    document.getElementById('currentPage').textContent = meta.current_page;
    document.getElementById('totalPages').textContent  = meta.last_page;

    buildPageControls('paginationControls', meta.last_page, 'goPage');
}

function buildPageControls(containerId, total, fnName) {
    const el = document.getElementById(containerId);
    if (!el) return;
    let html = `<button class="page-link" onclick="window.${fnName}(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    let s = Math.max(1, currentPage-2), e = Math.min(total, s+4);
    if (e-s<4) s = Math.max(1, e-4);
    if (s>1) { html += `<button class="page-link" onclick="window.${fnName}(1)">1</button>`; if(s>2) html += `<span style="padding:0 .5rem;color:var(--gray-light)">…</span>`; }
    for (let i=s; i<=e; i++) html += `<button class="page-link ${i===currentPage?'active':''}" onclick="window.${fnName}(${i})">${i}</button>`;
    if (e<total) { if(e<total-1) html += `<span style="padding:0 .5rem;color:var(--gray-light)">…</span>`; html += `<button class="page-link" onclick="window.${fnName}(${total})">${total}</button>`; }
    html += `<button class="page-link" onclick="window.${fnName}(${currentPage+1})" ${currentPage>=total?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
    el.innerHTML = html;
}

function goPage(p) {
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    loadPickups();

    // Scroll smoothly to top of cards grid
    const target = document.getElementById('tripCardsGrid');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/* ============================================================
   FILTERS & DYNAMIC DROPDOWNS
============================================================ */
function updateCourierDropdown(outletSelectId, courierSelectId, defaultText) {
    const outletSelect = document.getElementById(outletSelectId);
    const courierSelect = document.getElementById(courierSelectId);
    if (!outletSelect || !courierSelect) return;

    const selectedOutletId = outletSelect.value;
    const currentCourierId = courierSelect.value;

    // Filter drivers
    let filteredDrivers = window.driversData || [];
    if (selectedOutletId) {
        filteredDrivers = filteredDrivers.filter(d => d.outlet_id === selectedOutletId);
    }

    // Generate HTML
    let html = `<option value="">${defaultText}</option>`;
    filteredDrivers.forEach(d => {
        html += `<option value="${d.id}">${d.name}</option>`;
    });

    courierSelect.innerHTML = html;

    // Retain previous value if it's still available in filtered list, else reset
    const stillExists = filteredDrivers.some(d => d.id === currentCourierId);
    if (stillExists) {
        courierSelect.value = currentCourierId;
    } else {
        courierSelect.value = "";
    }

    // Trigger Select2 update if initialized
    if ($.fn.select2 && $(courierSelect).data('select2')) {
        $(courierSelect).trigger('change.select2');
    }
}

function updateDriverFilterOptions() {
    updateCourierDropdown('filterOutlet', 'filterDriver', 'Semua Kurir');
}

function updateAddModalDriverOptions() {
    updateCourierDropdown('a-outlet', 'a-driver', '-- Pilih Kurir --');
}

function filterByStatus(status, el) {
    activeStatus = status;
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active-stat'));
    if (el) el.classList.add('active-stat');
    currentPage = 1;
    loadPickups();
}

function applyFilters() {
    currentPage = 1;
    loadPickups();
}

function resetFilters() {
    if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
    
    // Reset values and update Select2 UI
    if ($.fn.select2) {
        $('#filterOutlet').val('').trigger('change.select2');
        $('#filterDriver').val('').trigger('change.select2');
    } else {
        if (document.getElementById('filterOutlet')) document.getElementById('filterOutlet').value = '';
        if (document.getElementById('filterDriver')) document.getElementById('filterDriver').value = '';
    }
    
    updateDriverFilterOptions();
    
    if (document.getElementById('filterDate')) document.getElementById('filterDate').value = '';
    activeStatus = 'all';
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active-stat'));
    const allStat = document.querySelector('.stat-card.c1');
    if (allStat) allStat.classList.add('active-stat');
    currentPage = 1;
    loadPickups();
}

/* ============================================================
   DETAIL MODAL & ACTIONS
============================================================ */
const TL_STAGES = [
    {label:'Permintaan Diterima',icon:'fas fa-inbox'},
    {label:'Kurir Ditugaskan',icon:'fas fa-user-check'},
    {label:'Kurir Berangkat',icon:'fas fa-motorcycle'},
    {label:'Tiba di Lokasi',icon:'fas fa-map-marker-alt'},
    {label:'Cucian Dijemput',icon:'fas fa-box'},
    {label:'Siap Diantar',icon:'fas fa-box-open'},
    {label:'Kurir Antar',icon:'fas fa-shipping-fast'},
    {label:'Terkirim',icon:'fas fa-check-circle'},
];
const STATUS_PROGRESS = {menunggu: 1, jemput: 3, proses: 5, antar: 7, selesai: 8};

function buildTimeline(status) {
    const done = STATUS_PROGRESS[status] || 0;
    return TL_STAGES.map((s, i) => {
        const isDone = i < done, isCur = i === done;
        return `
        <div class="ttl-item">
            <div class="ttl-dot-wrap">
                <div class="ttl-dot ${isDone?'done':isCur?'current':''}"><i class="${s.icon}"></i></div>
                ${i < TL_STAGES.length-1 ? `<div class="ttl-line ${isDone?'done':''}"></div>` : ''}
            </div>
            <div class="ttl-content">
                <div class="ttl-title">${s.label}</div>
                <div class="ttl-desc">${isDone?'Selesai':isCur?'Sedang berlangsung':'Menunggu'}</div>
            </div>
        </div>`;
    }).join('');
}

function openDetail(id) {
    const t = tripsData.find(x => x.id === id);
    if (!t) return;
    activeTrip = t;
    document.getElementById('dm-id').textContent     = '#'+t.trip_code;
    document.getElementById('dm-date').textContent   = t.scheduled_date+' · '+t.scheduled_time+' WIB';
    document.getElementById('dm-cust').textContent   = t.customer_name;
    document.getElementById('dm-phone').textContent  = t.customer_phone;
    document.getElementById('dm-outlet').textContent = t.outlet_name || '—';
    document.getElementById('dm-order').textContent  = t.order_code ? '#'+t.order_code : '—';
    document.getElementById('dm-from').textContent   = t.address_from;
    document.getElementById('dm-to').textContent     = t.address_to;
    document.getElementById('dm-dist').textContent   = t.distance + ' km';
    document.getElementById('dm-eta').textContent    = t.eta;
    document.getElementById('dm-driver').textContent = t.driver_name || 'Belum ditugaskan';
    document.getElementById('dm-vehicle').textContent= t.driver_vehicle || '—';
    document.getElementById('dm-service').textContent= t.service_type;
    document.getElementById('dm-fee').textContent    = formatRp(t.fee);
    document.getElementById('dm-timeline').innerHTML = buildTimeline(t.status);
    document.getElementById('dm-notes').textContent  = t.notes || 'Tidak ada catatan.';
    
    // Set status modal trip ID label
    document.getElementById('sm-id').textContent     = '#'+t.trip_code;

    openModal('detailModal');
}

function openStatusForTrip(id) {
    const t = tripsData.find(x => x.id === id);
    if (!t) return;
    activeTrip = t;
    document.getElementById('sm-id').textContent = '#'+t.trip_code;
    
    // Highlight existing status
    document.querySelectorAll('.status-option').forEach(x => x.classList.remove('selected'));
    const targetOpt = Array.from(document.querySelectorAll('.status-option')).find(o => {
        const text = o.querySelector('.so-label').textContent.toLowerCase();
        if (t.status === 'menunggu' && text.includes('menunggu')) return true;
        if (t.status === 'jemput' && text.includes('jemput')) return true;
        if (t.status === 'proses' && text.includes('proses')) return true;
        if (t.status === 'antar' && text.includes('antar')) return true;
        if (t.status === 'selesai' && text.includes('selesai')) return true;
        if (t.status === 'batal' && text.includes('batal')) return true;
        return false;
    });
    if (targetOpt) {
        targetOpt.classList.add('selected');
        selectedStatus = t.status;
    }
    openModal('statusModal');
}

function openAssignForTrip(id) {
    const t = tripsData.find(x => x.id === id);
    if (!t) return;
    activeTrip = t;
    renderDriverOptions();
    openModal('assignModal');
}

function cancelTrip() {
    if (!activeTrip) return;
    if (!confirm(`Batalkan trip #${activeTrip.trip_code}?`)) return;
    
    $.ajax({
        url: `/shuttles/${activeTrip.id}`,
        method: 'DELETE',
        success: function(res) {
            if (res.success) {
                closeModal('detailModal');
                loadPickups();
                showToast('success', 'Trip Dibatalkan', `Trip #${activeTrip.trip_code} berhasil dibatalkan`);
            }
        },
        error: function() {
            showToast('error', 'Gagal', 'Gagal membatalkan trip');
        }
    });
}

/* ============================================================
   STATUS UPDATE
============================================================ */
function openStatusModal() {
    if (activeTrip) {
        document.getElementById('sm-id').textContent = '#'+activeTrip.trip_code;
        openStatusForTrip(activeTrip.id);
    }
}

function selectStatus(el, status) {
    document.querySelectorAll('.status-option').forEach(x => x.classList.remove('selected'));
    el.classList.add('selected');
    selectedStatus = status;
}

function confirmStatus() {
    if (!selectedStatus) {
        showToast('error', 'Pilih Status', 'Silakan pilih status terlebih dahulu');
        return;
    }
    if (!activeTrip) return;

    const payload = {
        customer_name: activeTrip.customer_name,
        customer_phone: activeTrip.customer_phone,
        customer_id: activeTrip.customer_id,
        outlet_id: activeTrip.outlet_id,
        order_code: activeTrip.order_code,
        address_from: activeTrip.address_from,
        address_to: activeTrip.address_to,
        service_type: activeTrip.service_type,
        employee_id: activeTrip.employee_id,
        distance: activeTrip.distance,
        eta: activeTrip.eta,
        fee: activeTrip.fee,
        scheduled_at: activeTrip.scheduled_at,
        weight: activeTrip.weight,
        notes: activeTrip.notes,
        status: selectedStatus
    };

    $.ajax({
        url: `/shuttles/${activeTrip.id}`,
        method: 'PUT',
        data: payload,
        success: function(res) {
            if (res.success) {
                closeModal('statusModal');
                closeModal('detailModal');
                loadPickups();
                showToast('success', 'Status Diperbarui', `Trip berhasil diperbarui ke status ${selectedStatus}`);
                selectedStatus = null;
            }
        },
        error: function(err) {
            showToast('error', 'Gagal', 'Gagal memperbarui status trip');
        }
    });
}

/* ============================================================
   ASSIGN DRIVER
============================================================ */
function openAssignModal() {
    if (activeTrip) {
        openAssignForTrip(activeTrip.id);
    }
}

function renderDriverOptions() {
    const el = document.getElementById('driverOptions');
    if (!el) return;

    if (!window.driversData || window.driversData.length === 0) {
        el.innerHTML = `<div style="text-align:center;padding:1.5rem;color:var(--gray-light);font-size:.9rem">Tidak ada kurir terdaftar</div>`;
        return;
    }

    // Filter drivers by activeTrip's outlet
    let drivers = window.driversData;
    if (activeTrip && activeTrip.outlet_id) {
        drivers = window.driversData.filter(d => d.outlet_id === activeTrip.outlet_id);
    }

    if (drivers.length === 0) {
        el.innerHTML = `<div style="text-align:center;padding:1.5rem;color:var(--gray-light);font-size:.9rem">Tidak ada kurir yang terdaftar di outlet ini</div>`;
        return;
    }

    el.innerHTML = drivers.map(d => `
        <div class="driver-option" onclick="window.selectDriver(this,'${d.id}')">
            <div class="driver-option-avatar" style="background:#6366F1">${getInitials(d.name)}</div>
            <div class="driver-option-info">
                <div class="driver-option-name">${d.name}</div>
                <div class="driver-option-vehicle">${d.code ? 'Motor - ' + d.code : 'Motor Kurir'}</div>
            </div>
            <span class="driver-option-status" style="color:var(--secondary)">
                ● Aktif
            </span>
        </div>`).join('');
}

function selectDriver(el, driverId) {
    document.querySelectorAll('.driver-option').forEach(x => x.classList.remove('selected'));
    el.classList.add('selected');
    selectedDriver = window.driversData.find(d => d.id === driverId);
}

function confirmAssign() {
    if (!selectedDriver) {
        showToast('error', 'Pilih Kurir', 'Silakan pilih kurir terlebih dahulu');
        return;
    }
    if (!activeTrip) return;

    const newStatus = activeTrip.status === 'menunggu' ? 'jemput' : activeTrip.status;

    const payload = {
        customer_name: activeTrip.customer_name,
        customer_phone: activeTrip.customer_phone,
        customer_id: activeTrip.customer_id,
        outlet_id: activeTrip.outlet_id,
        order_code: activeTrip.order_code,
        address_from: activeTrip.address_from,
        address_to: activeTrip.address_to,
        service_type: activeTrip.service_type,
        employee_id: selectedDriver.id,
        distance: activeTrip.distance,
        eta: activeTrip.eta,
        fee: activeTrip.fee,
        scheduled_at: activeTrip.scheduled_at,
        weight: activeTrip.weight,
        notes: activeTrip.notes,
        status: newStatus
    };

    $.ajax({
        url: `/shuttles/${activeTrip.id}`,
        method: 'PUT',
        data: payload,
        success: function(res) {
            if (res.success) {
                closeModal('assignModal');
                closeModal('detailModal');
                loadPickups();
                showToast('success', 'Kurir Ditugaskan', `${selectedDriver.name} berhasil ditugaskan`);
                selectedDriver = null;
            }
        },
        error: function() {
            showToast('error', 'Gagal', 'Gagal menugaskan kurir');
        }
    });
}

/* ============================================================
   ADD TRIP
============================================================ */
function openAddModal() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    if (document.getElementById('a-time')) {
        document.getElementById('a-time').value = now.toISOString().slice(0, 16);
    }
    
    // Reset form inputs
    ['a-cust', 'a-phone', 'a-from', 'a-to', 'a-order', 'a-weight', 'a-notes'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    if ($.fn.select2) {
        $('#a-outlet').val('').trigger('change.select2');
        $('#a-driver').val('').trigger('change.select2');
    }

    // Update driver dropdown options based on selected outlet in modal
    updateAddModalDriverOptions();
    openModal('addModal');
}

function saveTrip() {
    const cust = document.getElementById('a-cust').value.trim();
    const phone = document.getElementById('a-phone').value.trim();
    const from = document.getElementById('a-from').value.trim();
    const to = document.getElementById('a-to').value.trim();
    const sched = document.getElementById('a-time').value;
    
    if (!cust || !phone || !from || !to || !sched) {
        showToast('error', 'Validasi', 'Mohon lengkapi semua field wajib');
        return;
    }

    const outletId = document.getElementById('a-outlet').value;
    const orderCode = document.getElementById('a-order').value.trim();
    const serviceType = document.getElementById('a-service').value;
    const driverId = document.getElementById('a-driver').value;
    const weight = document.getElementById('a-weight').value.trim();
    const notes = document.getElementById('a-notes').value.trim();

    const payload = {
        customer_name: cust,
        customer_phone: phone,
        address_from: from,
        address_to: to,
        scheduled_at: sched,
        outlet_id: outletId || null,
        order_code: orderCode || null,
        service_type: serviceType,
        employee_id: driverId || null,
        weight: weight || null,
        notes: notes || null,
        status: driverId ? 'jemput' : 'menunggu'
    };

    $.ajax({
        url: '/shuttles',
        method: 'POST',
        data: payload,
        success: function(res) {
            if (res.success) {
                // Clear fields
                ['a-cust', 'a-phone', 'a-order', 'a-weight', 'a-notes'].forEach(id => {
                    if (document.getElementById(id)) document.getElementById(id).value = '';
                });
                if (document.getElementById('a-from')) document.getElementById('a-from').value = '';
                if (document.getElementById('a-to')) document.getElementById('a-to').value = '';
                
                closeModal('addModal');
                loadPickups();
                showToast('success', 'Trip Dibuat', `Trip baru untuk ${cust} berhasil dibuat`);
            }
        },
        error: function(err) {
            let errorMsg = 'Gagal membuat trip baru';
            if (err.responseJSON && err.responseJSON.message) {
                errorMsg = err.responseJSON.message;
            }
            showToast('error', 'Gagal', errorMsg);
        }
    });
}

/* ============================================================
   HELPERS & UTILS
============================================================ */
function getInitials(name) {
    if (!name) return '??';
    return name.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
}

function formatRp(n) {
    if (n === null || n === undefined) return 'Rp 0';
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('show');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('show');
}

function closeModalOut(e, id) {
    if (e.target === e.currentTarget) closeModal(id);
}

function showToast(type, title, msg) {
    const wrap = document.getElementById('toastWrap');
    if (!wrap) return;
    const t = document.createElement('div');
    t.className = 'toast';
    t.innerHTML = `
        <div class="toast-icon ${type}"><i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info-circle'}"></i></div>
        <div style="flex:1"><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div>
        <button class="toast-x" onclick="this.closest('.toast').remove()"><i class="fas fa-times"></i></button>`;
    wrap.appendChild(t);
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => {
        t.classList.remove('show');
        setTimeout(() => t.remove(), 400);
    }, 4000);
}

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
        else if (p < 2 / d) e = n * (p -= 1.5 / d) * p + .75;
        else if (p < 2.5 / d) e = n * (p -= 2.25 / d) * p + .9375;
        else e = n * (p -= 2.625 / d) * p + .984375;
        window.scrollTo(0, start * (1 - e));
        if (p < 1) requestAnimationFrame(bounce);
    }
    requestAnimationFrame(bounce);
}

// Expose functions globally to prevent ReferenceErrors due to minification
window.openAddModal = openAddModal;
window.saveTrip = saveTrip;
window.openDetail = openDetail;
window.openStatusForTrip = openStatusForTrip;
window.openAssignForTrip = openAssignForTrip;
window.cancelTrip = cancelTrip;
window.openStatusModal = openStatusModal;
window.selectStatus = selectStatus;
window.confirmStatus = confirmStatus;
window.openAssignModal = openAssignModal;
window.selectDriver = selectDriver;
window.confirmAssign = confirmAssign;
window.closeModal = closeModal;
window.closeModalOut = closeModalOut;
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;
window.filterByStatus = filterByStatus;
window.goPage = goPage;
window.showToast = showToast;
window.scrollToTop = scrollToTop;
window.updateDriverFilterOptions = updateDriverFilterOptions;
window.updateAddModalDriverOptions = updateAddModalDriverOptions;
