<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @vite(['resources/css/admin/antarjemput.css'])
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            window.driversData = @json($drivers);
            window.outletsData = @json($outlets);
        </script>
        @vite(['resources/js/admin/antarjemput.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Antar & Jemput</h2>
            <p>Monitor dan kelola semua permintaan antar jemput cucian secara real-time</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="showToast('info','Riwayat','Membuka riwayat trip')"><i class="fas fa-history"></i> Riwayat</button>
            <button class="btn-page btn-page-outline" onclick="showToast('info','Laporan','Membuka laporan trip')"><i class="fas fa-chart-bar"></i> Laporan</button>
            <button class="btn-page btn-page-success" onclick="openAddModal()"><i class="fas fa-plus"></i> Buat Trip Baru</button>
        </div>
    </div>

    <!-- Stats Filter Cards -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="stat-card c1 fade-in d1 active-stat h-100" onclick="filterByStatus('all', this)">
                <div class="stat-hdr"><div class="stat-icon c1"><i class="fas fa-layer-group"></i></div></div>
                <div class="stat-value" id="sc-all">0</div>
                <div class="stat-label">Semua Trip</div>
                <div class="stat-sub">Hari ini</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card c2 fade-in d2 h-100" onclick="filterByStatus('menunggu', this)">
                <div class="stat-hdr"><div class="stat-icon c2"><i class="fas fa-clock"></i></div></div>
                <div class="stat-value" id="sc-menunggu">0</div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-sub">Belum dijadwalkan</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card c3 fade-in d3 h-100" onclick="filterByStatus('jemput', this)">
                <div class="stat-hdr"><div class="stat-icon c3"><i class="fas fa-motorcycle"></i></div></div>
                <div class="stat-value" id="sc-jemput">0</div>
                <div class="stat-label">Sedang Jemput</div>
                <div class="stat-sub">Dalam perjalanan</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card c4 fade-in d4 h-100" onclick="filterByStatus('proses', this)">
                <div class="stat-hdr"><div class="stat-icon c4"><i class="fas fa-spinner"></i></div></div>
                <div class="stat-value" id="sc-proses">0</div>
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-sub">Di outlet laundry</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card c5 fade-in d5 h-100" onclick="filterByStatus('antar', this)">
                <div class="stat-hdr"><div class="stat-icon c5"><i class="fas fa-shipping-fast"></i></div></div>
                <div class="stat-value" id="sc-antar">0</div>
                <div class="stat-label">Sedang Antar</div>
                <div class="stat-sub">Menuju ke pelanggan</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari ID trip, nama pelanggan, alamat..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Kurir</label>
            <select class="filter-input" id="filterDriver">
                <option value="">Semua Kurir</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:130px">
            <label class="filter-label">Tanggal</label>
            <input class="filter-input" type="date" id="filterDate" onchange="applyFilters()">
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- Content: Card list grid + Side Panel -->
    <div class="row fade-in">
        <!-- Left Section: Trip cards and pagination -->
        <div class="col-12 col-xl-8 col-xxl-9 mb-4">
            <div class="trip-cards-grid" id="tripCardsGrid">
                <!-- Trip cards injected dynamically -->
            </div>
            <!-- Pagination Bar -->
            <div class="pagination-bar" id="paginationBar">
                <div class="pagination-info">Halaman <strong id="currentPage">1</strong> dari <strong id="totalPages">1</strong></div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
        </div>

        <!-- Right Section: Side Panel -->
        <div class="col-12 col-xl-4 col-xxl-3">
            <div class="side-panel">
                <!-- Live stats -->
                <div class="live-stats">
                    <div class="live-badge"><i class="fas fa-circle"></i> Live Monitor</div>
                    <div class="live-stat-grid">
                        <div class="live-stat-item"><div class="live-stat-val" id="ls-kurir-aktif">{{ count($drivers) }}</div><div class="live-stat-lbl">Kurir Aktif</div></div>
                        <div class="live-stat-item"><div class="live-stat-val" id="ls-trip-hari">0</div><div class="live-stat-lbl">Trip Hari Ini</div></div>
                        <div class="live-stat-item"><div class="live-stat-val">5.8 km</div><div class="live-stat-lbl">Jarak Avg</div></div>
                        <div class="live-stat-item"><div class="live-stat-val">28m</div><div class="live-stat-lbl">Waktu Avg</div></div>
                    </div>
                </div>

                <!-- Driver Status -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-card-title">
                            <div class="panel-card-title-icon" style="background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(6,182,212,.12));color:var(--secondary)"><i class="fas fa-user-astronaut"></i></div>
                            Status Kurir
                        </div>
                    </div>
                    <div class="panel-card-body" id="driverList">
                        <!-- Status kurir injected dynamically -->
                    </div>
                </div>

                <!-- Today Schedule -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-card-title">
                            <div class="panel-card-title-icon" style="background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(249,115,22,.12));color:var(--warning)"><i class="fas fa-calendar-alt"></i></div>
                            Jadwal Hari Ini
                        </div>
                        <span style="font-size:.75rem;color:var(--gray);position:relative;z-index:1" id="scheduleDate"></span>
                    </div>
                    <div class="panel-card-body">
                        <div class="schedule-list" id="scheduleList">
                            <!-- Today schedule list injected dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.operasional.antarjemput.partials.modal')

    <!-- Toast -->
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
