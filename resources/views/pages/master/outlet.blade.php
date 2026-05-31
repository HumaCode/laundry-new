<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/outlet.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/outlet.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Data Outlet</h2>
            <p>Kelola dan pantau seluruh cabang outlet LaundryPro di berbagai daerah</p>
        </div>
        <div class="page-header-actions">
            <div class="view-toggle">
                <button class="view-btn active" id="viewBtnTable" onclick="switchView('table')" title="Tampilan Tabel"><i class="fas fa-list"></i></button>
                <button class="view-btn" id="viewBtnGrid"  onclick="switchView('grid')"  title="Tampilan Grid"><i class="fas fa-th-large"></i></button>
            </div>
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Outlet</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <div class="stat-card c1">
            <div class="stat-header">
                <div class="stat-icon c1"><i class="fas fa-store"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 1 Cabang</div>
            </div>
            <div class="stat-value">5</div>
            <div class="stat-label">Total Outlet</div>
            <div class="stat-footer">Tersebar di 5 kota besar</div>
        </div>
        <div class="stat-card c2">
            <div class="stat-header">
                <div class="stat-icon c2"><i class="fas fa-check-circle"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 60%</div>
            </div>
            <div class="stat-value">3</div>
            <div class="stat-label">Outlet Aktif</div>
            <div class="stat-footer">Beroperasi normal melayani pelanggan</div>
        </div>
        <div class="stat-card c3">
            <div class="stat-header">
                <div class="stat-icon c3"><i class="fas fa-tools"></i></div>
                <div class="stat-trend down"><i class="fas fa-exclamation-triangle"></i> 1 Unit</div>
            </div>
            <div class="stat-value">1</div>
            <div class="stat-label">Dalam Perawatan</div>
            <div class="stat-footer">Mesin cuci & fasilitas sedang diservis</div>
        </div>
        <div class="stat-card c4">
            <div class="stat-header">
                <div class="stat-icon c4"><i class="fas fa-users-cog"></i></div>
                <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 8.4%</div>
            </div>
            <div class="stat-value">58</div>
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-footer">Dari seluruh outlet & kurir</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama, alamat, manager, ID outlet..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Status</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Tutup">Tutup</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Lokasi Kota</label>
            <select class="filter-input" id="filterCity" onchange="applyFilters()">
                <option value="">Semua Lokasi</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Urutkan</label>
            <select class="filter-input" id="filterSort" onchange="applyFilters()">
                <option value="recent" selected>Terbaru Dibuka</option>
                <option value="name-asc">Nama A–Z</option>
                <option value="name-desc">Nama Z–A</option>
                <option value="staff-desc">Karyawan Terbanyak</option>
                <option value="revenue-desc">Omset Terbesar</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-icon"><i class="fas fa-store"></i></div>
                Daftar Outlet
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
            <table class="outlets-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Outlet <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('status')">Status <i class="fas fa-sort sort-icon" id="si-status"></i></th>
                        <th onclick="sortBy('phone')">Kontak <i class="fas fa-sort sort-icon" id="si-phone"></i></th>
                        <th onclick="sortBy('city')">Lokasi & Alamat <i class="fas fa-sort sort-icon" id="si-city"></i></th>
                        <th onclick="sortBy('manager')">Manager (PIC) <i class="fas fa-sort sort-icon" id="si-manager"></i></th>
                        <th onclick="sortBy('staffCount')">Jumlah Staff <i class="fas fa-sort sort-icon" id="si-staffCount"></i></th>
                        <th onclick="sortBy('revenue')">Omset Bulanan <i class="fas fa-sort sort-icon" id="si-revenue"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="outletTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-store-alt-slash"></i></div>
            <div class="empty-title">Tidak ada outlet ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci atau filter pencarian Anda</div>
        </div>
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="bulkCountText">0</span> dipilih</span>
            <button class="bulk-btn bulk-btn-white" onclick="bulkExport()"><i class="fas fa-file-export"></i> Export</button>
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
        <div id="outletGrid" class="outlets-grid fade-in"></div>
        <div class="empty-state" id="emptyStateGrid" style="display:none">
            <div class="empty-icon"><i class="fas fa-store-alt-slash"></i></div>
            <div class="empty-title">Tidak ada outlet ditemukan</div>
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
                <div class="drawer-header-avatar" id="d-avatar">OP</div>
                <div class="drawer-header-info">
                    <div class="drawer-header-name" id="d-name">—</div>
                    <div class="drawer-header-id" id="d-id">—</div>
                    <div style="margin-top:.35rem" id="d-status-wrap"></div>
                </div>
                <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
            </div>
            <div class="drawer-body">
                <!-- Stats -->
                <div class="drawer-profile-stats">
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalorders">—</div><div class="drawer-stat-lbl">Total Order</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-totalrevenue">—</div><div class="drawer-stat-lbl">Total Omset</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-staffcount">—</div><div class="drawer-stat-lbl">Jumlah Staff</div></div>
                </div>
                <!-- Info -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Cabang</div>
                    <div class="drawer-info-grid">
                        <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                        <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                        <div class="drawer-field"><label>Tanggal Dibuka</label><div class="val" id="d-joined">—</div></div>
                        <div class="drawer-field"><label>Kota</label><div class="val" id="d-city">—</div></div>
                        <div class="drawer-field" style="grid-column:span 2"><label>Alamat Lengkap</label><div class="val" id="d-address">—</div></div>
                        <div class="drawer-field"><label>Manager (PIC)</label><div class="val" id="d-manager">—</div></div>
                    </div>
                </div>
                <!-- Staff List -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-users-cog"></i> Daftar Karyawan</div>
                    <div id="d-recent-employees"></div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentOutlet()"><i class="fas fa-trash-alt"></i></button>
                <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
                <button class="drawer-btn drawer-btn-primary" onclick="editCurrentOutlet()"><i class="fas fa-pen"></i> Edit Outlet</button>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT MODAL -->
    <div class="modal-overlay" id="outletModal" onclick="closeModalOutside(event,'outletModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" id="modalIcon"><i class="fas fa-store"></i></div>
                <div class="modal-title"><h3 id="modalTitle">Tambah Outlet</h3><p id="modalSubtitle">Isi data outlet baru</p></div>
                <button class="modal-close" onclick="closeModal('outletModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label>Nama Outlet <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama outlet"><i class="fas fa-store icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>No. Telepon <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx atau 021-xxxxxx"><i class="fas fa-phone icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="cabang@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Kota Lokasi <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-city" type="text" placeholder="Contoh: Jakarta Selatan"><i class="fas fa-city icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Manager (PIC)</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-manager" type="text" placeholder="Nama penanggung jawab"><i class="fas fa-user-tie icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Status</label>
                        <select class="form-control" id="f-status">
                            <option value="Aktif">Aktif</option>
                            <option value="Tutup">Tutup</option>
                        </select>
                    </div>
                    <div class="form-field full">
                        <label>Alamat Lengkap</label>
                        <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap cabang outlet" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                    </div>
                    <div class="form-field full">
                        <label>Catatan Operasional</label>
                        <input class="form-control" id="f-notes" type="text" placeholder="Catatan operasional khusus (opsional)">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('outletModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" id="saveOutletBtn" onclick="saveOutlet()"><i class="fas fa-save"></i> Simpan</button>
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
