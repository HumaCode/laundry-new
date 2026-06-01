<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/order.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/order.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Semua Order</h2>
            <p>Kelola dan pantau seluruh transaksi laundry dari semua outlet</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="applyFilters()"><i class="fas fa-sync-alt"></i> Refresh</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Order Baru</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <div class="summary-card all active-filter" onclick="filterByStatus('all', this)">
            <div class="summary-icon all"><i class="fas fa-layer-group"></i></div>
            <div class="summary-count" id="count-all">0</div>
            <div class="summary-label">Semua Order</div>
            <div class="summary-sub">Keseluruhan order</div>
        </div>
        <div class="summary-card diterima" onclick="filterByStatus('diterima', this)">
            <div class="summary-icon diterima"><i class="fas fa-inbox"></i></div>
            <div class="summary-count" id="count-diterima">0</div>
            <div class="summary-label">Diterima</div>
            <div class="summary-sub">Menunggu dikerjakan</div>
        </div>
        <div class="summary-card proses" onclick="filterByStatus('proses', this)">
            <div class="summary-icon proses"><i class="fas fa-spinner"></i></div>
            <div class="summary-count" id="count-proses">0</div>
            <div class="summary-label">Diproses</div>
            <div class="summary-sub">Sedang dikerjakan</div>
        </div>
        <div class="summary-card siap" onclick="filterByStatus('siap', this)">
            <div class="summary-icon siap"><i class="fas fa-check-circle"></i></div>
            <div class="summary-count" id="count-siap">0</div>
            <div class="summary-label">Siap Diambil</div>
            <div class="summary-sub">Selesai dikerjakan</div>
        </div>
        <div class="summary-card selesai" onclick="filterByStatus('selesai', this)">
            <div class="summary-icon selesai"><i class="fas fa-flag-checkered"></i></div>
            <div class="summary-count" id="count-selesai">0</div>
            <div class="summary-label">Selesai</div>
            <div class="summary-sub">Sudah diambil pelanggan</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari No. Order, nama pelanggan..." oninput="applyFilters()">
        </div>
        <div class="filter-group">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Pembayaran</label>
            <select class="filter-input" id="filterBayar" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="Lunas">Lunas</option>
                <option value="Belum">Belum Bayar</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Dari Tanggal</label>
            <input class="filter-input" type="date" id="filterDateFrom" onchange="applyFilters()">
        </div>
        <div class="filter-group">
            <label class="filter-label">Sampai Tanggal</label>
            <input class="filter-input" type="date" id="filterDateTo" onchange="applyFilters()">
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- Table Card -->
    <div class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-title-icon"><i class="fas fa-receipt"></i></div>
                Daftar Order
            </div>
            <div class="table-meta">
                <span>Menampilkan <strong class="table-meta-count" id="showCount">0</strong> dari <strong class="table-meta-count" id="totalCount">0</strong> order</span>
                <select class="per-page-select" id="perPageSelect" onchange="changePerPage(this.value)">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                </select>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th>No. Order</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody"></tbody>
            </table>
        </div>

        <!-- Empty state -->
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-search"></i></div>
            <div class="empty-title">Tidak ada order ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci atau filter pencarian Anda</div>
        </div>

        <!-- Bulk Action Bar -->
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="bulkCountText">0</span> order dipilih</span>
            <button class="bulk-btn bulk-btn-danger" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Hapus</button>
            <button class="bulk-close" onclick="clearSelection()"><i class="fas fa-times"></i></button>
        </div>

        <!-- Pagination -->
        <div class="pagination-bar" id="paginationBar">
            <div class="pagination-info">Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event)">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="fas fa-receipt"></i></div>
                <div class="modal-title">
                    <h3 id="modalOrderId">Detail Order</h3>
                    <p id="modalOrderDate">—</p>
                </div>
                <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <!-- Customer Info -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-user"></i> Informasi Pelanggan</div>
                    <div class="modal-grid">
                        <div class="modal-field"><label>Nama Pelanggan</label><div class="val" id="m-customer">—</div></div>
                        <div class="modal-field"><label>No. Telepon</label><div class="val" id="m-phone">—</div></div>
                        <div class="modal-field"><label>Outlet</label><div class="val" id="m-outlet">—</div></div>
                        <div class="modal-field"><label>Kasir</label><div class="val" id="m-kasir">—</div></div>
                    </div>
                </div>
                <!-- Service Info -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-concierge-bell"></i> Detail Layanan</div>
                    <div class="modal-grid">
                        <div class="modal-field"><label>Layanan</label><div class="val" id="m-service">—</div></div>
                        <div class="modal-field"><label>Tipe</label><div class="val" id="m-type">—</div></div>
                        <div class="modal-field"><label>Jumlah / Berat</label><div class="val" id="m-qty">—</div></div>
                        <div class="modal-field"><label>Harga Satuan</label><div class="val" id="m-price">—</div></div>
                        <div class="modal-field"><label>Total</label><div class="val" id="m-total" style="font-size:1.1rem;color:var(--primary)">—</div></div>
                    </div>
                </div>
                <!-- Payment Info -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-money-bill-wave"></i> Pembayaran</div>
                    <div class="modal-grid">
                        <div class="modal-field"><label>Status Bayar</label><div class="val" id="m-paystatus">—</div></div>
                        <div class="modal-field"><label>Metode</label><div class="val" id="m-paymethod">—</div></div>
                    </div>
                </div>
                <!-- Progress Timeline -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-stream"></i> Progress Order</div>
                    <div class="timeline" id="m-timeline"></div>
                </div>
                <!-- Notes -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-sticky-note"></i> Catatan</div>
                    <div style="padding:0.875rem;background:#F9FAFB;border-radius:10px;font-size:0.875rem;color:var(--gray)" id="m-notes">Tidak ada catatan.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('detailModal')"><i class="fas fa-times"></i> Tutup</button>
                <button class="modal-btn modal-btn-success" onclick="openStatusModal()"><i class="fas fa-exchange-alt"></i> Update Status</button>
                <button class="modal-btn modal-btn-primary" onclick="printOrder()"><i class="fas fa-print"></i> Cetak Nota</button>
            </div>
        </div>
    </div>

    <!-- STATUS MODAL -->
    <div class="modal-overlay" id="statusModal" onclick="closeModalOutside(event)">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-exchange-alt"></i></div>
                <div class="modal-title">
                    <h3>Update Status Order</h3>
                    <p id="statusModalOrderId">—</p>
                </div>
                <button class="modal-close" onclick="closeModal('statusModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.875rem;color:var(--gray);margin-bottom:1rem">Pilih status baru untuk order ini:</p>
                <div class="status-options">
                    <div class="status-option" onclick="selectStatus(this, 'diterima')" data-status="Baru">
                        <div class="status-option-icon"><i class="fas fa-inbox"></i></div>
                        <div class="status-option-label">Diterima</div>
                    </div>
                    <div class="status-option" onclick="selectStatus(this, 'proses')" data-status="Proses">
                        <div class="status-option-icon"><i class="fas fa-spinner"></i></div>
                        <div class="status-option-label">Diproses</div>
                    </div>
                    <div class="status-option" onclick="selectStatus(this, 'siap')" data-status="Selesai">
                        <div class="status-option-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="status-option-label">Siap Diambil</div>
                    </div>
                    <div class="status-option" onclick="selectStatus(this, 'selesai')" data-status="Diambil">
                        <div class="status-option-icon"><i class="fas fa-flag-checkered"></i></div>
                        <div class="status-option-label">Selesai</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('statusModal')">Batal</button>
                <button class="modal-btn modal-btn-success" onclick="saveNewStatus()"><i class="fas fa-check"></i> Konfirmasi</button>
            </div>
        </div>
    </div>

    <!-- CREATE/EDIT ORDER MODAL -->
    <div class="modal-overlay" id="custModal" onclick="closeModalOutside(event, 'custModal')">
        <div class="modal-box" style="max-width:540px">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="fas fa-plus"></i></div>
                <div class="modal-title">
                    <h3 id="modalBoxTitle">Buat Order Baru</h3>
                    <p>Formulir pembuatan dan pembaruan order transaksi</p>
                </div>
                <button class="modal-close" onclick="closeModal('custModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="orderForm">
                <input type="hidden" id="orderId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="orderCustomer">Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-control" id="orderCustomer" name="customer_id" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="orderOutlet">Outlet <span class="text-danger">*</span></label>
                        <select class="form-control" id="orderOutlet" name="outlet_id" required>
                            <option value="">-- Pilih Outlet --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="orderServiceType">Layanan <span class="text-danger">*</span></label>
                        <select class="form-control" id="orderServiceType" name="service_type" required>
                            <option value="Cuci Setrika">Cuci Setrika (Rp 8.000 / kg)</option>
                            <option value="Cuci Kering">Cuci Kering (Rp 7.000 / kg)</option>
                            <option value="Setrika Saja">Setrika Saja (Rp 5.000 / kg)</option>
                            <option value="Cuci Bed Cover">Cuci Bed Cover (Rp 25.000 / kg)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="modal-grid">
                        <div class="form-group">
                            <label for="orderWeight">Berat / Qty <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="orderWeight" name="weight" value="1" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="orderPricePerUnit">Harga Satuan <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" id="orderPricePerUnit" name="price_per_unit" value="8000" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-grid">
                        <div class="form-group">
                            <label for="orderStatusSelect">Status Order</label>
                            <select class="form-control" id="orderStatusSelect" name="order_status">
                                <option value="Baru">Baru</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Diambil">Diambil</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="orderPaymentStatusSelect">Pembayaran</label>
                            <select class="form-control" id="orderPaymentStatusSelect" name="payment_status">
                                <option value="Belum">Belum Bayar</option>
                                <option value="Lunas">Lunas</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="orderPaymentMethod">Metode Pembayaran</label>
                        <select class="form-control" id="orderPaymentMethod" name="payment_method">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="orderNotes">Catatan Tambahan</label>
                        <textarea class="form-control" id="orderNotes" name="notes" rows="2" placeholder="Catatan khusus dari pelanggan..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-outline" onclick="closeModal('custModal')">Batal</button>
                    <button type="submit" class="modal-btn modal-btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
