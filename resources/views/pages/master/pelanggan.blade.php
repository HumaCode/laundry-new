<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/pelanggan.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/pelanggan.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Data Pelanggan</h2>
            <p>Kelola dan pantau seluruh pelanggan LaundryPro dari semua outlet</p>
        </div>
        <div class="page-header-actions">
            <div class="view-toggle">
                <button class="view-btn active" id="viewBtnTable" onclick="switchView('table')" title="Tampilan Tabel"><i class="fas fa-list"></i></button>
                <button class="view-btn" id="viewBtnGrid"  onclick="switchView('grid')"  title="Tampilan Grid"><i class="fas fa-th-large"></i></button>
            </div>
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Pelanggan</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <div class="stat-card c1">
            <div class="stat-header">
                <div class="stat-icon c1"><i class="fas fa-users"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 5.7%</div>
            </div>
            <div class="stat-value">3,891</div>
            <div class="stat-label">Total Pelanggan</div>
            <div class="stat-footer">284 pelanggan baru bulan ini</div>
        </div>
        <div class="stat-card c2">
            <div class="stat-header">
                <div class="stat-icon c2"><i class="fas fa-user-check"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 3.2%</div>
            </div>
            <div class="stat-value">2,140</div>
            <div class="stat-label">Pelanggan Aktif</div>
            <div class="stat-footer">Order dalam 30 hari terakhir</div>
        </div>
        <div class="stat-card c3">
            <div class="stat-header">
                <div class="stat-icon c3"><i class="fas fa-crown"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 12%</div>
            </div>
            <div class="stat-value">428</div>
            <div class="stat-label">Pelanggan VIP</div>
            <div class="stat-footer">Kontribusi 45% pendapatan</div>
        </div>
        <div class="stat-card c4">
            <div class="stat-header">
                <div class="stat-icon c4"><i class="fas fa-user-plus"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 8.4%</div>
            </div>
            <div class="stat-value">284</div>
            <div class="stat-label">Pelanggan Baru</div>
            <div class="stat-footer">Bulan Desember 2024</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama, telepon, email, ID pelanggan..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Tier</label>
            <select class="filter-input" id="filterTier" onchange="applyFilters()">
                <option value="">Semua Tier</option>
                <option value="VIP">VIP</option>
                <option value="Premium">Premium</option>
                <option value="Reguler">Reguler</option>
                <option value="Baru">Baru</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Outlet Favorit</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                <option>Outlet Pusat</option>
                <option>Outlet Bandung</option>
                <option>Outlet Surabaya</option>
                <option>Outlet Yogyakarta</option>
                <option>Outlet Semarang</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Urutkan</label>
            <select class="filter-input" id="filterSort" onchange="applyFilters()">
                <option value="name-asc">Nama A–Z</option>
                <option value="name-desc">Nama Z–A</option>
                <option value="order-desc">Order Terbanyak</option>
                <option value="total-desc">Pengeluaran Terbesar</option>
                <option value="recent">Terbaru Bergabung</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-icon"><i class="fas fa-users"></i></div>
                Daftar Pelanggan
            </div>
            <div class="table-meta">
                <span>Menampilkan <strong id="showCount">10</strong> dari <strong id="totalCount">0</strong></span>
                <select class="per-page-select" id="perPageSelect" onchange="changePerPage(this.value)">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="customers-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Pelanggan <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('tier')">Tier <i class="fas fa-sort sort-icon" id="si-tier"></i></th>
                        <th onclick="sortBy('phone')">Kontak <i class="fas fa-sort sort-icon" id="si-phone"></i></th>
                        <th onclick="sortBy('outlet')">Outlet Favorit <i class="fas fa-sort sort-icon" id="si-outlet"></i></th>
                        <th onclick="sortBy('orders')">Order <i class="fas fa-sort sort-icon" id="si-orders"></i></th>
                        <th onclick="sortBy('total')">Total Belanja <i class="fas fa-sort sort-icon" id="si-total"></i></th>
                        <th onclick="sortBy('rating')">Rating <i class="fas fa-sort sort-icon" id="si-rating"></i></th>
                        <th onclick="sortBy('lastOrder')">Order Terakhir <i class="fas fa-sort sort-icon" id="si-lastOrder"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="custTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-users"></i></div>
            <div class="empty-title">Tidak ada pelanggan ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci atau filter pencarian Anda</div>
        </div>
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="bulkCountText">0</span> dipilih</span>
            <button class="bulk-btn bulk-btn-white" onclick="bulkExport()"><i class="fas fa-file-export"></i> Export</button>
            <button class="bulk-btn bulk-btn-white" onclick="bulkMessage()"><i class="fas fa-sms"></i> Kirim Pesan</button>
            <button class="bulk-btn bulk-btn-danger" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Hapus</button>
            <button class="bulk-close" onclick="clearSelection()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pagination-bar" id="paginationBar">
            <div class="pagination-info">Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div id="gridView" style="display:none">
        <div id="custGrid" class="customers-grid fade-in"></div>
        <div class="empty-state" id="emptyStateGrid" style="display:none">
            <div class="empty-icon"><i class="fas fa-users"></i></div>
            <div class="empty-title">Tidak ada pelanggan ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci atau filter pencarian Anda</div>
        </div>
        <div class="pagination-bar" style="background:white;border-radius:var(--radius);margin-top:1.25rem;border:1px solid var(--border)" id="gridPagBar">
            <div class="pagination-info">Halaman <strong id="gridCurPage">1</strong> dari <strong id="gridTotalPages">1</strong></div>
            <div class="pagination-controls" id="gridPaginationControls"></div>
        </div>
    </div>

    <!-- DETAIL DRAWER -->
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawerOutside(event)">
        <div class="drawer" id="drawer">
            <div class="drawer-header">
                <div class="drawer-header-avatar" id="d-avatar">BK</div>
                <div class="drawer-header-info">
                    <div class="drawer-header-name" id="d-name">—</div>
                    <div class="drawer-header-id" id="d-id">—</div>
                    <div style="margin-top:.35rem" id="d-tier-wrap"></div>
                </div>
                <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
            </div>
            <div class="drawer-body">
                <!-- Stats -->
                <div class="drawer-profile-stats">
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalorders">—</div><div class="drawer-stat-lbl">Total Order</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalspend">—</div><div class="drawer-stat-lbl">Total Belanja</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-rating">—</div><div class="drawer-stat-lbl">Avg Rating</div></div>
                </div>
                <!-- Info -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Pribadi</div>
                    <div class="drawer-info-grid">
                        <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                        <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                        <div class="drawer-field"><label>Bergabung</label><div class="val" id="d-joined">—</div></div>
                        <div class="drawer-field"><label>Outlet Favorit</label><div class="val" id="d-outlet">—</div></div>
                        <div class="drawer-field" style="grid-column:span 2"><label>Alamat</label><div class="val" id="d-address">—</div></div>
                        <div class="drawer-field"><label>Layanan Favorit</label><div class="val" id="d-favservice">—</div></div>
                        <div class="drawer-field"><label>Avg Order</label><div class="val" id="d-avgorder">—</div></div>
                    </div>
                </div>
                <!-- Recent orders -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-receipt"></i> Order Terbaru</div>
                    <div id="d-recent-orders"></div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentCustomer()"><i class="fas fa-trash-alt"></i></button>
                <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
                <button class="drawer-btn drawer-btn-primary" onclick="editCurrentCustomer()"><i class="fas fa-pen"></i> Edit Pelanggan</button>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT MODAL -->
    <div class="modal-overlay" id="custModal" onclick="closeModalOutside(event,'custModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" id="modalIcon"><i class="fas fa-user-plus"></i></div>
                <div class="modal-title"><h3 id="modalTitle">Tambah Pelanggan</h3><p id="modalSubtitle">Isi data pelanggan baru</p></div>
                <button class="modal-close" onclick="closeModal('custModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label>Nama Lengkap <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama lengkap"><i class="fas fa-user icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>No. Telepon <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx"><i class="fas fa-phone icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="email@contoh.com"><i class="fas fa-envelope icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Tanggal Lahir</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-dob" type="date"><i class="fas fa-birthday-cake icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" id="f-gender">
                            <option value="">-- Pilih --</option>
                            <option>Laki-laki</option>
                            <option>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Outlet Favorit</label>
                        <select class="form-control" id="f-outlet">
                            <option value="">-- Pilih --</option>
                            <option>Outlet Pusat</option>
                            <option>Outlet Bandung</option>
                            <option>Outlet Surabaya</option>
                            <option>Outlet Yogyakarta</option>
                            <option>Outlet Semarang</option>
                        </select>
                    </div>
                    <div class="form-field full">
                        <label>Alamat</label>
                        <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap pelanggan" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Tier</label>
                        <select class="form-control" id="f-tier">
                            <option value="Baru">Baru</option>
                            <option value="Reguler">Reguler</option>
                            <option value="Premium">Premium</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Catatan</label>
                        <input class="form-control" id="f-notes" type="text" placeholder="Catatan khusus (opsional)">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('custModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" onclick="saveCustomer()"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- Toast Wrap -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Float Button -->
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-arrow-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>
</x-app-layout>
