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
        @vite(['resources/css/admin/dashboard.css'])
    @endpush

    @push('scripts')
        <script>
            window.dashboardChartData = {
                revenueTrend: @json($laporanData['dailyRevenue'] ?? []),
                serviceDist: @json($laporanData['serviceDist'] ?? [])
            };
        </script>
        @vite(['resources/js/admin/dashboard.js'])
    @endpush

    <!-- STAT CARDS -->
    <div class="row g-4 mb-4">
        <!-- Pendapatan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Pendapatan Bulan Ini"
                value="{{ formatRupiahK($totalRevenue ?? 0) }}"
                icon="wallet"
                trend="{{ number_format(abs($revenueGrowth ?? 0), 1) }}%"
                trendType="{{ ($revenueGrowth ?? 0) >= 0 ? 'up' : 'down' }}"
                footerText="vs bulan lalu: {{ formatRupiahK($prevRevenueSum ?? 0) }}"
                progress="{{ min(100, intval(($prevRevenueSum ?? 0) > 0 ? (($totalRevenue ?? 0) / ($prevRevenueSum ?? 0)) * 100 : 100)) }}%"
                theme="blue"
                delayClass="d1"
            />
        </div>

        <!-- Total Order -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Total Order Bulan Ini"
                value="{{ number_format($totalOrders ?? 0) }}"
                icon="box-open"
                trend="8.3%"
                trendType="up"
                footerText="Rata-rata {{ ($totalOrders ?? 0) > 0 ? number_format(($totalOrders ?? 0) / 30, 1) : 0 }} order/hari"
                progress="{{ min(100, intval((($totalOrders ?? 0) / 1500) * 100)) }}%"
                theme="green"
                delayClass="d2"
            />
        </div>

        <!-- Pelanggan Aktif -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Pelanggan Aktif"
                value="{{ number_format($totalCustomers ?? 0) }}"
                icon="users"
                trend="5.7%"
                trendType="up"
                footerText="{{ App\Models\User::role('customer')->whereMonth('created_at', Carbon\Carbon::now()->month)->count() }} pelanggan baru"
                progress="{{ min(100, intval((($totalCustomers ?? 0) / 5000) * 100)) }}%"
                theme="purple"
                delayClass="d3"
            />
        </div>

        <!-- Rating -->
        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard.stat-card 
                title="Rating Rata-rata"
                value="{{ number_format($avgRating ?? 4.87, 2) }}"
                icon="star"
                trend="0.2"
                trendType="down"
                footerText="Dari {{ number_format($totalOrders ?? 0) }} ulasan"
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
                        <div class="chart-summary-value">{{ formatRupiahK($totalRevenue ?? 0) }}</div>
                        <div class="chart-summary-label"><span class="chart-summary-dot" style="background:var(--primary)"></span>Pendapatan</div>
                    </div>
                    <div class="chart-summary-item">
                        <div class="chart-summary-value">{{ number_format($totalOrders ?? 0) }}</div>
                        <div class="chart-summary-label"><span class="chart-summary-dot" style="background:var(--secondary)"></span>Order</div>
                    </div>
                    <div class="chart-summary-item">
                        <div class="chart-summary-value">{{ formatRupiahK(($totalOrders ?? 0) > 0 ? ($totalRevenue ?? 0) / ($totalOrders ?? 0) : 0) }}</div>
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
                    @php $rank = 1; @endphp
                    @forelse(collect($laporanData['outletRevenues'] ?? [])->sortByDesc('current') as $outletRev)
                    <div class="outlet-item">
                        <div class="outlet-rank r{{ $rank <= 5 ? $rank : 'other' }}">{{ $rank }}</div>
                        <div class="outlet-info">
                            <div class="outlet-name">{{ $outletRev['name'] }}</div>
                            <div class="outlet-city">Cabang</div>
                            @php
                                $maxVal = collect($laporanData['outletRevenues'] ?? [])->max('current') ?: 1;
                                $widthPct = min(100, intval(($outletRev['current'] / $maxVal) * 100));
                            @endphp
                            <div class="outlet-progress"><div class="outlet-progress-fill" style="width:{{ $widthPct }}%"></div></div>
                        </div>
                        <div class="outlet-perf">
                            <div class="outlet-revenue">{{ formatRupiahK($outletRev['current']) }}</div>
                            @php
                                $growth = $outletRev['previous'] > 0 
                                    ? (($outletRev['current'] - $outletRev['previous']) / $outletRev['previous']) * 100 
                                    : 0;
                            @endphp
                            <div class="outlet-growth" style="color:{{ $growth >= 0 ? 'var(--secondary)' : 'var(--danger)' }}">
                                <i class="fas fa-arrow-{{ $growth >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($growth), 1) }}%
                            </div>
                        </div>
                    </div>
                    @php $rank++; @endphp
                    @empty
                    <div style="padding: 1.5rem; text-align: center; color: var(--gray);">Belum ada data performa outlet</div>
                    @endforelse
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
                    <a href="{{ route('orders') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:600;position:relative;z-index:1">
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
                            @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td>
                                    <div class="order-id">#{{ $order->order_code }}</div>
                                    <div class="order-sub">{{ $order->created_at->format('d M, H:i') }}</div>
                                </td>
                                <td>
                                    <div class="order-customer">{{ $order->customer->name ?? '-' }}</div>
                                    <div class="order-sub">{{ $order->customer->phone ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>{{ $order->service_type }}</div>
                                    <div class="order-sub">{{ $order->weight }} kg</div>
                                </td>
                                <td>
                                    <div class="order-outlet">{{ $order->outlet->name ?? '-' }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'Baru' => 'badge-diterima',
                                            'Proses' => 'badge-proses',
                                            'Selesai' => 'badge-siap',
                                            'Diambil' => 'badge-selesai'
                                        ][$order->order_status] ?? 'badge-proses';
                                    @endphp
                                    <span class="order-badge {{ $statusClass }}">{{ $order->order_status }}</span>
                                </td>
                                <td>
                                    <div class="order-amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--gray);">Belum ada order terbaru</td>
                            </tr>
                            @endforelse
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
                            <div class="donut-center-value">{{ number_format($totalOrders ?? 0) }}</div>
                            <div class="donut-center-label">Total Order</div>
                        </div>
                    </div>
                    <div class="donut-legend">
                        @php
                            $colors = ['#6366F1','#10B981','#F59E0B','#EC4899','#9CA3AF'];
                            $serviceDist = collect($laporanData['serviceDist'] ?? []);
                            $totalCount = $serviceDist->sum('count') ?: 1;
                        @endphp
                        @forelse($serviceDist->take(5) as $index => $sd)
                        <div class="legend-item">
                            <div class="legend-dot" style="background:{{ $colors[$index] ?? '#9CA3AF' }}"></div>
                            <span class="legend-name">{{ $sd->service_type }}</span>
                            <span class="legend-pct">{{ number_format(($sd->count / $totalCount) * 100, 1) }}%</span>
                        </div>
                        @empty
                        <div style="text-align: center; color: var(--gray); font-size: 0.8rem;">Tidak ada data distribusi</div>
                        @endforelse
                    </div>
                </x-dashboard.card>

                <!-- Quick Actions -->
                <x-dashboard.card 
                    title="Aksi Cepat" 
                    icon="bolt" 
                    iconStyle="background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(249,115,22,0.12));color:var(--warning)">
                    <div class="quick-actions">
                        <a href="{{ route('orders') }}" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(99,102,241,0.1),rgba(139,92,246,0.1));color:var(--primary)">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <span class="quick-action-label">Order Baru</span>
                        </a>
                        <a href="{{ route('customers') }}" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(6,182,212,0.1));color:var(--secondary)">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="quick-action-label">Tambah Pelanggan</span>
                        </a>
                        <a href="{{ route('reports') }}" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(249,115,22,0.12));color:var(--warning)">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <span class="quick-action-label">Export Laporan</span>
                        </a>
                        <a href="{{ route('outlets') }}" class="quick-action-btn">
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
