<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @vite(['resources/css/admin/karyawan.css'])
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        @vite(['resources/js/admin/karyawan.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Data Karyawan</h2>
            <p>Kelola dan pantau tugas karyawan LaundryPro di seluruh cabang outlet</p>
        </div>
        <div class="page-header-actions">
            <div class="view-toggle">
                <button class="view-btn active" id="viewBtnTable" onclick="switchView('table')" title="Tampilan Tabel"><i class="fas fa-list"></i></button>
                <button class="view-btn" id="viewBtnGrid"  onclick="switchView('grid')"  title="Tampilan Grid"><i class="fas fa-th-large"></i></button>
            </div>
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Karyawan</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <div class="stat-card c1">
            <div class="stat-header">
                <div class="stat-icon c1"><i class="fas fa-user-tie"></i></div>
                <div class="stat-trend up" id="statTrendActive"><i class="fas fa-arrow-up"></i> {{ $stats['active_percentage'] }}%</div>
            </div>
            <div class="stat-value" id="statTotalEmployees">{{ $stats['total_employees'] }}</div>
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-footer">Jumlah terdaftar di sistem</div>
        </div>
        <div class="stat-card c2">
            <div class="stat-header">
                <div class="stat-icon c2"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="statActiveEmployees">{{ $stats['active_employees'] }}</div>
            <div class="stat-label">Karyawan Aktif</div>
            <div class="stat-footer">Sedang bertugas melayani cabang</div>
        </div>
        <div class="stat-card c3">
            <div class="stat-header">
                <div class="stat-icon c3"><i class="fas fa-briefcase"></i></div>
            </div>
            <div class="stat-value" id="statRolesCount">{{ $stats['roles_count'] }}</div>
            <div class="stat-label">Variasi Peran</div>
            <div class="stat-footer">Jenis spesialisasi keahlian</div>
        </div>
        <div class="stat-card c4">
            <div class="stat-header">
                <div class="stat-icon c4"><i class="fas fa-user-slash"></i></div>
            </div>
            <div class="stat-value" id="statInactiveEmployees">{{ $stats['inactive_employees'] }}</div>
            <div class="stat-label">Karyawan Nonaktif</div>
            <div class="stat-footer">Karyawan dengan status cuti/tidak aktif</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama, peran, telepon, ID karyawan..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Status</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Tutup">Tutup</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:145px">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:135px">
            <label class="filter-label">Peran</label>
            <select class="filter-input" id="filterRole" onchange="applyFilters()">
                <option value="">Semua Peran</option>
                @foreach($stats['roles'] as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Urutkan</label>
            <select class="filter-input" id="filterSort" onchange="applyFilters()">
                <option value="recent" selected>Terbaru Terdaftar</option>
                <option value="name-asc">Nama A–Z</option>
                <option value="name-desc">Nama Z–A</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-icon"><i class="fas fa-user-check"></i></div>
                Daftar Karyawan
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
            <table class="employees-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Karyawan <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('status')">Status <i class="fas fa-sort sort-icon" id="si-status"></i></th>
                        <th onclick="sortBy('phone')">Kontak <i class="fas fa-sort sort-icon" id="si-phone"></i></th>
                        <th>Outlet Cabang</th>
                        <th>Peran (Role)</th>
                        <th onclick="sortBy('joined')">Mulai Bekerja <i class="fas fa-sort sort-icon" id="si-joined"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-user-slash"></i></div>
            <div class="empty-title">Tidak ada karyawan ditemukan</div>
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
        <div id="employeeGrid" class="employees-grid fade-in"></div>
        <div class="empty-state" id="emptyStateGrid" style="display:none">
            <div class="empty-icon"><i class="fas fa-user-slash"></i></div>
            <div class="empty-title">Tidak ada karyawan ditemukan</div>
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
                <div class="drawer-header-avatar" id="d-avatar">EM</div>
                <div class="drawer-header-info">
                    <div class="drawer-header-name" id="d-name">—</div>
                    <div class="drawer-header-id" id="d-id">—</div>
                    <div style="margin-top:.35rem" id="d-status-wrap"></div>
                </div>
                <button class="drawer-close" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
            </div>
            <div class="drawer-body">
                <!-- Info -->
                <div class="drawer-section">
                    <div class="drawer-section-title"><i class="fas fa-info-circle"></i> Informasi Detail Karyawan</div>
                    <div class="drawer-info-grid">
                        <div class="drawer-field"><label>No. Telepon</label><div class="val" id="d-phone">—</div></div>
                        <div class="drawer-field"><label>Email</label><div class="val" id="d-email">—</div></div>
                        <div class="drawer-field"><label>Tanggal Masuk</label><div class="val" id="d-joined">—</div></div>
                        <div class="drawer-field"><label>Outlet Cabang</label><div class="val" id="d-outlet">—</div></div>
                        <div class="drawer-field"><label>Peran (Role)</label><div class="val" id="d-role">—</div></div>
                        <div class="drawer-field" style="grid-column:span 2"><label>Alamat Lengkap</label><div class="val" id="d-address">—</div></div>
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="drawer-btn drawer-btn-danger" onclick="deleteCurrentEmployee()"><i class="fas fa-trash-alt"></i></button>
                <button class="drawer-btn drawer-btn-outline" onclick="closeDrawer()"><i class="fas fa-times"></i> Tutup</button>
                <button class="drawer-btn drawer-btn-primary" onclick="editCurrentEmployee()"><i class="fas fa-pen"></i> Edit Karyawan</button>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT MODAL -->
    <div class="modal-overlay" id="employeeModal" onclick="closeModalOutside(event,'employeeModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" id="modalIcon"><i class="fas fa-user-check"></i></div>
                <div class="modal-title"><h3 id="modalTitle">Tambah Karyawan</h3><p id="modalSubtitle">Isi data karyawan baru</p></div>
                <button class="modal-close" onclick="closeModal('employeeModal')"><i class="fas fa-times"></i></button>
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
                        <div class="input-icon-wrap"><input class="form-control" id="f-email" type="email" placeholder="karyawan@laundrypro.com"><i class="fas fa-envelope icon"></i></div>
                    </div>
                    <div class="form-field">
                        <label>Outlet Penempatan <span class="req">*</span></label>
                        <select class="form-control" id="f-outlet">
                            <option value="">Pilih Cabang Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Peran (Role) <span class="req">*</span></label>
                        <select class="form-control" id="f-role">
                            <option value="">Pilih atau Ketik Peran</option>
                            <option value="Kepala Outlet">Kepala Outlet</option>
                            <option value="Kasir">Kasir</option>
                            <option value="Kurir">Kurir</option>
                            <option value="Pencuci">Pencuci</option>
                            <option value="Penyetrika">Penyetrika</option>
                            <option value="Staff Admin">Staff Admin</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Status</label>
                        <select class="form-control" id="f-status">
                            <option value="Aktif">Aktif</option>
                            <option value="Tutup">Tutup</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Tanggal Masuk Bekerja</label>
                        <div class="input-icon-wrap"><input class="form-control" id="f-joined_at" type="date"><i class="fas fa-calendar-alt icon" style="pointer-events:none"></i></div>
                    </div>
                    <div class="form-field full">
                        <label>Alamat Lengkap</label>
                        <div class="input-icon-wrap"><textarea class="form-control" id="f-address" rows="3" placeholder="Alamat lengkap tempat tinggal" style="padding-left:2.75rem;resize:vertical"></textarea><i class="fas fa-map-marker-alt icon" style="top:1rem;transform:none"></i></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('employeeModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" id="saveEmployeeBtn" onclick="saveEmployee()"><i class="fas fa-save"></i> Simpan</button>
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
