// Set CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// App State
let currentPage = 1;
let perPage = 10;
let activeStatusFilter = '';
let selectedOrders = new Set();
let activeOrder = null;
let selectedNewStatus = null;

$(document).ready(function () {
    // Initial fetch
    applyFilters();

    // Setup event listeners for forms
    $('#orderForm').on('submit', function (e) {
        e.preventDefault();
        saveOrder();
    });

    // Check all rows checkbox handler
    $('#checkAll').on('change', function () {
        toggleAllCheck();
    });
});

// Toast notification helper
function showToast(type, title, message) {
    const toastHtml = `
        <div class="toast show">
            <div class="toast-icon ${type}">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
            </div>
            <div>
                <div class="toast-title">${title}</div>
                <div class="toast-msg">${message}</div>
            </div>
            <button class="toast-close-btn" onclick="$(this).closest('.toast').remove()"><i class="fas fa-times"></i></button>
        </div>
    `;
    
    // Check if toast wrapper exists, if not create it
    if ($('.toast-wrap').length === 0) {
        $('body').append('<div class="toast-wrap"></div>');
    }
    
    const $toast = $(toastHtml);
    $('.toast-wrap').append($toast);
    
    setTimeout(() => {
        $toast.fadeOut(400, function() { $(this).remove(); });
    }, 4000);
}

// Fetch orders with current filters
function applyFilters() {
    const search = $('#searchInput').val() || '';
    const outlet_id = $('#filterOutlet').val() || '';
    const order_status = activeStatusFilter || $('#filterStatus').val() || '';
    const payment_status = $('#filterBayar').val() || '';
    const sort = $('#filterSort').val() || 'recent';

    // Dates
    const from_date = $('#filterDateFrom').val() || '';
    const to_date = $('#filterDateTo').val() || '';
    let date_range = '';
    if (from_date && to_date) {
        date_range = `${from_date} - ${to_date}`;
    }

    // Set page controls info
    const reqData = {
        search,
        outlet_id,
        order_status,
        payment_status,
        sort,
        date_range,
        page: currentPage,
        per_page: perPage
    };

    // Show skeleton loaders in table
    renderSkeletons();

    $.ajax({
        url: '/orders',
        method: 'GET',
        data: reqData,
        success: function (res) {
            if (res.success) {
                renderTableData(res.data.data);
                updatePagination(res.data.meta);
                updateSummaryStats(res.data.stats);
            }
        },
        error: function () {
            showToast('error', 'Error', 'Gagal memuat data order');
        }
    });
}

// Render skeletons while fetching
function renderSkeletons() {
    let rows = '';
    for (let i = 0; i < 5; i++) {
        rows += `
            <tr class="skeleton-row">
                <td class="cb-cell"><div style="width:18px;height:18px;background:#e5e7eb;border-radius:4px"></div></td>
                <td><div style="width:100px;height:16px;background:#e5e7eb;border-radius:4px;margin-bottom:6px"></div><div style="width:80px;height:12px;background:#f3f4f6;border-radius:4px"></div></td>
                <td><div style="width:140px;height:16px;background:#e5e7eb;border-radius:4px;margin-bottom:6px"></div><div style="width:100px;height:12px;background:#f3f4f6;border-radius:4px"></div></td>
                <td><div style="width:120px;height:16px;background:#e5e7eb;border-radius:4px;margin-bottom:6px"></div><div style="width:90px;height:12px;background:#f3f4f6;border-radius:4px"></div></td>
                <td><div style="width:110px;height:22px;background:#e5e7eb;border-radius:20px"></div></td>
                <td><div style="width:80px;height:22px;background:#e5e7eb;border-radius:20px"></div></td>
                <td><div style="width:80px;height:22px;background:#e5e7eb;border-radius:20px"></div></td>
                <td><div style="width:90px;height:16px;background:#e5e7eb;border-radius:4px"></div></td>
                <td><div style="display:flex;gap:4px"><div style="width:28px;height:28px;background:#e5e7eb;border-radius:6px"></div><div style="width:28px;height:28px;background:#e5e7eb;border-radius:6px"></div></div></td>
            </tr>
        `;
    }
    $('#orderTableBody').html(rows);
}

