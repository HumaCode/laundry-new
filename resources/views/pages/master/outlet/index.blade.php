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
        <x-stat-card 
            theme="c1"
            icon="store"
            value="{{ $stats['total_outlets'] }}"
            valueId="statTotalOutlets"
            title="Total Outlet"
            footerText="Tersebar di {{ $stats['cities_count'] }} kota besar"
            subId="statFooterCities"
        >
            <x-slot:trendSlot>
                <div class="stat-trend up" id="statTrendCities"><i class="fas fa-city"></i> {{ $stats['cities_count'] }} Kota</div>
            </x-slot:trendSlot>
        </x-stat-card>
        <x-stat-card 
            theme="c2"
            icon="check-circle"
            value="{{ $stats['active_outlets'] }}"
            valueId="statActiveOutlets"
            title="Outlet Aktif"
            footerText="Beroperasi normal melayani pelanggan"
        >
            <x-slot:trendSlot>
                <div class="stat-trend up" id="statTrendActive"><i class="fas fa-arrow-up"></i> {{ $stats['active_percentage'] }}%</div>
            </x-slot:trendSlot>
        </x-stat-card>
        <x-stat-card 
            theme="c3"
            icon="tools"
            value="{{ $stats['maintenance_outlets'] }}"
            valueId="statMaintenanceOutlets"
            title="Dalam Perawatan"
            footerText="Mesin cuci & fasilitas sedang diservis"
        >
            <x-slot:trendSlot>
                <div class="stat-trend down" id="statTrendMaintenance"><i class="fas fa-exclamation-triangle"></i> {{ $stats['maintenance_outlets'] }} Unit</div>
            </x-slot:trendSlot>
        </x-stat-card>
        <x-stat-card 
            theme="c4"
            icon="users-cog"
            value="{{ $stats['total_employees'] }}"
            valueId="statTotalEmployees"
            title="Total Karyawan"
            footerText="Dari seluruh outlet & kurir"
        >
            <x-slot:trendSlot>
                <div class="stat-trend up" id="statTrendEmployees"><i class="fas fa-users"></i> {{ $stats['total_outlets'] > 0 ? 'Aktif' : '0%' }}</div>
            </x-slot:trendSlot>
        </x-stat-card>
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
    @include('pages.master.outlet.partials.drawer')

    @include('pages.master.outlet.partials.modal')

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
