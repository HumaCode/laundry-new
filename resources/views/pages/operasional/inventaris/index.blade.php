<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @vite(['resources/css/admin/inventaris.css'])
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        @vite(['resources/js/admin/inventaris.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Manajemen Inventaris</h2>
            <p>Kelola stok bahan baku, perlengkapan, dan peralatan di seluruh outlet LaundryPro</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn-page btn-page-warning" onclick="openRestockAllModal()"><i class="fas fa-redo-alt"></i> Restock Otomatis</button>
            <button class="btn-page btn-page-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Barang</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <x-stat-card 
            class="active-filter"
            theme="c1"
            icon="boxes"
            value="0"
            valueId="sc-all"
            title="Total Jenis Barang"
            subClass="ok"
            subId="sc-all-sub"
            footerText="—"
            delayClass="d1"
            onclick="filterByStat('all',this)"
        />
        <x-stat-card 
            theme="c2"
            icon="check-circle"
            value="0"
            valueId="sc-cukup"
            title="Stok Aman"
            subClass="ok"
            footerText="Di atas batas minimum"
            delayClass="d2"
            onclick="filterByStat('cukup',this)"
        />
        <x-stat-card 
            theme="c3"
            icon="exclamation-triangle"
            value="0"
            valueId="sc-rendah"
            title="Stok Rendah"
            subClass="warn"
            footerText="Segera restock"
            delayClass="d3"
            onclick="filterByStat('rendah',this)"
        />
        <x-stat-card 
            theme="c4"
            icon="fire"
            value="0"
            valueId="sc-kritis"
            title="Stok Kritis / Habis"
            subClass="warn"
            footerText="Butuh restock segera!"
            delayClass="d4"
            onclick="filterByStat('kritis',this)"
        />
        <x-stat-card 
            theme="c5"
            icon="dollar-sign"
            value="0"
            valueId="sc-nilai"
            title="Nilai Inventaris"
            subClass="ok"
            footerText="Total keseluruhan outlet"
            delayClass="d5"
        />
    </div>

    <!-- Dashboard Grid: Chart + Low Stock -->
    <div class="dash-grid fade-in d3">
        <!-- Stock Usage Bar Chart -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon" style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.12));color:var(--primary)"><i class="fas fa-chart-bar"></i></div>
                    Penggunaan Stok per Kategori
                </div>
                <span style="font-size:.78rem;color:var(--gray);position:relative;z-index:1">Bulan Desember 2024</span>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="stockChart"></canvas></div>
            </div>
        </div>

        <!-- Low / Critical Stock Alert -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon" style="background:rgba(239,68,68,.1);color:var(--danger)"><i class="fas fa-bell"></i></div>
                    Peringatan Stok
                </div>
                <span style="font-size:.78rem;font-weight:600;color:var(--danger);position:relative;z-index:1" id="alertCount">0 item</span>
            </div>
            <div class="card-body" style="max-height:360px;overflow-y:auto" id="alertList"></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in d4">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari nama barang, kode, merek..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Kategori</label>
            <select class="filter-input" id="filterCat" onchange="applyFilters()">
                <option value="">Semua Kategori</option>
                <option>Deterjen & Kimia</option>
                <option>Pewangi & Softener</option>
                <option>Plastik & Kemasan</option>
                <option>Peralatan Cuci</option>
                <option>Peralatan Setrika</option>
                <option>Kebersihan Outlet</option>
                <option>ATK & Administrasi</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:120px">
            <label class="filter-label">Status Stok</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="lebih">Lebih</option>
                <option value="cukup">Cukup</option>
                <option value="rendah">Rendah</option>
                <option value="kritis">Kritis</option>
                <option value="habis">Habis</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- Table -->
    <div class="table-card fade-in d5">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-title-icon"><i class="fas fa-boxes"></i></div>
                Daftar Inventaris
            </div>
            <div class="table-meta">
                <span>Tampil <strong id="showCount">10</strong> dari <strong id="totalCount">0</strong></span>
                <select class="per-page-select" id="perPageSel" onchange="changePerPage(this.value)">
                    <option value="10">10/hal</option><option value="25">25/hal</option><option value="50">50/hal</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th class="cb-cell"><input type="checkbox" class="custom-cb" id="checkAll" onchange="toggleAllCheck()"></th>
                        <th onclick="sortBy('name')">Barang <i class="fas fa-sort sort-icon active" id="si-name"></i></th>
                        <th onclick="sortBy('category')">Kategori <i class="fas fa-sort sort-icon" id="si-category"></i></th>
                        <th onclick="sortBy('outlet')">Outlet <i class="fas fa-sort sort-icon" id="si-outlet"></i></th>
                        <th onclick="sortBy('stock')">Stok <i class="fas fa-sort sort-icon" id="si-stock"></i></th>
                        <th onclick="sortBy('stockStatus')">Status <i class="fas fa-sort sort-icon" id="si-stockStatus"></i></th>
                        <th onclick="sortBy('price')">Harga Satuan <i class="fas fa-sort sort-icon" id="si-price"></i></th>
                        <th onclick="sortBy('value')">Nilai Stok <i class="fas fa-sort sort-icon" id="si-value"></i></th>
                        <th onclick="sortBy('lastRestock')">Restock Terakhir <i class="fas fa-sort sort-icon" id="si-lastRestock"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="invTableBody"></tbody>
            </table>
        </div>
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-boxes"></i></div>
            <div style="font-size:1.125rem;font-weight:700;color:var(--dark);margin-bottom:.5rem">Tidak ada barang ditemukan</div>
            <div style="font-size:.875rem;color:var(--gray)">Coba ubah kata kunci atau filter pencarian</div>
        </div>
        <div class="bulk-bar" id="bulkBar">
            <span class="bulk-count"><i class="fas fa-check-square"></i> <span id="bulkCountText">0</span> dipilih</span>
            <button class="bulk-btn bulk-btn-white" onclick="bulkRestock()"><i class="fas fa-redo-alt"></i> Restock Semua</button>
            <button class="bulk-btn bulk-btn-white" onclick="bulkExport()"><i class="fas fa-file-export"></i> Export</button>
            <button class="bulk-btn bulk-btn-danger" onclick="bulkDelete()"><i class="fas fa-trash-alt"></i> Hapus</button>
            <button class="bulk-close" onclick="clearSelection()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pagination-bar">
            <div class="pagination-info">Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    @include('pages.operasional.inventaris.partials.modal')

    <!-- Toast + Float btn -->
    <div class="toast-wrap" id="toastWrap"></div>
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="window.scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-arrow-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>
</x-app-layout>
