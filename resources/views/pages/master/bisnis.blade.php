<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/bisnis.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/bisnis.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Data Bisnis</h2>
            <p>Kelola dan pantau seluruh unit bisnis LaundryPro beserta outlet di dalamnya</p>
        </div>
        <div class="page-header-actions">
            <div class="view-toggle">
                <button class="view-btn active" id="viewBtnTable" onclick="switchView('table')" title="Tampilan Tabel"><i class="fas fa-list"></i></button>
                <button class="view-btn" id="viewBtnGrid"  onclick="switchView('grid')"  title="Tampilan Grid"><i class="fas fa-th-large"></i></button>
            </div>
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Bisnis</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <div class="stat-card c1">
            <div class="stat-header">
                <div class="stat-icon c1"><i class="fas fa-building"></i></div>
                <div class="stat-trend up" id="statTrendCities"><i class="fas fa-city"></i> {{ $stats['cities_count'] }} Kota</div>
            </div>
            <div class="stat-value" id="statTotalBusinesses">{{ $stats['total_businesses'] }}</div>
            <div class="stat-label">Total Bisnis</div>
            <div class="stat-footer">Terdaftar di {{ $stats['cities_count'] }} kota</div>
        </div>
        <div class="stat-card c2">
            <div class="stat-header">
                <div class="stat-icon c2"><i class="fas fa-check-circle"></i></div>
                <div class="stat-trend up" id="statTrendActive"><i class="fas fa-arrow-up"></i> {{ $stats['active_percentage'] }}%</div>
            </div>
            <div class="stat-value" id="statActiveBusinesses">{{ $stats['active_businesses'] }}</div>
            <div class="stat-label">Bisnis Aktif</div>
            <div class="stat-footer">Beroperasi normal saat ini</div>
        </div>
        <div class="stat-card c3">
            <div class="stat-header">
                <div class="stat-icon c3"><i class="fas fa-store"></i></div>
            </div>
            <div class="stat-value" id="statTotalOutlets">{{ $stats['total_outlets'] }}</div>
            <div class="stat-label">Total Outlet</div>
            <div class="stat-footer">Dari seluruh unit bisnis</div>
        </div>
        <div class="stat-card c4">
            <div class="stat-header">
                <div class="stat-icon c4"><i class="fas fa-user-slash"></i></div>
            </div>
            <div class="stat-value" id="statInactiveBusinesses">{{ $stats['inactive_businesses'] }}</div>
            <div class="stat-label">Bisnis Tidak Aktif</div>
            <div class="stat-footer">Sedang tidak beroperasi</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama, pemilik, kota, kode bisnis..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Status</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Urutkan</label>
            <select class="filter-input" id="filterSort" onchange="applyFilters()">
                <option value="recent" selected>Terbaru Dibuat</option>
                <option value="name-asc">Nama A–Z</option>
                <option value="name-desc">Nama Z–A</option>
                <option value="outlets-desc">Outlet Terbanyak</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-icon"><i class="fas fa-building"></i></div>
                Daftar Bisnis
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
            <table class="businesses-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Bisnis <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('status')">Status <i class="fas fa-sort sort-icon" id="si-status"></i></th>
                        <th>Pemilik</th>
                        <th>Kontak</th>
                        <th onclick="sortBy('outlets')">Jumlah Outlet <i class="fas fa-sort sort-icon" id="si-outlets"></i></th>
                        <th>Kota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="businessTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-building"></i></div>
            <div class="empty-title">Tidak ada bisnis ditemukan</div>
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
        <div id="businessGrid" class="businesses-grid fade-in"></div>
        <div class="empty-state" id="emptyStateGrid" style="display:none">
            <div class="empty-icon"><i class="fas fa-building"></i></div>
            <div class="empty-title">Tidak ada bisnis ditemukan</div>
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
                <div class="drawer-header-avatar" id="d-avatar">BS</div>
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
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-outletcount">—</div><div class="drawer-stat-lbl">Total Outlet</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-activeoutlets">—</div><div class="drawer-stat-lbl">Outlet Aktif</div></div>
                    <div class="drawer-stat"><div class="drawer-stat-val" id="d-employees">—</div><div class="drawer-stat-lbl">Total Karyawan</div></div>
                </div>
                <!-- Info -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Bisnis</div>
                    <div class="drawer-info-grid">
                        <div class="drawer-field"><label>Pemilik</label><div class="val" id="d-owner">—</div></div>
                        <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                        <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                        <div class="drawer-field"><label>Kota</label><div class="val" id="d-city">—</div></div>
                        <div class="drawer-field" style="grid-column:span 2"><label>Deskripsi</label><div class="val" id="d-description">—</div></div>
                        <div class="drawer-field" style="grid-column:span 2"><label>Alamat</label><div class="val" id="d-address">—</div></div>
                    </div>
                </div>
                <!-- Outlet List -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-store"></i> Daftar Outlet</div>
                    <div id="d-outlet-list"></div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentBusiness()"><i class="fas fa-trash-alt"></i></button>
                <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
                <button class="drawer-btn drawer-btn-primary" onclick="editCurrentBusiness()"><i class="fas fa-pen"></i> Edit Bisnis</button>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT MODAL -->
    <div class="modal-overlay" id="businessModal" onclick="closeModalOutside(event,'businessModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" id="modalIcon"><i class="fas fa-building"></i></div>
                <div class="modal-title"><h3 id="modalTitle">Tambah Bisnis</h3><p id="modalSubtitle">Isi data bisnis baru</p></div>
                <button class="modal-close" onclick="closeModal('businessModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label>Nama Bisnis <span class="req">*</span></label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-name" type="text" placeholder="Masukkan nama bisnis"><i class="fas fa-building icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Pemilik (Owner)</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-owner" type="text" placeholder="Nama pemilik bisnis"><i class="fas fa-user-tie icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>No. Telepon</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-phone" type="tel" placeholder="08xxxxxxxxxx atau 021-xxxxxx"><i class="fas fa-phone icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="bisnis@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Kota</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-city" type="text" placeholder="Contoh: Jakarta Selatan"><i class="fas fa-city icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Status</label>
                        <select class="form-control" id="f-status">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-field full">
                        <label>Deskripsi Singkat</label>
                        <div class="input-icon-wrap"><textarea class="form-control" id="f-description" rows="2" placeholder="Deskripsi singkat tentang bisnis ini" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-align-left icon" style="top:1rem;transform:none"></i></div>
                    </div>
                    <div class="form-field full">
                        <label>Alamat Lengkap</label>
                        <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="2" placeholder="Alamat kantor pusat bisnis" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('businessModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" id="saveBusinessBtn" onclick="saveBusiness()"><i class="fas fa-save"></i> Simpan</button>
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
