@php
    if (!function_exists('formatRupiahK')) {
        function formatRupiahK($n) {
            if ($n >= 1000000000) return 'Rp ' . number_format($n / 1000000000, 1) . 'M';
            if ($n >= 1000000) return 'Rp ' . number_format($n / 1000000, 1) . 'jt';
            if ($n >= 1000) return 'Rp ' . number_format($n / 1000, 0) . 'rb';
            return 'Rp ' . $n;
        }
    }
@endphp

<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/pembayaran.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/pembayaran.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Data Pembayaran</h2>
            <p>Pantau dan kelola transaksi keuangan dari seluruh outlet secara real-time</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid fade-in">
        <x-stat-card 
            theme="c1"
            icon="wallet"
            trend="12.5%"
            trendType="up"
            value="{{ formatRupiahK($stats['total_pendapatan'] ?? 0) }}"
            valueId="statTotalPendapatan"
            title="Total Pendapatan"
            footerText="Akumulasi pembayaran lunas"
        />
        <x-stat-card 
            theme="c3"
            icon="hand-holding-usd"
            trend="5.2%"
            trendType="down"
            value="{{ formatRupiahK($stats['total_piutang'] ?? 0) }}"
            valueId="statTotalPiutang"
            title="Total Piutang"
            footerText="Akumulasi belum dibayar"
        />
        <x-stat-card 
            theme="c2"
            icon="check-circle"
            trend="8.1%"
            trendType="up"
            value="{{ number_format($stats['count_lunas'] ?? 0) }}"
            valueId="statCountLunas"
            title="Transaksi Lunas"
            footerText="Order lunas terproses"
        />
        <x-stat-card 
            theme="c4"
            icon="exclamation-circle"
            trend="2.4%"
            trendType="down"
            value="{{ number_format($stats['count_belum'] ?? 0) }}"
            valueId="statCountBelum"
            title="Menunggu Pembayaran"
            footerText="Order belum diselesaikan"
        />
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar fade-in">
        <div class="filter-search">
            <span class="filter-search-icon"><i class="fas fa-search"></i></span>
            <input class="filter-input" type="text" id="searchInput" placeholder="Cari kode order, nama pelanggan..." oninput="applyFilters()">
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Status Bayar</label>
            <select class="filter-input" id="filterStatus" onchange="applyFilters()">
                <option value="">Semua Status</option>
                <option value="Lunas">Lunas</option>
                <option value="Belum">Belum Lunas</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Metode</label>
            <select class="filter-input" id="filterMethod" onchange="applyFilters()">
                <option value="">Semua Metode</option>
                <option value="Tunai">Tunai</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>
        <div class="filter-group" style="min-width:160px">
            <label class="filter-label">Outlet</label>
            <select class="filter-input" id="filterOutlet" onchange="applyFilters()">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $o)
                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="min-width:140px">
            <label class="filter-label">Urutkan</label>
            <select class="filter-input" id="filterSort" onchange="applyFilters()">
                <option value="recent">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="amount-high">Nominal Tertinggi</option>
                <option value="amount-low">Nominal Terendah</option>
            </select>
        </div>
        <button class="filter-btn filter-btn-reset" onclick="resetFilters()"><i class="fas fa-undo"></i> Reset</button>
        <button class="filter-btn filter-btn-primary" onclick="applyFilters()"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-card fade-in">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="table-card-icon"><i class="fas fa-credit-card"></i></div>
                Daftar Pembayaran Order
            </div>
            <div class="table-meta">
                <span>Menampilkan <strong id="showCount">0</strong> dari <strong id="totalCount">0</strong></span>
                <select class="per-page-select" id="perPageSelect" onchange="changePerPage(this.value)">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Tanggal Order</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="paymentTableBody"></tbody>
            </table>
        </div>
        
        <!-- Empty State -->
        <div class="empty-state" id="emptyState" style="display:none">
            <div class="empty-icon"><i class="fas fa-credit-card"></i></div>
            <div class="empty-title">Tidak ada data pembayaran ditemukan</div>
            <div class="empty-desc">Coba ubah kata kunci pencarian atau filter Anda</div>
        </div>

        <!-- Pagination Bar -->
        <div class="pagination-bar p-3" id="paginationBar">
            <nav id="paginationNav" aria-label="Page navigation"></nav>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event,'detailModal')">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="modal-title">
                    <h3 id="detOrderCode">Detail Pembayaran</h3>
                    <p>Detail tagihan & rincian pembayaran order</p>
                </div>
                <button class="modal-close" onclick="closeModal('detailModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body modal-payment-detail">
                <div class="detail-row">
                    <span class="detail-label">Pelanggan</span>
                    <span class="detail-value" id="detCustomer">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">No. Telepon</span>
                    <span class="detail-value" id="detPhone">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Outlet</span>
                    <span class="detail-value" id="detOutlet">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Layanan</span>
                    <span class="detail-value" id="detService">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Tagihan</span>
                    <span class="detail-value text-primary" style="font-weight:800; font-size:1.1rem" id="detAmount">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status Pembayaran</span>
                    <span class="detail-value"><span class="payment-badge" id="detStatus">-</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran</span>
                    <span class="detail-value" id="detMethod">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Transaksi</span>
                    <span class="detail-value" id="detDate">-</span>
                </div>
                <div class="detail-row" style="flex-direction:column; align-items:flex-start; gap:0.25rem">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value text-muted" style="font-weight:400" id="detNotes">-</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('detailModal')"><i class="fas fa-times"></i> Tutup</button>
            </div>
        </div>
    </div>

    <!-- UPDATE PAYMENT MODAL -->
    <div class="modal-overlay" id="paymentModal" onclick="closeModalOutside(event,'paymentModal')">
        <div class="modal-box" style="max-width: 500px">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="fas fa-credit-card"></i></div>
                <div class="modal-title">
                    <h3 id="payOrderCode">Proses Pembayaran</h3>
                    <p>Perbarui status pembayaran order ini</p>
                </div>
                <button class="modal-close" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div style="font-size:0.8rem; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.4px">Pelanggan</div>
                    <div id="payCustomer" style="font-size:1.1rem; font-weight:700; color:var(--dark)">-</div>
                </div>
                <div class="mb-3">
                    <div style="font-size:0.8rem; color:var(--gray); text-transform:uppercase; font-weight:600; letter-spacing:0.4px">Total Tagihan</div>
                    <div id="payAmount" style="font-size:1.25rem; font-weight:800; color:var(--primary)">-</div>
                </div>
                <div class="form-field mb-3">
                    <label>Status Pembayaran</label>
                    <select class="form-control" id="payStatus" onchange="toggleMethodSelect(this.value)">
                        <option value="Belum">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>
                <div class="form-field mb-3" id="methodSelectContainer" style="display:none">
                    <label class="mb-2">Metode Pembayaran</label>
                    <div class="payment-methods-grid">
                        <label class="payment-method-card">
                            <input type="radio" name="pay_method_option" value="Tunai" checked>
                            <div class="method-card-content">
                                <i class="fas fa-money-bill-wave method-icon"></i>
                                <span class="method-name">Tunai / Cash</span>
                            </div>
                        </label>
                        <label class="payment-method-card">
                            <input type="radio" name="pay_method_option" value="QRIS">
                            <div class="method-card-content">
                                <i class="fas fa-qrcode method-icon"></i>
                                <span class="method-name">QRIS</span>
                            </div>
                        </label>
                        <label class="payment-method-card">
                            <input type="radio" name="pay_method_option" value="Transfer">
                            <div class="method-card-content">
                                <i class="fas fa-university method-icon"></i>
                                <span class="method-name">Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-outline" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i> Batal</button>
                <button class="modal-btn modal-btn-primary" id="btnSavePayment" onclick="savePayment()"><i class="fas fa-check"></i> Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Scroll Top Button -->
    <div class="float-btn-container">
        <button class="btn-scroll-top" id="scrollTopBtn" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
</x-app-layout>
