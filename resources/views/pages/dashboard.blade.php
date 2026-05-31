<x-app-layout>
    @push('styles')
        @vite(['resources/css/admin/dashboard.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/admin/dashboard.js'])
    @endpush

    <!-- STAT CARDS -->
    <div class="row g-4 mb-4">
        <!-- Pendapatan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Pendapatan Bulan Ini"
                value="Rp 84.2jt"
                icon="wallet"
                trend="12.5%"
                trendType="up"
                footerText="vs bulan lalu: Rp 74.8jt"
                progress="72%"
                theme="blue"
                delayClass="d1"
            />
        </div>

        <!-- Total Order -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Total Order Bulan Ini"
                value="1,248"
                icon="box-open"
                trend="8.3%"
                trendType="up"
                footerText="Rata-rata 41 order/hari"
                progress="83%"
                theme="green"
                delayClass="d2"
            />
        </div>

        <!-- Pelanggan Aktif -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Pelanggan Aktif"
                value="3,891"
                icon="users"
                trend="5.7%"
                trendType="up"
                footerText="284 pelanggan baru"
                progress="60%"
                theme="purple"
                delayClass="d3"
            />
        </div>

        <!-- Rating -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Rating Rata-rata"
                value="4.87"
                icon="star"
                trend="0.2"
                trendType="down"
                footerText="Dari 1,248 ulasan"
                progress="97%"
                theme="orange"
                delayClass="d4"
            />
        </div>
    </div>

    <!-- REVENUE CHART + OUTLET PERFORMANCE -->
    <div class="row g-4 mb-4 animate-fade-up d3">
        <!-- Revenue Chart -->
        <div class="col-12 col-lg-8">
            <x-dashboard.card 
                title="Grafik Pendapatan" 
                icon="chart-bar" 
                iconStyle="background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));color:var(--primary)">
                <x-slot:headerAction>
                    <div class="period-tabs">
                        <button class="period-tab" onclick="switchPeriod(this,'week')">Minggu</button>
                        <button class="period-tab active" onclick="switchPeriod(this,'month')">Bulan</button>
                        <button class="period-tab" onclick="switchPeriod(this,'year')">Tahun</button>
                    </div>
                </x-slot:headerAction>

                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-summary">
                    <div class="chart-summary-item">
                        <div class="chart-summary-value">Rp 84.2jt</div>
                        <div class="chart-summary-label"><span class="chart-summary-dot" style="background:var(--primary)"></span>Pendapatan</div>
                    </div>
                    <div class="chart-summary-item">
                        <div class="chart-summary-value">1,248</div>
                        <div class="chart-summary-label"><span class="chart-summary-dot" style="background:var(--secondary)"></span>Order</div>
                    </div>
                    <div class="chart-summary-item">
                        <div class="chart-summary-value">Rp 67.4rb</div>
                        <div class="chart-summary-label"><span class="chart-summary-dot" style="background:var(--orange)"></span>Rata-rata</div>
                    </div>
                </div>
            </x-dashboard.card>
        </div>

        <!-- Outlet Performance -->
        <div class="col-12 col-lg-4">
            <x-dashboard.card 
                title="Performa Outlet" 
                icon="store" 
                iconStyle="background:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(6,182,212,0.12));color:var(--secondary)">
                <x-slot:headerAction>
                    <span style="font-size:0.75rem;color:var(--gray);position:relative;z-index:1">Bulan ini</span>
                </x-slot:headerAction>

                <div class="outlet-list">
                    <div class="outlet-item">
                        <div class="outlet-rank r1">1</div>
                        <div class="outlet-info">
                            <div class="outlet-name">Outlet Pusat</div>
                            <div class="outlet-city">Jakarta Pusat</div>
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:90%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">Rp 28.4jt</div>
                            <div class="outlet-growth" style="color:var(--secondary)"><i class="fas fa-arrow-up"></i> 14.2%</div>
                        </div>
                    </div>
                    <div class="outlet-item">
                        <div class="outlet-rank r2">2</div>
                        <div class="outlet-info">
                            <div class="outlet-name">Outlet Bandung</div>
                            <div class="outlet-city">Bandung Kota</div>
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:70%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">Rp 22.1jt</div>
                            <div class="outlet-growth" style="color:var(--secondary)"><i class="fas fa-arrow-up"></i> 9.8%</div>
                        </div>
                    </div>
                    <div class="outlet-item">
                        <div class="outlet-rank r3">3</div>
                        <div class="outlet-info">
                            <div class="outlet-name">Outlet Surabaya</div>
                            <div class="outlet-city">Surabaya Barat</div>
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:58%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">Rp 18.7jt</div>
                            <div class="outlet-growth" style="color:var(--warning)"><i class="fas fa-arrow-up"></i> 3.1%</div>
                        </div>
                    </div>
                    <div class="outlet-item">
                        <div class="outlet-rank r4">4</div>
                        <div class="outlet-info">
                            <div class="outlet-name">Outlet Yogyakarta</div>
                            <div class="outlet-city">Yogyakarta</div>
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:45%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">Rp 10.2jt</div>
                            <div class="outlet-growth" style="color:var(--danger)"><i class="fas fa-arrow-down"></i> 2.4%</div>
                        </div>
                    </div>
                    <div class="outlet-item">
                        <div class="outlet-rank r5">5</div>
                        <div class="outlet-info">
                            <div class="outlet-name">Outlet Semarang</div>
                            <div class="outlet-city">Semarang Tengah</div>
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:30%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">Rp 4.8jt</div>
                            <div class="outlet-growth" style="color:var(--secondary)"><i class="fas fa-arrow-up"></i> 6.5%</div>
                        </div>
                    </div>
                </div>
            </x-dashboard.card>
        </div>
    </div>

    <!-- BOTTOM: Recent Orders + Right Column -->
    <div class="row g-4 animate-fade-up d5">
        <!-- Recent Orders -->
        <div class="col-12 col-lg-8">
            <x-dashboard.card 
                title="Order Terbaru" 
                icon="receipt" 
                iconStyle="background:linear-gradient(135deg,rgba(59,130,246,0.12),rgba(99,102,241,0.12));color:var(--info)"
                :noPadding="true">
                <x-slot:headerAction>
                    <a href="#" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:600;position:relative;z-index:1">
                        Lihat Semua <i class="fas fa-arrow-right" style="font-size:0.7rem"></i>
                    </a>
                </x-slot:headerAction>

                <div style="overflow-x:auto">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Outlet</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1248</div><div class="order-sub">24 Des, 13:45</div></td>
                                <td><div class="order-customer">Budi Santoso</div><div class="order-sub">081234567890</div></td>
                                <td><div>Cuci Setrika</div><div class="order-sub">3.5 kg · Kiloan</div></td>
                                <td><div class="order-outlet">Outlet Pusat</div></td>
                                <td><span class="order-badge badge-proses">Diproses</span></td>
                                <td><div class="order-amount">Rp 35.000</div></td>
                            </tr>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1247</div><div class="order-sub">24 Des, 13:20</div></td>
                                <td><div class="order-customer">Siti Rahayu</div><div class="order-sub">089876543210</div></td>
                                <td><div>Express</div><div class="order-sub">2 kg · Kiloan</div></td>
                                <td><div class="order-outlet">Outlet Bandung</div></td>
                                <td><span class="order-badge badge-siap">Siap Diambil</span></td>
                                <td><div class="order-amount">Rp 30.000</div></td>
                            </tr>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1246</div><div class="order-sub">24 Des, 12:55</div></td>
                                <td><div class="order-customer">Ahmad Fauzi</div><div class="order-sub">081398765432</div></td>
                                <td><div>Jas & Blazer</div><div class="order-sub">2 pcs · Satuan</div></td>
                                <td><div class="order-outlet">Outlet Pusat</div></td>
                                <td><span class="order-badge badge-diterima">Diterima</span></td>
                                <td><div class="order-amount">Rp 70.000</div></td>
                            </tr>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1245</div><div class="order-sub">24 Des, 12:30</div></td>
                                <td><div class="order-customer">Maya Anggraini</div><div class="order-sub">087812345678</div></td>
                                <td><div>Bed Cover</div><div class="order-sub">1 set · Satuan</div></td>
                                <td><div class="order-outlet">Outlet Surabaya</div></td>
                                <td><span class="order-badge badge-selesai">Selesai</span></td>
                                <td><div class="order-amount">Rp 25.000</div></td>
                            </tr>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1244</div><div class="order-sub">24 Des, 11:50</div></td>
                                <td><div class="order-customer">Rizki Pratama</div><div class="order-sub">082312345678</div></td>
                                <td><div>Cuci Kering</div><div class="order-sub">5 kg · Kiloan</div></td>
                                <td><div class="order-outlet">Outlet Bandung</div></td>
                                <td><span class="order-badge badge-siap">Siap Diambil</span></td>
                                <td><div class="order-amount">Rp 40.000</div></td>
                            </tr>
                            <tr>
                                <td><div class="order-id">#ORD-2024-1243</div><div class="order-sub">24 Des, 11:10</div></td>
                                <td><div class="order-customer">Dewi Lestari</div><div class="order-sub">081556677889</div></td>
                                <td><div>Sepatu</div><div class="order-sub">2 pair · Satuan</div></td>
                                <td><div class="order-outlet">Outlet Yogyakarta</div></td>
                                <td><span class="order-badge badge-proses">Diproses</span></td>
                                <td><div class="order-amount">Rp 80.000</div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-dashboard.card>
        </div>

        <!-- Right Column -->
        <div class="col-12 col-lg-4">
            <div class="right-column d-flex flex-column gap-4 h-100">
                <!-- Service Distribution -->
                <x-dashboard.card 
                    title="Distribusi Layanan" 
                    icon="chart-pie" 
                    iconStyle="background:linear-gradient(135deg,rgba(139,92,246,0.12),rgba(236,72,153,0.12));color:var(--purple)">
                    <div class="donut-container">
                        <canvas id="donutChart"></canvas>
                        <div class="donut-center">
                            <div class="donut-center-value">1,248</div>
                            <div class="donut-center-label">Total Order</div>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--primary)"></div>
                            <span class="legend-name">Cuci Setrika</span>
                            <span class="legend-pct">38%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--secondary)"></div>
                            <span class="legend-name">Cuci Kering</span>
                            <span class="legend-pct">27%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--warning)"></div>
                            <span class="legend-name">Express</span>
                            <span class="legend-pct">18%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--pink)"></div>
                            <span class="legend-name">Satuan</span>
                            <span class="legend-pct">11%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:var(--gray-light)"></div>
                            <span class="legend-name">Setrika Saja</span>
                            <span class="legend-pct">6%</span>
                        </div>
                    </div>
                </x-dashboard.card>

                <!-- Quick Actions -->
                <x-dashboard.card 
                    title="Aksi Cepat" 
                    icon="bolt" 
                    iconStyle="background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(249,115,22,0.12));color:var(--warning)">
                    <div class="quick-actions">
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(99,102,241,0.1),rgba(139,92,246,0.1));color:var(--primary)">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <span class="quick-action-label">Order Baru</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(6,182,212,0.1));color:var(--secondary)">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="quick-action-label">Tambah Pelanggan</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(249,115,22,0.1));color:var(--warning)">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <span class="quick-action-label">Export Laporan</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(139,92,246,0.1),rgba(236,72,153,0.1));color:var(--purple)">
                                <i class="fas fa-tags"></i>
                            </div>
                            <span class="quick-action-label">Buat Promo</span>
                        </a>
                    </div>
                </x-dashboard.card>
            </div>
        </div>
    </div>
</x-app-layout>
