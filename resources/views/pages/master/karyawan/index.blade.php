<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        @vite(['resources/css/admin/karyawan.css'])
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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
                <option value="Tidak Aktif">Tidak Aktif</option>
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
    @include('pages.master.karyawan.partials.drawer')

    @include('pages.master.karyawan.partials.modal')

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