// Format IDR Rupiah currency helper
function formatRp(value) {
    return 'Rp ' + parseInt(value).toLocaleString('id-ID');
}

// Translate and map badges
function getStatusBadge(status) {
    const label = status || 'Baru';
    let cls = 'badge-status ';
    if (label === 'Baru') cls += 'baru';
    else if (label === 'Proses') cls += 'proses';
    else if (label === 'Selesai') cls += 'selesai';
    else if (label === 'Diambil') cls += 'diambil';
    
    return `<span class="badge-status ${cls.split(' ')[1]}"><i class="fas ${label === 'Baru' ? 'fa-inbox' : label === 'Proses' ? 'fa-spinner' : label === 'Selesai' ? 'fa-check-circle' : 'fa-flag-checkered'}"></i> ${label}</span>`;
}

function getPaymentBadge(status) {
    const label = status || 'Belum';
    let cls = 'badge-pay ';
    if (label === 'Lunas') cls += 'lunas';
    else cls += 'belum';

    return `<span class="badge-pay ${cls.split(' ')[1]}"><i class="fas ${label === 'Lunas' ? 'fa-check' : 'fa-times'}"></i> ${label}</span>`;
}

// Render data into the table
function renderTableData(data) {
    const $tbody = $('#orderTableBody');
    if (!data || data.length === 0) {
        $tbody.html('');
        $('#emptyState').show();
        return;
    }

    $('#emptyState').hide();
    let html = '';
    
    data.forEach(order => {
        const isChecked = selectedOrders.has(order.id);
        const weightLabel = `${order.weight} kg`;
        
        html += `
            <tr data-id="${order.id}">
                <td class="cb-cell">
                    <input type="checkbox" class="custom-cb row-cb" data-id="${order.id}" ${isChecked ? 'checked' : ''} onchange="toggleRowCheck(this)">
                </td>
                <td>
                    <a href="javascript:void(0)" onclick="openDetail('${order.id}')" class="order-code-link">#${order.order_code}</a>
                    <div style="font-size:0.75rem;color:var(--gray);margin-top:2px"><i class="fas fa-clock"></i> ${order.created_at_formatted}</div>
                </td>
                <td class="customer-info-cell">
                    <span class="customer-name">${order.customer}</span>
                    <span class="customer-phone">${order.customer_phone}</span>
                </td>
                <td>
                    <div style="font-weight:600">${order.service_type}</div>
                    <div style="font-size:0.75rem;color:var(--gray-light)">${weightLabel} × ${formatRp(order.price_per_unit)}</div>
                </td>
                <td>
                    <span style="font-size:0.8rem;font-weight:500;color:var(--gray)"><i class="fas fa-store"></i> ${order.outlet}</span>
                </td>
                <td>${getStatusBadge(order.order_status)}</td>
                <td>${getPaymentBadge(order.payment_status)}</td>
                <td class="order-amount">${formatRp(order.total_price)}</td>
                <td>
                    <div style="display:flex;gap:0.35rem">
                        <button class="btn-page btn-page-outline" style="padding:0.4rem 0.6rem" title="Detail" onclick="openDetail('${order.id}')"><i class="fas fa-eye"></i></button>
                        <button class="btn-page btn-page-outline" style="padding:0.4rem 0.6rem" title="Edit" onclick="openEditModal('${order.id}')"><i class="fas fa-pen"></i></button>
                        <button class="btn-page btn-page-outline" style="padding:0.4rem 0.6rem;color:var(--danger);border-color:rgba(239,68,68,0.2)" title="Hapus" onclick="deleteOrder('${order.id}')"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            </tr>
        `;
    });

    $tbody.html(html);
}

// Update counts & status cards
function updateSummaryStats(stats) {
    if (!stats) return;
    
    $('#count-all').text(stats.total_orders.toLocaleString());
    $('#count-diterima').text(stats.processing_orders.toLocaleString()); // using processing as proxy for status filter
    $('#count-proses').text(stats.processing_orders.toLocaleString());
    $('#count-selesai').text(stats.completed_orders.toLocaleString());
    
    // Format Revenue for Monthly Revenue Card
    $('#revenue-monthly').text(formatRp(stats.monthly_revenue));
}

