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
        @vite(['resources/css/admin/laporan.css'])
    @endpush

    @push('scripts')
        <script>
            window.laporanData = @json($laporanData);
        </script>
        @vite(['resources/js/admin/laporan.js'])
    @endpush

    <!-- Page Header -->
    <div class="page-header-bar fade-in">
        <div class="page-header-left">
            <h2>Laporan Bisnis</h2>
            <p>Analisis mendalam pendapatan, order, layanan, pelanggan, dan kinerja outlet LaundryPro</p>
        </div>
        <div class="page-header-actions">
            <button class="btn-page btn-page-outline" onclick="showToastLocal('info','Jadwal','Atur jadwal pengiriman laporan')"><i class="fas fa-calendar-check"></i> Jadwalkan</button>
            <button class="btn-page btn-page-outline" onclick="showToastLocal('info','Export PDF','Mengekspor laporan ke PDF...')"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="btn-page btn-page-success" onclick="showToastLocal('info','Export Excel','Mengekspor data ke Excel...')"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="period-bar fade-in">
        <div class="period-tabs">
            <button class="period-tab" onclick="setPeriod('today',this)">Hari Ini</button>
            <button class="period-tab" onclick="setPeriod('week',this)">Minggu Ini</button>
            <button class="period-tab active" onclick="setPeriod('month',this)">Bulan Ini</button>
            <button class="period-tab" onclick="setPeriod('quarter',this)">Kuartal</button>
            <button class="period-tab" onclick="setPeriod('year',this)">Tahun Ini</button>
        </div>
        <div class="period-divider"></div>
        <div class="period-custom">
            <label>Dari:</label>
            <input type="date" class="period-input" id="dateFrom" value="{{ $dateFrom }}">
            <label>s.d.:</label>
            <input type="date" class="period-input" id="dateTo" value="{{ $dateTo }}">
            <select class="outlet-select" id="outletSelect">
                <option value="">Semua Outlet</option>
                @foreach(App\Models\Master\Outlet::all() as $ot)
                    <option value="{{ $ot->id }}" {{ $outletId == $ot->id ? 'selected' : '' }}>{{ $ot->name }}</option>
                @endforeach
            </select>
            <button class="btn-apply" onclick="applyFilter()"><i class="fas fa-sync-alt"></i> Terapkan</button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid fade-in">
        <x-stat-card 
            theme="c1"
            icon="wallet"
            trend="{{ number_format(abs($revenueGrowth), 1) }}%"
            trendType="{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"
            value="{{ formatRupiahK($totalRevenue) }}"
            title="Total Pendapatan"
            footerText="vs bln lalu: {{ formatRupiahK($prevRevenueSum) }}"
            sparklineId="spark-revenue"
        />
        <x-stat-card 
            theme="c2"
            icon="box-open"
            trend="8.3%"
            trendType="up"
            value="{{ number_format($totalOrders) }}"
            title="Total Order"
            footerText="Rata-rata {{ $totalOrders > 0 ? number_format($totalOrders / 30, 1) : 0 }} order/hari"
            sparklineId="spark-orders"
        />
        <x-stat-card 
            theme="c3"
            icon="users"
            trend="5.7%"
            trendType="up"
            value="{{ number_format($totalCustomers) }}"
            title="Pelanggan Aktif"
            footerText="{{ App\Models\User::role('customer')->whereBetween('created_at', [Carbon\Carbon::parse($dateFrom)->startOfDay(), Carbon\Carbon::parse($dateTo)->endOfDay()])->count() }} pelanggan baru"
            sparklineId="spark-customers"
        />
        <x-stat-card 
            theme="c4"
            icon="star"
            trend="0.2"
            trendType="down"
            value="{{ number_format($avgRating, 2) }}"
            title="Rating Rata-rata"
            footerText="Dari {{ number_format($totalOrders) }} ulasan"
            sparklineId="spark-rating"
        />
    </div>

    <!-- Section Tabs -->
    <div class="section-tabs fade-in">
        <button class="section-tab active" onclick="switchSection('pendapatan',this)"><i class="fas fa-chart-line"></i> Pendapatan</button>
        <button class="section-tab" onclick="switchSection('order',this)"><i class="fas fa-receipt"></i> Order & Layanan</button>
        <button class="section-tab" onclick="switchSection('pelanggan',this)"><i class="fas fa-users"></i> Pelanggan</button>
        <button class="section-tab" onclick="switchSection('outlet',this)"><i class="fas fa-store"></i> Kinerja Outlet</button>
        <button class="section-tab" onclick="switchSection('karyawan',this)"><i class="fas fa-user-tie"></i> Karyawan</button>
        <button class="section-tab" onclick="switchSection('aktivitas',this)"><i class="fas fa-calendar-alt"></i> Aktivitas</button>
    </div>

    <!-- ════ SECTION: PENDAPATAN ════ -->
    <div class="section-content active" id="sec-pendapatan">
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.12));color:var(--primary)"><i class="fas fa-chart-area"></i></div>Tren Pendapatan Harian</div>
                    <span class="card-subtitle">Tren Pendapatan</span>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="revenueTrend"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-chart-pie"></i></div>Distribusi Metode Bayar</div>
                    <span class="card-subtitle">Bulan ini</span>
                </div>
                <div class="card-body">
                    <div class="chart-h240 donut-wrap">
                        <canvas id="payMethodChart"></canvas>
                        <div class="donut-center"><div class="donut-center-val">{{ formatRupiahK($totalRevenue) }}</div><div class="donut-center-lbl">Total</div></div>
                    </div>
                    <div class="chart-legend" id="payLegend"></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(16,185,129,.1);color:var(--secondary)"><i class="fas fa-chart-bar"></i></div>Pendapatan per Outlet — Bulan Ini vs Bulan Lalu</div>
            </div>
            <div class="card-body"><div class="chart-h280"><canvas id="outletRevChart"></canvas></div></div>
        </div>
        <div class="card card-last">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(59,130,246,.1);color:var(--info)"><i class="fas fa-table"></i></div>Rincian Pendapatan per Minggu</div>
            </div>
            <div style="overflow-x:auto">
                <table class="rpt-table">
                    <thead><tr><th>Minggu</th><th>Periode</th><th>Total Order</th><th>Pendapatan</th><th>Avg Order</th><th>vs Minggu Lalu</th></tr></thead>
                    <tbody id="revWeekTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ SECTION: ORDER & LAYANAN ════ -->
    <div class="section-content" id="sec-order">
        <div class="mini-stats" id="orderMiniStats"></div>
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(99,102,241,.1);color:var(--primary)"><i class="fas fa-chart-pie"></i></div>Distribusi Layanan</div>
                </div>
                <div class="card-body">
                    <div class="chart-h240 donut-wrap">
                        <canvas id="serviceDistChart"></canvas>
                        <div class="donut-center"><div class="donut-center-val">{{ number_format($totalOrders) }}</div><div class="donut-center-lbl">Order</div></div>
                    </div>
                    <div class="chart-legend" id="serviceLegend"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(16,185,129,.1);color:var(--secondary)"><i class="fas fa-exchange-alt"></i></div>Status Order Bulan Ini</div>
                </div>
                <div class="card-body"><div class="chart-h240"><canvas id="orderStatusChart"></canvas></div></div>
            </div>
        </div>
        <div class="card card-last">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-trophy"></i></div>Ranking Layanan Terlaris</div>
            </div>
            <div style="overflow-x:auto">
                <table class="rpt-table">
                    <thead><tr><th>#</th><th>Layanan</th><th>Tipe</th><th>Total Order</th><th>Pendapatan</th><th>% dari Total</th><th>Growth</th></tr></thead>
                    <tbody id="serviceRankTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ SECTION: PELANGGAN ════ -->
    <div class="section-content" id="sec-pelanggan">
        <div class="three-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(99,102,241,.1);color:var(--primary)"><i class="fas fa-user-plus"></i></div>Pertumbuhan Pelanggan</div>
                </div>
                <div class="card-body"><div class="chart-h240"><canvas id="custGrowthChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-crown"></i></div>Segmentasi Tier</div>
                </div>
                <div class="card-body">
                    <div class="chart-h200 donut-wrap" style="height:200px;position:relative">
                        <canvas id="tierChart"></canvas>
                        <div class="donut-center"><div class="donut-center-val">{{ number_format($totalCustomers) }}</div><div class="donut-center-lbl">Pelanggan</div></div>
                    </div>
                    <div class="chart-legend" id="tierLegend"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(236,72,153,.1);color:var(--pink)"><i class="fas fa-redo-alt"></i></div>Retensi Pelanggan</div>
                </div>
                <div class="card-body"><div class="chart-h240" style="height:200px;position:relative"><canvas id="retentionChart"></canvas></div>
                    <div style="margin-top:1rem;display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                        <div style="padding:.875rem;background:rgba(16,185,129,.05);border-radius:12px;border:1px solid rgba(16,185,129,.15);text-align:center"><div style="font-size:1.5rem;font-weight:800;color:var(--secondary)">78.4%</div><div style="font-size:.75rem;color:var(--gray)">Repeat Customer</div></div>
                        <div style="padding:.875rem;background:rgba(99,102,241,.05);border-radius:12px;border:1px solid rgba(99,102,241,.15);text-align:center"><div style="font-size:1.5rem;font-weight:800;color:var(--primary)">3.2x</div><div style="font-size:.75rem;color:var(--gray)">Avg Order/Bulan</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-last">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(16,185,129,.1);color:var(--secondary)"><i class="fas fa-medal"></i></div>Top 10 Pelanggan Terbaik</div>
                <span class="card-subtitle">Berdasarkan total belanja periode ini</span>
            </div>
            <div style="overflow-x:auto">
                <table class="rpt-table">
                    <thead><tr><th>#</th><th>Pelanggan</th><th>Tier</th><th>Total Order</th><th>Total Belanja</th><th>Avg Order</th><th>Last Order</th></tr></thead>
                    <tbody id="topCustTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ SECTION: OUTLET ════ -->
    <div class="section-content" id="sec-outlet">
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(99,102,241,.1);color:var(--primary)"><i class="fas fa-chart-bar"></i></div>Perbandingan Pendapatan Outlet</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="outletCompChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(16,185,129,.1);color:var(--secondary)"><i class="fas fa-radiation"></i></div>Radar Kinerja Outlet</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="outletRadarChart"></canvas></div></div>
            </div>
        </div>
        <div class="card card-last">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-store"></i></div>Scorecard Outlet</div>
            </div>
            <div style="overflow-x:auto">
                <table class="rpt-table">
                    <thead><tr><th>#</th><th>Outlet</th><th>Order</th><th>Pendapatan</th><th>Pelanggan</th><th>Rating</th><th>Growth</th><th>% Kontribusi</th></tr></thead>
                    <tbody id="outletScorecardTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ SECTION: KARYAWAN ════ -->
    <div class="section-content" id="sec-karyawan">
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(99,102,241,.1);color:var(--primary)"><i class="fas fa-user-clock"></i></div>Order per Kasir</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="kasirChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-motorcycle"></i></div>Performa Kurir</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="kurirChart"></canvas></div></div>
            </div>
        </div>
        <div class="card card-last">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(16,185,129,.1);color:var(--secondary)"><i class="fas fa-table"></i></div>Detail Kinerja Karyawan</div>
            </div>
            <div style="overflow-x:auto">
                <table class="rpt-table">
                    <thead><tr><th>#</th><th>Nama</th><th>Jabatan</th><th>Outlet</th><th>Total Order</th><th>Rata-rata/Hari</th><th>Akurasi</th><th>Rating</th></tr></thead>
                    <tbody id="employeeTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ SECTION: AKTIVITAS ════ -->
    <div class="section-content" id="sec-aktivitas">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><div class="card-title-icon" style="background:rgba(99,102,241,.1);color:var(--primary)"><i class="fas fa-th"></i></div>Heatmap Aktivitas Order — 2024</div>
                <span class="card-subtitle">Tiap kotak = 1 hari · Warna = jumlah order</span>
            </div>
            <div class="card-body" id="heatmapSection"></div>
        </div>
        <div class="two-col" style="margin-top:1.5rem">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(236,72,153,.1);color:var(--pink)"><i class="fas fa-clock"></i></div>Order per Jam (Peak Hours)</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="peakHoursChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="card-title-icon" style="background:rgba(245,158,11,.1);color:var(--warning)"><i class="fas fa-calendar-week"></i></div>Order per Hari dalam Seminggu</div>
                </div>
                <div class="card-body"><div class="chart-h280"><canvas id="weekdayChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <!-- Float Button -->
    <div class="float-btn-container">
        <button class="float-btn" id="scrollTopBtn" onclick="scrollToTop(event)">
            <div class="float-btn-ring"></div>
            <i class="fas fa-arrow-up"></i>
            <span class="float-btn-tooltip">Kembali ke Atas</span>
        </button>
    </div>
</x-app-layout>
