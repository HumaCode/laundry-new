// Setup CSRF token for jQuery AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let searchQuery    = '';
let filterStatus   = '';
let filterMethod   = '';
let filterOutlet   = '';
let filterSort     = 'recent';
let paymentsData   = [];
let currentPage    = 1;
let perPage        = 10;
let totalItems     = 0;
let activeId       = null;

// Initialize Page
$(document).ready(function() {
    loadPayments();

    // Hook search inputs
    const searchEl = document.getElementById('searchInput');
    if (searchEl) {
        searchEl.addEventListener('input', function(e) {
            searchQuery = e.target.value;
            currentPage = 1;
            loadPayments();
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
function loadPayments() {
    showSkeletonTable();
    
    $.ajax({
        url: '/payments',
        method: 'GET',
        data: {
            search: searchQuery,
            payment_status: filterStatus,
            payment_method: filterMethod,
            outlet_id: filterOutlet,
            sort: filterSort,
            per_page: perPage,
            page: currentPage
        },
        success: function(res) {
            if (res.success && res.data) {
                paymentsData = res.data.data || [];
                totalItems = res.data.meta ? res.data.meta.total : 0;
                currentPage = res.data.meta ? res.data.meta.current_page : 1;
                
                updateStats(res.data.stats);
                renderTable();
                
                if (res.data.meta) {
                    renderPagination(res.data.meta);
                } else {
                    const pagBar = document.getElementById('paginationBar');
                    if (pagBar) pagBar.style.display = 'none';
                }
            }
        },
        error: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal memuat data pembayaran dari server', 'error', 'Error');
            }
        }
    });
}

function showSkeletonTable() {
    const tbody = document.getElementById('paymentTableBody');
    if (!tbody) return;

    let rowsHtml = '';
    for (let i = 0; i < perPage; i++) {
        rowsHtml += `
            <tr class="skeleton-row">
                <td><div class="skeleton-bar" style="width: 80px"></div></td>
                <td><div class="skeleton-bar" style="width: 140px; margin-bottom: 6px"></div><div class="skeleton-bar" style="width: 90px"></div></td>
                <td><div class="skeleton-bar" style="width: 100px"></div></td>
                <td><div class="skeleton-bar" style="width: 80px"></div></td>
                <td><div class="skeleton-bar" style="width: 70px"></div></td>
                <td><div class="skeleton-bar" style="width: 60px"></div></td>
                <td><div class="skeleton-bar" style="width: 100px"></div></td>
                <td>
                    <div class="action-cell">
                        <div class="skeleton-bar" style="width: 32px; height: 32px; border-radius: 8px"></div>
                        <div class="skeleton-bar" style="width: 32px; height: 32px; border-radius: 8px"></div>
                    </div>
                </td>
            </tr>
        `;
    }
    tbody.innerHTML = rowsHtml;
}

function updateStats(stats) {
    if (!stats) return;

    const formatCurrency = (val) => {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
    };

    // Update stats elements with animation
    animateValue('statTotalPendapatan', stats.total_pendapatan, formatCurrency);
    animateValue('statTotalPiutang', stats.total_piutang, formatCurrency);
    animateValue('statCountLunas', stats.count_lunas, val => new Intl.NumberFormat('id-ID').format(val));
    animateValue('statCountBelum', stats.count_belum, val => new Intl.NumberFormat('id-ID').format(val));
}

function animateValue(id, endVal, formatter) {
    const obj = document.getElementById(id);
    if (!obj) return;
    
    // Quick directly formatted display if element doesn't have transition class or value is same
    const currentVal = parseInt(obj.getAttribute('data-value') || '0');
    if (currentVal === endVal) {
        obj.textContent = formatter(endVal);
        return;
    }

    obj.setAttribute('data-value', endVal);
    let start = currentVal;
    let duration = 800;
    let startTime = null;

    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const value = Math.floor(progress * (endVal - start) + start);
        obj.textContent = formatter(value);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.textContent = formatter(endVal);
        }
    }
    window.requestAnimationFrame(step);
}

function renderTable() {
    const tbody = document.getElementById('paymentTableBody');
    const emptyState = document.getElementById('emptyState');
    if (!tbody) return;

    if (paymentsData.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        
        const countText = document.getElementById('showCount');
        if (countText) countText.textContent = '0';
        
        const totalText = document.getElementById('totalCount');
        if (totalText) totalText.textContent = '0';
        return;
    }

    if (emptyState) emptyState.style.display = 'none';

    tbody.innerHTML = paymentsData.map(order => {
        const badgeClass = order.payment_status === 'Lunas' ? 'badge-lunas' : 'badge-belum';
        const formattedPrice = 'Rp ' + new Intl.NumberFormat('id-ID').format(order.total_price);
        
        return `
            <tr>
                <td><span class="order-code-text">#${order.order_code}</span></td>
                <td>
                    <div class="cust-name-text">${order.customer}</div>
                    <div class="cust-phone-text">${order.customer_phone || '-'}</div>
                </td>
                <td><div>${order.service_type}</div><div class="cust-phone-text">${order.weight} kg</div></td>
                <td><div>${order.outlet}</div></td>
                <td><span class="payment-badge ${badgeClass}">${order.payment_status}</span></td>
                <td><span class="badge bg-light text-dark font-weight-bold" style="padding:0.3rem 0.6rem; border-radius:6px; font-size:0.75rem">${order.payment_method}</span></td>
                <td><div>${order.created_at_formatted}</div></td>
                <td>
                    <div class="action-cell">
                        <button class="act-btn act-btn-view" onclick="openDetailModal('${order.id}')" title="Detail"><i class="fas fa-eye"></i></button>
                        <button class="act-btn act-btn-pay" onclick="openPaymentModal('${order.id}')" title="Bayar / Edit"><i class="fas fa-credit-card"></i></button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    // Update counts
    const countText = document.getElementById('showCount');
    if (countText) countText.textContent = paymentsData.length.toString();
    
    const totalText = document.getElementById('totalCount');
    if (totalText) totalText.textContent = totalItems.toString();
}

function renderPagination(meta) {
    const container = document.getElementById('paginationNav');
    if (!container) return;

    if (meta.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-rounded justify-content-center mb-0">';
    
    // Previous Link
    if (meta.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="goPage(${meta.current_page - 1})"><</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link"><</span></li>`;
    }

    // Smart Page Links
    const totalPages = meta.last_page;
    const current = meta.current_page;
    const range = 2; // Number of pages to show before and after current page

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= current - range && i <= current + range)) {
            if (i === current) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="goPage(${i})">${i}</a></li>`;
            }
        } else if (i === current - range - 1 || i === current + range + 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Next Link
    if (meta.current_page < meta.last_page) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="goPage(${meta.current_page + 1})">></a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">></span></li>`;
    }

    html += '</ul>';
    container.innerHTML = html;
}