// Pagination setup
function updatePagination(meta) {
    $('#showCount').text(meta.to - meta.from + 1 || 0);
    $('#totalCount').text(meta.total);
    $('#currentPage').text(meta.current_page);
    $('#totalPages').text(meta.last_page);

    const $controls = $('#paginationControls');
    let html = `
        <button class="page-btn" onclick="changePage(${meta.current_page - 1})" ${meta.current_page <= 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    for (let i = 1; i <= meta.last_page; i++) {
        if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
            html += `
                <button class="page-btn ${i === meta.current_page ? 'active' : ''}" onclick="changePage(${i})">
                    ${i}
                </button>
            `;
        } else if (i === 2 || i === meta.last_page - 1) {
            html += `<span style="padding:0 .25rem;color:var(--gray-light)">…</span>`;
        }
    }

    html += `
        <button class="page-btn" onclick="changePage(${meta.current_page + 1})" ${meta.current_page >= meta.last_page ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    $controls.html(html);
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    applyFilters();
}

function changePerPage(limit) {
    perPage = limit;
    currentPage = 1;
    applyFilters();
}

// Reset filters
function resetFilters() {
    $('#searchInput').val('');
    $('#filterOutlet').val('');
    $('#filterStatus').val('');
    $('#filterBayar').val('');
    $('#filterDateFrom').val('');
    $('#filterDateTo').val('');
    activeStatusFilter = '';
    $('.summary-card').removeClass('active-filter');
    $('.summary-card.all').addClass('active-filter');
    currentPage = 1;
    applyFilters();
}

// Summary cards status filter trigger
function filterByStatus(status, element) {
    $('.summary-card').removeClass('active-filter');
    $(element).addClass('active-filter');
    
    if (status === 'all') {
        activeStatusFilter = '';
    } else if (status === 'diterima') {
        activeStatusFilter = 'Baru';
    } else if (status === 'proses') {
        activeStatusFilter = 'Proses';
    } else if (status === 'siap') {
        activeStatusFilter = 'Selesai';
    } else if (status === 'selesai') {
        activeStatusFilter = 'Diambil';
    }
    
    currentPage = 1;
    applyFilters();
}

// Selection logic
function toggleRowCheck(el) {
    const id = $(el).data('id');
    if (el.checked) {
        selectedOrders.add(id);
    } else {
        selectedOrders.delete(id);
    }
    updateBulkBar();
}

function toggleAllCheck() {
    const checked = $('#checkAll')[0].checked;
    $('.row-cb').each(function () {
        this.checked = checked;
        const id = $(this).data('id');
        if (checked) {
            selectedOrders.add(id);
        } else {
            selectedOrders.delete(id);
        }
    });
    updateBulkBar();
}

function syncCheckAll() {
    const rowCount = $('.row-cb').length;
    const checkedCount = $('.row-cb:checked').length;
    $('#checkAll')[0].checked = rowCount > 0 && rowCount === checkedCount;
}

function clearSelection() {
    selectedOrders.clear();
    $('.row-cb').each(function () { this.checked = false; });
    $('#checkAll')[0].checked = false;
    updateBulkBar();
}

function updateBulkBar() {
    const count = selectedOrders.size;
    $('#bulkCountText').text(count);
    if (count > 0) {
        $('#bulkBar').addClass('show');
    } else {
        $('#bulkBar').removeClass('show');
    }
}

// Open Detail Modal
function openDetail(id) {
    $.ajax({
        url: `/orders/${id}`,
        method: 'GET',
        success: function (res) {
            if (res.success) {
                const order = res.data;
                activeOrder = order;

                $('#modalOrderId').text(`Order #${order.order_code}`);
                $('#modalOrderDate').text(order.created_at_formatted);
                $('#m-customer').text(order.customer);
                $('#m-phone').text(order.customer_phone || '-');
                $('#m-outlet').text(order.outlet);
                $('#m-kasir').text('Admin Kasir');
                $('#m-service').text(order.service_type);
                $('#m-type').text(order.service_type.includes('Kilo') || order.service_type.includes('Cuci') ? 'Kiloan' : 'Satuan');
                $('#m-qty').text(`${order.weight} kg`);
                $('#m-price').text(formatRp(order.price_per_unit));
                $('#m-total').text(formatRp(order.total_price));
                $('#m-paystatus').html(getPaymentBadge(order.payment_status));
                $('#m-paymethod').text(order.payment_method || '-');
                $('#m-notes').text(order.notes || 'Tidak ada catatan.');

                // Render Progress Timeline
                renderTimeline(order.order_status, order.payment_status);

                $('#detailModal').addClass('show');
            }
        },
        error: function () {
            showToast('error', 'Error', 'Gagal memuat detail order');
        }
    });
}

// Render progress timeline dynamically
function renderTimeline(orderStatus, paymentStatus) {
    const steps = [
        { key: 'Baru', title: 'Order Diterima', desc: 'Order laundry didaftarkan ke sistem' },
        { key: 'Proses', title: 'Sedang Diproses', desc: 'Pakaian sedang dicuci, dikeringkan atau disetrika' },
        { key: 'Selesai', title: 'Selesai', desc: 'Pakaian rapi dan siap diambil pelanggan' },
        { key: 'Diambil', title: 'Sudah Diambil', desc: 'Order diserahterimakan ke pelanggan' }
    ];

    let currentIdx = steps.findIndex(s => s.key === orderStatus);
    if (currentIdx === -1) currentIdx = 0;

    let html = '';
    steps.forEach((step, idx) => {
        let dotClass = '';
        let lineClass = '';
        
        if (idx < currentIdx) {
            dotClass = 'done';
            lineClass = 'done';
        } else if (idx === currentIdx) {
            dotClass = 'current';
        }
        
        html += `
            <div class="tl-item">
                <div class="tl-dot-wrap">
                    <div class="tl-dot ${dotClass}">
                        <i class="fas ${dotClass === 'done' ? 'fa-check' : step.key === 'Baru' ? 'fa-inbox' : step.key === 'Proses' ? 'fa-spinner' : step.key === 'Selesai' ? 'fa-check-circle' : 'fa-flag-checkered'}"></i>
                    </div>
                    <div class="tl-line ${lineClass}"></div>
                </div>
                <div class="tl-content">
                    <div class="tl-title">${step.title}</div>
                    <div class="tl-desc">${step.desc}</div>
                </div>
            </div>
        `;
    });

    $('#m-timeline').html(html);
}

// Close Modals
function closeModal(id) {
    $(`#${id}`).removeClass('show');
}

function closeModalOutside(event) {
    if ($(event.target).hasClass('modal-overlay')) {
        $('.modal-overlay').removeClass('show');
    }
}

// Save Order (Create / Update)
function saveOrder() {
    const id = $('#orderId').val();
    const data = {
        customer_id: $('#orderCustomer').val(),
        outlet_id: $('#orderOutlet').val(),
        service_type: $('#orderServiceType').val(),
        weight: $('#orderWeight').val(),
        price_per_unit: $('#orderPricePerUnit').val(),
        order_status: $('#orderStatusSelect').val() || 'Baru',
        payment_status: $('#orderPaymentStatusSelect').val() || 'Belum',
        payment_method: $('#orderPaymentMethod').val() || 'Tunai',
        notes: $('#orderNotes').val() || ''
    };

    const isEdit = !!id;
    const url = isEdit ? `/orders/${id}` : '/orders';
    const method = isEdit ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function (res) {
            if (res.success) {
                closeModal('custModal');
                applyFilters();
                showToast('success', 'Berhasil', isEdit ? 'Order berhasil diperbarui' : 'Order baru berhasil dibuat');
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $('.invalid-feedback').text('').parent().find('.form-control').removeClass('is-invalid');
                Object.keys(errors).forEach(key => {
                    const $input = $(`[name="${key}"]`);
                    $input.addClass('is-invalid');
                    $input.parent().find('.invalid-feedback').text(errors[key][0]);
                });
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan sistem');
            }
        }
    });
}

