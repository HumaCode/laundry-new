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
            <div class="summary-count" id="count-all">{{ number_format($stats['total_orders'] ?? 0) }}</div>
            <div class="summary-label">Semua Order</div>
            <div class="summary-sub">Keseluruhan order</div>
        </div>
        <div class="summary-card diterima" onclick="filterByStatus('diterima', this)">
            <div class="summary-icon diterima"><i class="fas fa-inbox"></i></div>
            <div class="summary-count" id="count-diterima">{{ number_format($stats['baru_orders'] ?? 0) }}</div>
            <div class="summary-label">Diterima</div>
            <div class="summary-sub">Menunggu dikerjakan</div>
        </div>
        <div class="summary-card proses" onclick="filterByStatus('proses', this)">
            <div class="summary-icon proses"><i class="fas fa-spinner"></i></div>
            <div class="summary-count" id="count-proses">{{ number_format($stats['proses_orders'] ?? 0) }}</div>
            <div class="summary-label">Diproses</div>
            <div class="summary-sub">Sedang dikerjakan</div>
        </div>
        <div class="summary-card siap" onclick="filterByStatus('siap', this)">
            <div class="summary-icon siap"><i class="fas fa-check-circle"></i></div>
            <div class="summary-count" id="count-siap">{{ number_format($stats['selesai_orders'] ?? 0) }}</div>
            <div class="summary-label">Siap Diambil</div>
            <div class="summary-sub">Selesai dikerjakan</div>
        </div>
        <div class="summary-card selesai" onclick="filterByStatus('selesai', this)">
            <div class="summary-icon selesai"><i class="fas fa-flag-checkered"></i></div>
            <div class="summary-count" id="count-selesai">{{ number_format($stats['diambil_orders'] ?? 0) }}</div>
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
    @include('pages.master.order.partials.detail')

    @include('pages.master.order.partials.status')

    @include('pages.master.order.partials.modal')
</x-app-layout>