function goPage(page) {
    currentPage = page;
    loadPayments();
    
    // Smooth scroll to table view section (clean UI transition without jumping to the top of body)
    const tableEl = document.getElementById('tableView');
    if (tableEl) {
        tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/* ============================================================
   FILTERS & ACTION HANDLERS
============================================================ */
function applyFilters() {
    filterStatus = document.getElementById('filterStatus').value;
    filterMethod = document.getElementById('filterMethod').value;
    filterOutlet = document.getElementById('filterOutlet').value;
    filterSort   = document.getElementById('filterSort').value;
    currentPage  = 1;
    loadPayments();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterMethod').value = '';
    document.getElementById('filterOutlet').value = '';
    document.getElementById('filterSort').value = 'recent';
    
    searchQuery  = '';
    filterStatus = '';
    filterMethod = '';
    filterOutlet = '';
    filterSort   = 'recent';
    currentPage  = 1;
    loadPayments();
}

function changePerPage(val) {
    perPage = parseInt(val);
    currentPage = 1;
    loadPayments();
}

/* ============================================================
   MODALS
============================================================ */
function openDetailModal(id) {
    activeId = id;
    $.ajax({
        url: `/payments/${id}`,
        method: 'GET',
        success: function(res) {
            if (res.success && res.data) {
                const order = res.data;
                const formattedPrice = 'Rp ' + new Intl.NumberFormat('id-ID').format(order.total_price);
                const badgeClass = order.payment_status === 'Lunas' ? 'badge-lunas' : 'badge-belum';

                document.getElementById('detOrderCode').textContent = '#' + order.order_code;
                document.getElementById('detCustomer').textContent = order.customer;
                document.getElementById('detPhone').textContent = order.customer_phone || '-';
                document.getElementById('detOutlet').textContent = order.outlet;
                document.getElementById('detService').textContent = order.service_type + ` (${order.weight} kg)`;
                document.getElementById('detAmount').textContent = formattedPrice;
                document.getElementById('detDate').textContent = order.created_at_formatted;
                
                const statusBadge = document.getElementById('detStatus');
                statusBadge.className = `payment-badge ${badgeClass}`;
                statusBadge.textContent = order.payment_status;
                
                document.getElementById('detMethod').textContent = order.payment_method;
                document.getElementById('detNotes').textContent = order.notes || 'Tidak ada catatan';

                document.getElementById('detailModal').classList.add('show');
            }
        }
    });
}

function openPaymentModal(id) {
    activeId = id;
    $.ajax({
        url: `/payments/${id}`,
        method: 'GET',
        success: function(res) {
            if (res.success && res.data) {
                const order = res.data;
                const formattedPrice = 'Rp ' + new Intl.NumberFormat('id-ID').format(order.total_price);

                document.getElementById('payOrderCode').textContent = '#' + order.order_code;
                document.getElementById('payCustomer').textContent = order.customer;
                document.getElementById('payAmount').textContent = formattedPrice;

                const statusSelect = document.getElementById('payStatus');
                statusSelect.value = order.payment_status;
                
                const methodSelect = document.getElementById('payMethod');
                methodSelect.value = order.payment_method || 'Tunai';

                toggleMethodSelect(order.payment_status);

                document.getElementById('paymentModal').classList.add('show');
            }
        }
    });
}

function toggleMethodSelect(status) {
    const container = document.getElementById('methodSelectContainer');
    if (status === 'Lunas') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function savePayment() {
    const status = document.getElementById('payStatus').value;
    const method = document.getElementById('payMethod').value;

    const btn = document.getElementById('btnSavePayment');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

    $.ajax({
        url: `/payments/${activeId}`,
        method: 'PUT',
        data: {
            payment_status: status,
            payment_method: method
        },
        success: function(res) {
            if (res.success) {
                closeModal('paymentModal');
                if (typeof window.showToast === 'function') {
                    window.showToast('Pembayaran berhasil diperbarui', 'success', 'Berhasil');
                }
                loadPayments();
            }
        },
        error: function() {
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal memproses pembayaran', 'error', 'Error');
            }
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('show');
}

function closeModalOutside(e, id) {
    if (e.target === e.currentTarget) closeModal(id);
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Export functions to window
window.openDetailModal = openDetailModal;
window.openPaymentModal = openPaymentModal;
window.toggleMethodSelect = toggleMethodSelect;
window.savePayment = savePayment;
window.closeModal = closeModal;
window.closeModalOutside = closeModalOutside;
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;
window.changePerPage = changePerPage;
window.scrollToTop = scrollToTop;
window.goPage = goPage;