// Open Edit Modal
function openEditModal(id) {
    $.ajax({
        url: `/orders/${id}`,
        method: 'GET',
        success: function (res) {
            if (res.success) {
                const order = res.data;
                $('#modalBoxTitle').text('Edit Order');
                $('#orderId').val(order.id);
                $('#orderCustomer').val(order.customer_id);
                $('#orderOutlet').val(order.outlet_id);
                $('#orderServiceType').val(order.service_type);
                $('#orderWeight').val(order.weight);
                $('#orderPricePerUnit').val(order.price_per_unit);
                $('#orderStatusSelect').val(order.order_status);
                $('#orderPaymentStatusSelect').val(order.payment_status);
                $('#orderPaymentMethod').val(order.payment_method);
                $('#orderNotes').val(order.notes);

                // Clear previous invalid validations
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $('#custModal').addClass('show');
            }
        },
        error: function () {
            showToast('error', 'Error', 'Gagal memuat detail order');
        }
    });
}

// Open Add Modal
function openAddModal() {
    $('#modalBoxTitle').text('Buat Order Baru');
    $('#orderId').val('');
    $('#orderCustomer').val('');
    $('#orderOutlet').val('');
    $('#orderServiceType').val('Cuci Setrika');
    $('#orderWeight').val('1');
    $('#orderPricePerUnit').val('8000');
    $('#orderStatusSelect').val('Baru');
    $('#orderPaymentStatusSelect').val('Belum');
    $('#orderPaymentMethod').val('Tunai');
    $('#orderNotes').val('');

    // Clear previous validations
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    $('#custModal').addClass('show');
}

