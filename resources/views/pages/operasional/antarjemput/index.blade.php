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
    <div class="stats-grid">
        <div class="stat-card c1 fade-in d1 active-stat" onclick="filterByStatus('all', this)">
            <div class="stat-hdr"><div class="stat-icon c1"><i class="fas fa-layer-group"></i></div></div>
            <div class="stat-value" id="sc-all">0</div>
            <div class="stat-label">Semua Trip</div>
            <div class="stat-sub">Hari ini</div>
        </div>
        <div class="stat-card c2 fade-in d2" onclick="filterByStatus('menunggu', this)">
            <div class="stat-hdr"><div class="stat-icon c2"><i class="fas fa-clock"></i></div></div>
            <div class="stat-value" id="sc-menunggu">0</div>
            <div class="stat-label">Menunggu</div>
            <div class="stat-sub">Belum dijadwalkan</div>
        </div>
        <div class="stat-card c3 fade-in d3" onclick="filterByStatus('jemput', this)">
            <div class="stat-hdr"><div class="stat-icon c3"><i class="fas fa-motorcycle"></i></div></div>
            <div class="stat-value" id="sc-jemput">0</div>
            <div class="stat-label">Sedang Jemput</div>
            <div class="stat-sub">Dalam perjalanan</div>
        </div>
        <div class="stat-card c4 fade-in d4" onclick="filterByStatus('proses', this)">
            <div class="stat-hdr"><div class="stat-icon c4"><i class="fas fa-spinner"></i></div></div>
            <div class="stat-value" id="sc-proses">0</div>
            <div class="stat-label">Sedang Diproses</div>
            <div class="stat-sub">Di outlet laundry</div>
        </div>
        <div class="stat-card c5 fade-in d5" onclick="filterByStatus('antar', this)">
            <div class="stat-hdr"><div class="stat-icon c5"><i class="fas fa-shipping-fast"></i></div></div>
            <div class="stat-value" id="sc-antar">0</div>
            <div class="stat-label">Sedang Antar</div>
            <div class="stat-sub">Menuju ke pelanggan</div>
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
    <div class="content-layout fade-in">
        <!-- Left Section: Trip cards and pagination -->
        <div>
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

    <!-- ======================== DETAIL MODAL ======================== -->
    <div class="modal-overlay" id="detailModal" onclick="closeModalOut(event,'detailModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="fas fa-route"></i></div>
                <div class="modal-title">
                    <h3 id="dm-id">Detail Trip</h3>
                    <p id="dm-date">—</p>
                </div>
                <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-user"></i> Pelanggan</div>
                    <div class="modal-info-grid">
                        <div class="modal-field"><label>Nama</label><div class="val" id="dm-cust">—</div></div>
                        <div class="modal-field"><label>Telepon</label><div class="val" id="dm-phone">—</div></div>
                        <div class="modal-field"><label>Outlet</label><div class="val" id="dm-outlet">—</div></div>
                        <div class="modal-field"><label>Order Terkait</label><div class="val mono" id="dm-order">—</div></div>
                    </div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-map-marker-alt"></i> Rute</div>
                    <div class="modal-info-grid">
                        <div class="modal-field"><label>Alamat Jemput</label><div class="val" id="dm-from">—</div></div>
                        <div class="modal-field"><label>Alamat Antar</label><div class="val" id="dm-to">—</div></div>
                        <div class="modal-field"><label>Jarak</label><div class="val" id="dm-dist">—</div></div>
                        <div class="modal-field"><label>Estimasi Waktu</label><div class="val" id="dm-eta">—</div></div>
                    </div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-motorcycle"></i> Kurir</div>
                    <div class="modal-info-grid">
                        <div class="modal-field"><label>Nama Kurir</label><div class="val" id="dm-driver">—</div></div>
                        <div class="modal-field"><label>Kendaraan</label><div class="val" id="dm-vehicle">—</div></div>
                        <div class="modal-field"><label>Layanan</label><div class="val" id="dm-service">—</div></div>
                        <div class="modal-field"><label>Biaya</label><div class="val" style="color:var(--primary);font-weight:700" id="dm-fee">—</div></div>
                    </div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-stream"></i> Progress Trip</div>
                    <div class="trip-timeline" id="dm-timeline"></div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fas fa-sticky-note"></i> Catatan</div>
                    <div style="padding:.875rem;background:#F9FAFB;border-radius:10px;font-size:.875rem;color:var(--gray)" id="dm-notes"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-danger" onclick="cancelTrip()"><i class="fas fa-times-circle"></i> Batalkan</button>
                <button class="modal-btn modal-btn-outline" onclick="closeModal('detailModal')"><i class="fas fa-times"></i> Tutup</button>
                <button class="modal-btn modal-btn-primary" onclick="openAssignModal()"><i class="fas fa-user-plus"></i> Tugaskan Kurir</button>
                <button class="modal-btn modal-btn-success" onclick="openStatusModal()"><i class="fas fa-exchange-alt"></i> Update Status</button>
            </div>
        </div>
    </div>

    <!-- ======================== STATUS MODAL ======================== -->
    <div class="modal-overlay" id="statusModal" onclick="closeModalOut(event,'statusModal')">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-exchange-alt"></i></div>
                <div class="modal-title"><h3>Update Status Trip</h3><p id="sm-id">—</p></div>
                <button class="modal-close" onclick="closeModal('statusModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.875rem;color:var(--gray);margin-bottom:1rem">Pilih status baru untuk trip ini:</p>
                <div class="status-options">
                    <div class="status-option" onclick="selectStatus(this,'menunggu')"><div class="so-icon"><i class="fas fa-clock"></i></div><div class="so-label">Menunggu</div></div>
                    <div class="status-option" onclick="selectStatus(this,'jemput')"><div class="so-icon"><i class="fas fa-motorcycle"></i></div><div class="so-label">Sedang Jemput</div></div>
                    <div class="status-option" onclick="selectStatus(this,'proses')"><div class="so-icon"><i class="fas fa-spinner"></i></div><div class="so-label">Sedang Proses</div></div>
                    <div class="status-option" onclick="selectStatus(this,'antar')"><div class="so-icon"><i class="fas fa-shipping-fast"></i></div><div class="so-label">Sedang Antar</div></div>
                    <div class="status-option" onclick="selectStatus(this,'selesai')"><div class="so-icon"><i class="fas fa-check-circle"></i></div><div class="so-label">Selesai</div></div>
                    <div class="status-option" onclick="selectStatus(this,'batal')"><div class="so-icon"><i class="fas fa-times-circle"></i></div><div class="so-label">Batal</div></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('statusModal')">Batal</button>
                <button class="modal-btn modal-btn-success" onclick="confirmStatus()"><i class="fas fa-check"></i> Konfirmasi</button>
            </div>
        </div>
    </div>

    <!-- ======================== ASSIGN DRIVER MODAL ======================== -->
    <div class="modal-overlay" id="assignModal" onclick="closeModalOut(event,'assignModal')">
        <div class="modal-box" style="max-width:460px">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--warning),var(--orange))"><i class="fas fa-user-plus"></i></div>
                <div class="modal-title"><h3>Tugaskan Kurir</h3><p>Pilih kurir yang tersedia</p></div>
                <button class="modal-close" onclick="closeModal('assignModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div id="driverOptions"></div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('assignModal')">Batal</button>
                <button class="modal-btn modal-btn-primary" onclick="confirmAssign()"><i class="fas fa-check"></i> Tugaskan</button>
            </div>
        </div>
    </div>

    <!-- ======================== ADD TRIP MODAL ======================== -->
    <div class="modal-overlay" id="addModal" onclick="closeModalOut(event,'addModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:linear-gradient(135deg,var(--secondary),var(--success))"><i class="fas fa-plus-circle"></i></div>
                <div class="modal-title"><h3>Buat Trip Baru</h3><p>Atur jadwal antar atau jemput</p></div>
                <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom:1.25rem">
                    <div class="form-section-title"><i class="fas fa-user"></i> Data Pelanggan</div>
                    <div class="form-grid-2">
                        <div class="form-field">
                            <label>Nama Pelanggan <span class="req">*</span></label>
                            <div class="input-icon-wrap"><input class="form-control" id="a-cust" type="text" placeholder="Masukkan nama pelanggan"><i class="fas fa-user ic"></i></div>
                        </div>
                        <div class="form-field">
                            <label>No. Telepon <span class="req">*</span></label>
                            <div class="input-icon-wrap"><input class="form-control" id="a-phone" type="tel" placeholder="08xxxxxxxxxx"><i class="fas fa-phone ic"></i></div>
                        </div>
                        <div class="form-field">
                            <label>Order Terkait</label>
                            <div class="input-icon-wrap"><input class="form-control" id="a-order" type="text" placeholder="cth. ORD-2026-1248"><i class="fas fa-receipt ic"></i></div>
                        </div>
                        <div class="form-field">
                            <label>Outlet</label>
                            <select class="form-control" id="a-outlet">
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:1.25rem">
                    <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> Rute</div>
                    <div class="form-grid-2">
                        <div class="form-field full">
                            <label>Alamat Jemput <span class="req">*</span></label>
                            <div class="input-icon-wrap"><textarea class="form-control" id="a-from" placeholder="Alamat lengkap penjemputan" style="padding-left:2.75rem;min-height:60px"></textarea><i class="fas fa-map-pin ic" style="top:1rem;transform:none"></i></div>
                        </div>
                        <div class="form-field full">
                            <label>Alamat Antar <span class="req">*</span></label>
                            <div class="input-icon-wrap"><textarea class="form-control" id="a-to" placeholder="Alamat lengkap pengantaran" style="padding-left:2.75rem;min-height:60px"></textarea><i class="fas fa-map-marker-alt ic" style="top:1rem;transform:none"></i></div>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:1.25rem">
                    <div class="form-section-title"><i class="fas fa-motorcycle"></i> Detail Trip</div>
                    <div class="form-grid-2">
                        <div class="form-field">
                            <label>Layanan</label>
                            <select class="form-control" id="a-service">
                                <option value="Antar Jemput Standar">Antar Jemput Standar</option>
                                <option value="Antar Jemput Express">Antar Jemput Express</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Kurir</label>
                            <select class="form-control" id="a-driver">
                                <option value="">-- Pilih Kurir --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Jadwal Trip <span class="req">*</span></label>
                            <input class="form-control" id="a-time" type="datetime-local">
                        </div>
                        <div class="form-field">
                            <label>Berat Estimasi</label>
                            <div class="input-icon-wrap"><input class="form-control" id="a-weight" type="text" placeholder="cth. 3 kg"><i class="fas fa-weight-hanging ic"></i></div>
                        </div>
                        <div class="form-field full">
                            <label>Catatan</label>
                            <textarea class="form-control" id="a-notes" placeholder="Catatan untuk kurir..." style="min-height:60px"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('addModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-success" onclick="saveTrip()"><i class="fas fa-plus-circle"></i> Buat Trip</button>
            </div>
        </div>
    </div>

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