// Delete Order
function deleteOrder(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus order ini?')) return;

    $.ajax({
        url: `/orders/${id}`,
        method: 'DELETE',
        success: function (res) {
            if (res.success) {
                closeModal('detailModal');
                applyFilters();
                showToast('success', 'Dihapus', 'Order berhasil dihapus');
            }
        },
        error: function () {
            showToast('error', 'Error', 'Gagal menghapus order');
        }
    });
}

// Open Status Modal inside Detail Drawer
function openStatusModal() {
    if (!activeOrder) return;
    
    $('#statusModalOrderId').text(`Order #${activeOrder.order_code}`);
    $('.status-option').removeClass('selected');
    $(`.status-option[data-status="${activeOrder.order_status}"]`).addClass('selected');
    selectedNewStatus = activeOrder.order_status;
    
    $('#statusModal').addClass('show');
}

// Status select option inside status change modal
function selectStatus(element, status) {
    $('.status-option').removeClass('selected');
    $(element).addClass('selected');
    selectedNewStatus = status;
}

// Save new order status
function saveNewStatus() {
    if (!activeOrder || !selectedNewStatus) return;

    // Map status properly to uppercase format
    let mappedStatus = 'Baru';
    if (selectedNewStatus === 'diterima' || selectedNewStatus === 'Baru') mappedStatus = 'Baru';
    else if (selectedNewStatus === 'proses' || selectedNewStatus === 'Proses') mappedStatus = 'Proses';
    else if (selectedNewStatus === 'siap' || selectedNewStatus === 'Selesai') mappedStatus = 'Selesai';
    else if (selectedNewStatus === 'selesai' || selectedNewStatus === 'Diambil') mappedStatus = 'Diambil';

    $.ajax({
        url: `/orders/${activeOrder.id}`,
        method: 'PUT',
        data: {
            order_status: mappedStatus
        },
        success: function (res) {
            if (res.success) {
                closeModal('statusModal');
                closeModal('detailModal');
                applyFilters();
                showToast('success', 'Berhasil', 'Status order berhasil diperbarui');
            }
        },
        error: function () {
            showToast('error', 'Error', 'Gagal memperbarui status order');
        }
    });
}

// Bulk delete action
function bulkDelete() {
    if (selectedOrders.size === 0) return;
    if (!confirm(`Yakin ingin menghapus ${selectedOrders.size} order terpilih?`)) return;

    let deletedCount = 0;
    const ids = Array.from(selectedOrders);
    
    const deletePromises = ids.map(id => {
        return $.ajax({
            url: `/orders/${id}`,
            method: 'DELETE'
        });
    });

    Promise.all(deletePromises).then(() => {
        selectedOrders.clear();
        updateBulkBar();
        applyFilters();
        showToast('success', 'Berhasil', 'Order terpilih berhasil dihapus');
    }).catch(() => {
        showToast('error', 'Error', 'Beberapa order gagal dihapus');
    });
}

// Print order receipt
function printOrder() {
    if (!activeOrder) return;
    alert(`Mencetak nota belanja untuk Order #${activeOrder.order_code}`);
}
