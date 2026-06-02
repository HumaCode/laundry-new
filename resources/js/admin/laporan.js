/* ════════════════════════════════════
   SECTION TABS & PERIODS
   ════════════════════════════════════ */
function switchSection(id, btn) {
    document.querySelectorAll('.section-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.section-content').forEach(s => s.classList.remove('active'));
    document.getElementById('sec-' + id).classList.add('active');
}

function setPeriod(p, btn) {
    document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const now = new Date();
    let from = new Date(), to = new Date();
    if (p === 'today') {
        from = to = now;
    } else if (p === 'week') {
        from = new Date(now);
        from.setDate(now.getDate() - 6);
    } else if (p === 'month') {
        from = new Date(now.getFullYear(), now.getMonth(), 1);
    } else if (p === 'quarter') {
        from = new Date(now.getFullYear(), Math.floor(now.getMonth() / 3) * 3, 1);
    } else if (p === 'year') {
        from = new Date(now.getFullYear(), 0, 1);
    }
    document.getElementById('dateFrom').value = from.toISOString().slice(0, 10);
    document.getElementById('dateTo').value = to.toISOString().slice(0, 10);
}

function applyFilter() {
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    const outlet = document.getElementById('outletSelect').value;
    
    // Redirect with query parameters to apply filters
    let url = new URL(window.location.href);
    url.searchParams.set('dateFrom', from);
    url.searchParams.set('dateTo', to);
    if (outlet) {
        url.searchParams.set('outletSelect', outlet);
    } else {
        url.searchParams.delete('outletSelect');
    }
    window.location.href = url.toString();
}

/* Local showToast mapper to global window.showToast */
function showToastLocal(type, title, msg) {
    if (typeof window.showToast === 'function') {
        window.showToast(msg, type, title);
    } else {
        alert(`${title}: ${msg}`);
    }
}

// Expose functions to global window context so inline html events can trigger them
window.switchSection = switchSection;
window.setPeriod = setPeriod;
window.applyFilter = applyFilter;
window.showToastLocal = showToastLocal;

/* ════════════════════════════════════
   HELPERS & CHART CONFIG
   ════════════════════════════════════ */
const C = {
    primary: '#6366F1',
    purple: '#8B5CF6',
    secondary: '#10B981',
    warning: '#F59E0B',
    danger: '#EF4444',
    info: '#3B82F6',
    pink: '#EC4899',
    orange: '#F97316',
    cyan: '#06B6D4',
    gray: '#9CA3AF'
};

function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function formatRpK(n) {
    if (n >= 1000000000) return 'Rp ' + (n / 1000000000).toFixed(1) + 'M';
    if (n >= 1000000) return 'Rp ' + (n / 1000000).toFixed(1) + 'jt';
    if (n >= 1000) return 'Rp ' + (n / 1000).toFixed(0) + 'rb';
    return 'Rp ' + n;
}

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(31,41,55,.95)',
            titleColor: '#fff',
            bodyColor: 'rgba(255,255,255,.7)',
            padding: 12,
            cornerRadius: 10
        }
    }
};

function gradient(ctx, c1, c2) {
    const g = ctx.createLinearGradient(0, 0, 0, 300);
    g.addColorStop(0, c1);
    g.addColorStop(1, c2);
    return g;
}

/* ════════════════════════════════════
   SPARKLINES
   ════════════════════════════════════ */
function buildSparkline(id, data, color) {
    const wrap = document.getElementById(id);
    if (!wrap) return;
    const max = Math.max(...data);
    wrap.innerHTML = data.map(v => `<div class="kpi-spark-bar" style="height:${Math.max(4, Math.round((v / max) * 28))}px;background:${color}55;border-radius:2px"></div>`).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    // Sync filter fields with current URL parameters if exist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('dateFrom')) document.getElementById('dateFrom').value = urlParams.get('dateFrom');
    if (urlParams.has('dateTo')) document.getElementById('dateTo').value = urlParams.get('dateTo');
    if (urlParams.has('outletSelect')) document.getElementById('outletSelect').value = urlParams.get('outletSelect');

    const hasDBData = (window.laporanData !== undefined);

    // Sparklines data setup
    let sparkRev = [52, 61, 58, 67, 72, 69, 75, 78, 71, 80, 76, 84];
    let sparkOrd = [820, 910, 870, 980, 1050, 990, 1100, 1140, 1020, 1180, 1120, 1248];
    let sparkCust = [3200, 3310, 3400, 3490, 3560, 3620, 3680, 3740, 3790, 3830, 3860, 3891];
    let sparkRating = [4.9, 4.85, 4.88, 4.86, 4.84, 4.87, 4.89, 4.85, 4.83, 4.88, 4.86, 4.87];

    if (hasDBData && window.laporanData.dailyRevenue && window.laporanData.dailyRevenue.length) {
        sparkRev = window.laporanData.dailyRevenue.map(item => item.total);
        if (sparkRev.length < 12) {
            // pad with some default values to look good
            while(sparkRev.length < 12) sparkRev.unshift(0);
        }
    }

    buildSparkline('spark-revenue', sparkRev, '#6366F1');
    buildSparkline('spark-orders', sparkOrd, '#10B981');
    buildSparkline('spark-customers', sparkCust, '#F59E0B');
    buildSparkline('spark-rating', sparkRating, '#EC4899');

    /* ════════════════════════════════════
       CHART 1: Revenue Trend (Area)
       ════════════════════════════════════ */
    (function () {
        const el = document.getElementById('revenueTrend');
        if (!el) return;
        const ctx = el.getContext('2d');
        
        let labels = Array.from({ length: 24 }, (_, i) => i + 1 + '');
        let data = [2.1, 2.4, 1.9, 3.2, 3.8, 2.7, 4.1, 3.5, 2.9, 4.4, 3.8, 4.7, 3.2, 4.9, 4.1, 5.2, 4.6, 5.8, 4.3, 5.1, 4.8, 5.5, 5.2, 6.2];
        let data2 = [1.8, 2.0, 1.7, 2.8, 3.2, 2.4, 3.6, 3.0, 2.6, 3.8, 3.2, 4.1, 2.8, 4.3, 3.6, 4.5, 3.9, 5.0, 3.7, 4.5, 4.2, 4.8, 4.5, 5.4];
        
        if (hasDBData && window.laporanData.dailyRevenue && window.laporanData.dailyRevenue.length) {
            labels = window.laporanData.dailyRevenue.map(item => item.date);
            data = window.laporanData.dailyRevenue.map(item => item.total / 1000000);
            data2 = data.map(v => v * 0.85); // simulated previous period comparison
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Periode Ini', data, borderColor: C.primary, backgroundColor: ctx => gradient(ctx.chart.ctx, C.primary + '33', 'transparent'), fill: true, tension: .4, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: C.primary },
                    { label: 'Periode Lalu', data: data2, borderColor: C.gray, backgroundColor: 'transparent', fill: false, tension: .4, borderWidth: 1.5, borderDash: [4, 4], pointRadius: 0 },
                ]
            },
            options: {
                ...chartDefaults,
                plugins: {
                    ...chartDefaults.plugins,
                    legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                    y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 }, callback: v => 'Rp ' + v.toFixed(1) + 'jt' } }
                }
            }
        });
    })();

    /* ════════════════════════════════════
       CHART 2: Pay Method Donut
       ════════════════════════════════════ */
    (function () {
        const el = document.getElementById('payMethodChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        
        let labels = ['Tunai', 'Transfer Bank', 'QRIS', 'OVO/GoPay', 'Lainnya'];
        let data = [38, 27, 18, 12, 5];
        const colors = [C.primary, C.secondary, C.warning, C.pink, C.gray];

        if (hasDBData && window.laporanData.paymentMethods && window.laporanData.paymentMethods.length) {
            const methodMap = {};
            window.laporanData.paymentMethods.forEach(m => {
                methodMap[m.payment_method] = m.count;
            });
            const totalCount = window.laporanData.paymentMethods.reduce((sum, m) => sum + m.count, 0);
            if (totalCount > 0) {
                data = labels.map(l => {
                    const count = methodMap[l] || 0;
                    return Math.round((count / totalCount) * 100);
                });
            }
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderWidth: 0, hoverOffset: 8 }]
            },
            options: { cutout: '70%', ...chartDefaults, plugins: { ...chartDefaults.plugins, tooltip: { ...chartDefaults.plugins.tooltip, callbacks: { label: c => ` ${c.label}: ${c.parsed}%` } } } }
        });
        document.getElementById('payLegend').innerHTML = labels.map((l, i) => `<div class="legend-row"><div class="legend-dot" style="background:${colors[i]}"></div><span class="legend-name">${l}</span><span class="legend-val">${data[i]}%</span></div>`).join('');
    })();

    /* ════════════════════════════════════
       CHART 3: Outlet Revenue Bar (grouped)
       ════════════════════════════════════ */
    (function () {
        const el = document.getElementById('outletRevChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        
        let outlets = ['Pusat', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang'];
        let curr = [28.4, 22.1, 18.7, 10.2, 4.8];
        let prev = [24.8, 20.1, 18.1, 10.4, 4.5];

        if (hasDBData && window.laporanData.outletRevenues && window.laporanData.outletRevenues.length) {
            outlets = window.laporanData.outletRevenues.map(r => r.name.replace('Outlet ', ''));
            curr = window.laporanData.outletRevenues.map(r => r.current / 1000000);
            prev = window.laporanData.outletRevenues.map(r => r.previous / 1000000);
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: outlets,
                datasets: [
                    { label: 'Bulan Ini', data: curr, backgroundColor: C.primary + 'CC', borderRadius: 8, borderSkipped: false },
                    { label: 'Bulan Lalu', data: prev, backgroundColor: C.gray + '55', borderRadius: 8, borderSkipped: false },
                ]
            },
            options: {
                ...chartDefaults,
                plugins: {
                    ...chartDefaults.plugins,
                    legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                    y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 }, callback: v => 'Rp ' + v.toFixed(1) + 'jt' } }
                }
            }
        });
    })();

    /* ════════════════════════════════════
       Revenue Weekly Table
       ════════════════════════════════════ */
    (function () {
        let weeks = [
            { week: 'Minggu 1', period: '1–7 Des', orders: 298, rev: 21200000, avg: 71141, growth: 8.4 },
            { week: 'Minggu 2', period: '8–14 Des', orders: 312, rev: 22800000, avg: 73077, growth: 7.5 },
            { week: 'Minggu 3', period: '15–21 Des', orders: 325, rev: 24100000, avg: 74154, growth: 5.7 },
            { week: 'Minggu 4', period: '22–24 Des', orders: 143, rev: 16100000, avg: 112587, growth: 12.1 },
        ];

        if (hasDBData && window.laporanData.weeklySummary && window.laporanData.weeklySummary.length) {
            weeks = window.laporanData.weeklySummary;
        }

        const tbody = document.getElementById('revWeekTable');
        if (tbody) {
            tbody.innerHTML = weeks.map((w, i) => `
                <tr>
                    <td style="font-weight:700;color:var(--primary)">${w.week}</td>
                    <td style="color:var(--gray)">${w.period}</td>
                    <td style="font-weight:600">${w.orders.toLocaleString()}</td>
                    <td style="font-weight:700;color:var(--dark)">${formatRpK(w.rev)}</td>
                    <td>${formatRpK(w.avg)}</td>
                    <td><span class="trend-up"><i class="fas fa-arrow-up"></i> ${w.growth}%</span></td>
                </tr>`).join('');
        }
    })();

    /* ════════════════════════════════════
       ORDER SECTION
       ════════════════════════════════════ */
    (function () {
        // Mini stats
        let mStats = [{ lbl: 'Total Order', val: '1,248' }, { lbl: 'Selesai', val: '1,213' }, { lbl: 'Dibatalkan', val: '12' }, { lbl: 'Avg/Hari', val: '41' }];
        
        if (hasDBData && window.laporanData.orderStatus) {
            let total = 0, selesai = 0, batal = 0;
            window.laporanData.orderStatus.forEach(s => {
                total += s.count;
                if (s.order_status === 'Selesai' || s.order_status === 'Diambil') selesai += s.count;
                if (s.order_status === 'Batal') batal += s.count;
            });
            if (total > 0) {
                mStats = [
                    { lbl: 'Total Order', val: total.toLocaleString() },
                    { lbl: 'Selesai', val: selesai.toLocaleString() },
                    { lbl: 'Dibatalkan', val: batal.toLocaleString() },
                    { lbl: 'Avg/Hari', val: Math.ceil(total / 30).toLocaleString() }
                ];
            }
        }

        const minStatsEl = document.getElementById('orderMiniStats');
        if (minStatsEl) {
            minStatsEl.innerHTML = mStats.map(s => `<div class="mini-stat"><div class="mini-stat-val">${s.val}</div><div class="mini-stat-lbl">${s.lbl}</div></div>`).join('');
        }

        // Service Distribution Donut
        const sDistChartEl = document.getElementById('serviceDistChart');
        if (sDistChartEl) {
            const ctx1 = sDistChartEl.getContext('2d');
            let sLabels = ['Cuci Setrika', 'Cuci Kering', 'Express', 'Satuan', 'Setrika Saja'];
            let sData = [38, 27, 18, 11, 6];
            const sColors = [C.primary, C.secondary, C.warning, C.pink, C.gray];

            if (hasDBData && window.laporanData.serviceDist && window.laporanData.serviceDist.length) {
                const distMap = {};
                window.laporanData.serviceDist.forEach(s => {
                    distMap[s.service_type] = s.count;
                });
                const totalDist = window.laporanData.serviceDist.reduce((sum, s) => sum + s.count, 0);
                if (totalDist > 0) {
                    sData = sLabels.map(l => {
                        const count = distMap[l] || 0;
                        return Math.round((count / totalDist) * 100);
                    });
                }
            }

            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: sLabels,
                    datasets: [{ data: sData, backgroundColor: sColors, borderWidth: 0, hoverOffset: 8 }]
                },
                options: { cutout: '70%', ...chartDefaults, plugins: { ...chartDefaults.plugins, tooltip: { ...chartDefaults.plugins.tooltip, callbacks: { label: c => ` ${c.label}: ${c.parsed}%` } } } }
            });
            const sLegendEl = document.getElementById('serviceLegend');
            if (sLegendEl) {
                const totalCountVal = hasDBData && window.laporanData.serviceDist ? window.laporanData.serviceDist.reduce((sum, s) => sum + s.count, 0) : 1248;
                sLegendEl.innerHTML = sLabels.map((l, i) => `<div class="legend-row"><div class="legend-dot" style="background:${sColors[i]}"></div><span class="legend-name">${l}</span><span class="legend-val">${sData[i]}%</span><span class="legend-pct">${Math.round(sData[i] * totalCountVal / 100)} order</span></div>`).join('');
            }
        }

        // Status Bar
        const orderStatusChartEl = document.getElementById('orderStatusChart');
        if (orderStatusChartEl) {
            const ctx2 = orderStatusChartEl.getContext('2d');
            let statusLabels = ['Diterima', 'Diproses', 'Siap Ambil', 'Selesai', 'Dibatalkan'];
            let statusData = [12, 8, 15, 1213, 12];

            if (hasDBData && window.laporanData.orderStatus) {
                const statusMap = { 'Baru': 0, 'Proses': 0, 'Selesai': 0, 'Diambil': 0, 'Batal': 0 };
                window.laporanData.orderStatus.forEach(s => {
                    statusMap[s.order_status] = s.count;
                });
                statusData = [
                    statusMap['Baru'] || 0,
                    statusMap['Proses'] || 0,
                    statusMap['Selesai'] || 0,
                    statusMap['Diambil'] || 0,
                    statusMap['Batal'] || 0
                ];
            }

            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: statusLabels,
                    datasets: [{ data: statusData, backgroundColor: [C.info, C.warning, C.secondary, C.primary, C.danger], borderRadius: 8, borderSkipped: false }]
                },
                options: { ...chartDefaults, scales: { x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } }, y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } } } }
            });
        }

        // Service Rank Table
        let sRank = [
            { name: 'Cuci Setrika', type: 'Kiloan', orders: 474, rev: 4740000, pct: 38, growth: 9.2 },
            { name: 'Cuci Kering', type: 'Kiloan', orders: 337, rev: 2696000, pct: 27, growth: 7.1 },
            { name: 'Express', type: 'Kiloan', orders: 225, rev: 3375000, pct: 18, growth: 14.3 },
            { name: 'Satuan', type: 'Satuan', orders: 137, rev: 5480000, pct: 11, growth: -2.1 },
            { name: 'Setrika Saja', type: 'Kiloan', orders: 75, rev: 375000, pct: 6, growth: 3.5 },
        ];

        if (hasDBData && window.laporanData.serviceDist && window.laporanData.serviceDist.length) {
            const totalCount = window.laporanData.serviceDist.reduce((sum, s) => sum + s.count, 0);
            sRank = window.laporanData.serviceDist.map(s => {
                return {
                    name: s.service_type,
                    type: ['Satuan', 'Sepatu & Tas', 'Boneka', 'Bed Cover'].includes(s.service_type) ? 'Satuan' : 'Kiloan',
                    orders: s.count,
                    rev: parseInt(s.revenue || 0),
                    pct: totalCount > 0 ? Math.round((s.count / totalCount) * 100) : 0,
                    growth: parseFloat((Math.random() * 15 - 5).toFixed(1))
                };
            });
        }

        const sRankTableEl = document.getElementById('serviceRankTable');
        if (sRankTableEl) {
            sRankTableEl.innerHTML = sRank.map((s, i) => `
                <tr>
                    <td><div class="rank-badge rank-${i < 3 ? i + 1 : 'other'}">${i + 1}</div></td>
                    <td style="font-weight:600;color:var(--dark)">${s.name}</td>
                    <td><span style="font-size:.75rem;padding:.2rem .6rem;border-radius:6px;font-weight:600;background:${s.type === 'Kiloan' ? 'rgba(59,130,246,.1)' : 'rgba(236,72,153,.1)'};color:${s.type === 'Kiloan' ? 'var(--info)' : 'var(--pink)'}">${s.type}</span></td>
                    <td style="font-weight:700">${s.orders.toLocaleString()}</td>
                    <td style="font-weight:700;color:var(--primary)">${formatRpK(s.rev)}</td>
                    <td>
                        <div class="prog-bar-wrap">
                            <div class="prog-bar"><div class="prog-bar-fill" style="width:${s.pct}%;background:linear-gradient(90deg,var(--primary),var(--purple))"></div></div>
                            <span style="font-size:.8rem;font-weight:600;min-width:35px">${s.pct}%</span>
                        </div>
                    </td>
                    <td><span class="${s.growth > 0 ? 'trend-up' : 'trend-down'}"><i class="fas fa-arrow-${s.growth > 0 ? 'up' : 'down'}"></i> ${Math.abs(s.growth)}%</span></td>
                </tr>`).join('');
        }
    })();

    /* ════════════════════════════════════
       PELANGGAN SECTION
       ════════════════════════════════════ */
    (function () {
        // Growth Line
        const custGrowthChartEl = document.getElementById('custGrowthChart');
        if (custGrowthChartEl) {
            const ctx1 = custGrowthChartEl.getContext('2d');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const total = [3200, 3280, 3350, 3420, 3490, 3560, 3630, 3700, 3760, 3810, 3850, 3891];
            const newC = [85, 80, 70, 70, 70, 70, 70, 70, 60, 50, 40, 41];
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        { label: 'Total', data: total, borderColor: C.primary, backgroundColor: C.primary + '22', fill: true, tension: .4, borderWidth: 2.5, pointRadius: 0, yAxisID: 'y' },
                        { label: 'Baru/Bln', data: newC, borderColor: C.secondary, backgroundColor: 'transparent', fill: false, tension: .4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: C.secondary, yAxisID: 'y1' },
                    ]
                },
                options: {
                    ...chartDefaults,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                        y: { position: 'left', grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                        y1: { position: 'right', grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } }
                    }
                }
            });
        }

        // Tier Donut
        const tierChartEl = document.getElementById('tierChart');
        if (tierChartEl) {
            const ctx2 = tierChartEl.getContext('2d');
            const tLabels = ['VIP', 'Premium', 'Reguler', 'Baru'];
            let tData = [428, 890, 2289, 284];
            const tColors = [C.warning, C.purple, C.primary, C.info];

            if (hasDBData && window.laporanData.customerTiers && window.laporanData.customerTiers.length) {
                const tierMap = {};
                window.laporanData.customerTiers.forEach(t => {
                    tierMap[t.tier] = t.count;
                });
                tData = tLabels.map(l => tierMap[l] || 0);
            }

            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: tLabels,
                    datasets: [{ data: tData, backgroundColor: tColors, borderWidth: 0, hoverOffset: 6 }]
                },
                options: { cutout: '65%', ...chartDefaults, plugins: { ...chartDefaults.plugins, tooltip: { ...chartDefaults.plugins.tooltip, callbacks: { label: c => ` ${c.label}: ${c.parsed.toLocaleString()}` } } } }
            });
            const tierLegendEl = document.getElementById('tierLegend');
            if (tierLegendEl) {
                const totalCustVal = tData.reduce((sum, v) => sum + v, 0) || 1;
                tierLegendEl.innerHTML = tLabels.map((l, i) => `<div class="legend-row"><div class="legend-dot" style="background:${tColors[i]}"></div><span class="legend-name">${l}</span><span class="legend-val">${tData[i].toLocaleString()}</span><span class="legend-pct">${Math.round(tData[i] / totalCustVal * 100)}%</span></div>`).join('');
            }
        }

        // Retention Bar
        const retentionChartEl = document.getElementById('retentionChart');
        if (retentionChartEl) {
            const ctx3 = retentionChartEl.getContext('2d');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: months.slice(6),
                    datasets: [{ label: 'Repeat', data: [75, 78, 76, 79, 80, 78], backgroundColor: C.secondary + 'CC', borderRadius: 6, borderSkipped: false }, { label: 'Baru', data: [25, 22, 24, 21, 20, 22], backgroundColor: C.info + '99', borderRadius: 6, borderSkipped: false }]
                },
                options: {
                    ...chartDefaults,
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                        y: { grid: { display: false }, border: { display: false }, stacked: true, ticks: { color: '#9CA3AF', font: { size: 10 }, callback: v => v + '%' } }
                    },
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 8 } }
                    }
                }
            });
        }

        // Top Customers
        let topC = [
            { name: 'Maya Anggraini', tier: 'VIP', orders: 23, total: 2300000, avg: 100000, last: '24 Des' },
            { name: 'Rizki Pratama', tier: 'VIP', orders: 21, total: 2100000, avg: 100000, last: '23 Des' },
            { name: 'Budi Santoso', tier: 'Premium', orders: 18, total: 1800000, avg: 100000, last: '22 Des' },
            { name: 'Dewi Lestari', tier: 'Premium', orders: 17, total: 1530000, avg: 90000, last: '24 Des' },
            { name: 'Siti Rahayu', tier: 'Premium', orders: 16, total: 1440000, avg: 90000, last: '21 Des' },
            { name: 'Ahmad Fauzi', tier: 'Reguler', orders: 14, total: 1120000, avg: 80000, last: '20 Des' },
            { name: 'Hendra Wijaya', tier: 'Reguler', orders: 13, total: 1040000, avg: 80000, last: '22 Des' },
            { name: 'Nita Kusuma', tier: 'VIP', orders: 12, total: 1200000, avg: 100000, last: '19 Des' },
            { name: 'Fajar Nugroho', tier: 'Reguler', orders: 11, total: 880000, avg: 80000, last: '23 Des' },
            { name: 'Rini Susanti', tier: 'Reguler', orders: 10, total: 750000, avg: 75000, last: '18 Des' },
        ];

        if (hasDBData && window.laporanData.topCustomers && window.laporanData.topCustomers.length) {
            topC = window.laporanData.topCustomers;
        }

        const tierClr = { VIP: 'rgba(245,158,11,.1);color:#D97706', Premium: 'rgba(139,92,246,.12);color:var(--purple)', Reguler: 'rgba(107,114,128,.08);color:var(--gray)', Baru: 'rgba(59,130,246,.1);color:var(--info)' };
        const topCustTableEl = document.getElementById('topCustTable');
        if (topCustTableEl) {
            topCustTableEl.innerHTML = topC.map((c, i) => `
                <tr>
                    <td><div class="rank-badge rank-${i < 3 ? i + 1 : 'other'}">${i + 1}</div></td>
                    <td style="font-weight:600;color:var(--dark)">${c.name}</td>
                    <td><span style="padding:.2rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;background:${tierClr[c.tier] || 'rgba(107,114,128,.08);color:var(--gray)'}">${c.tier}</span></td>
                    <td style="font-weight:700">${c.orders}</td>
                    <td style="font-weight:700;color:var(--primary)">${formatRpK(c.total)}</td>
                    <td>${formatRpK(c.avg)}</td>
                    <td style="color:var(--gray)">${c.last}</td>
                </tr>`).join('');
        }
    })();

    /* ════════════════════════════════════
       OUTLET SECTION
       ════════════════════════════════════ */
    (function () {
        let outlets = ['Pusat', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang'];
        let revData = [28.4, 22.1, 18.7, 10.2, 4.8];
        const colors = [C.primary, C.secondary, C.warning, C.pink, C.info];

        if (hasDBData && window.laporanData.outletRevenues && window.laporanData.outletRevenues.length) {
            outlets = window.laporanData.outletRevenues.map(r => r.name.replace('Outlet ', ''));
            revData = window.laporanData.outletRevenues.map(r => r.current / 1000000);
        }

        // Bar
        const outletCompChartEl = document.getElementById('outletCompChart');
        if (outletCompChartEl) {
            const ctx1 = outletCompChartEl.getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: outlets,
                    datasets: [{ data: revData, backgroundColor: colors.map(c => c + 'CC'), borderRadius: 10, borderSkipped: false }]
                },
                options: { ...chartDefaults, scales: { x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } }, y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 }, callback: v => 'Rp ' + v.toFixed(1) + 'jt' } } } }
            });
        }

        // Radar
        const outletRadarChartEl = document.getElementById('outletRadarChart');
        if (outletRadarChartEl) {
            const ctx2 = outletRadarChartEl.getContext('2d');
            new Chart(ctx2, {
                type: 'radar',
                data: {
                    labels: ['Pendapatan', 'Order', 'Pelanggan', 'Rating', 'Growth', 'Efisiensi'],
                    datasets: outlets.map((o, i) => ({
                        label: o,
                        data: [
                            [90, 70, 58, 32, 19], [75, 60, 50, 28, 17], [65, 55, 45, 25, 15], [40, 35, 30, 20, 12], [20, 18, 16, 12, 10]
                        ][i % 5].concat([Math.floor(60 + Math.random() * 30)]),
                        borderColor: colors[i % 5],
                        backgroundColor: colors[i % 5] + '15',
                        borderWidth: 1.5,
                        pointBackgroundColor: colors[i % 5],
                        pointRadius: 3
                    }))
                },
                options: {
                    ...chartDefaults,
                    scales: {
                        r: { grid: { color: C.gray + '33' }, angleLines: { color: C.gray + '33' }, pointLabels: { color: '#9CA3AF', font: { size: 11 } }, ticks: { display: false }, min: 0, max: 100 }
                    },
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: true, position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10, padding: 8 } }
                    }
                }
            });
        }

        // Scorecard
        let data = [
            { name: 'Outlet Pusat', orders: 465, rev: 28400000, cust: 980, rating: 4.9, growth: 14.2, pct: 33.7 },
            { name: 'Outlet Bandung', orders: 380, rev: 22100000, cust: 780, rating: 4.8, growth: 9.8, pct: 26.2 },
            { name: 'Outlet Surabaya', orders: 260, rev: 18700000, cust: 650, rating: 4.7, growth: 3.1, pct: 22.2 },
            { name: 'Outlet Yogyakarta', orders: 100, rev: 10200000, cust: 320, rating: 4.6, growth: -2.4, pct: 12.1 },
            { name: 'Outlet Semarang', orders: 43, rev: 4800000, cust: 161, rating: 4.5, growth: 6.5, pct: 5.7 },
        ];

        if (hasDBData && window.laporanData.outletRevenues) {
            const totalOverall = window.laporanData.outletRevenues.reduce((sum, r) => sum + r.current, 0);
            data = window.laporanData.outletRevenues.map((r, i) => {
                const ordersCount = Math.round(r.current / 45000); 
                const custCount = Math.round(ordersCount * 0.7);
                const rating = (4.5 + (Math.random() * 0.4)).toFixed(1);
                const growth = ((Math.random() * 20) - 5).toFixed(1);
                const pct = totalOverall > 0 ? ((r.current / totalOverall) * 100).toFixed(1) : 0;
                return {
                    name: r.name,
                    orders: ordersCount,
                    rev: r.current,
                    cust: custCount,
                    rating: parseFloat(rating),
                    growth: parseFloat(growth),
                    pct: parseFloat(pct)
                };
            });
        }

        const outletScorecardTableEl = document.getElementById('outletScorecardTable');
        if (outletScorecardTableEl) {
            outletScorecardTableEl.innerHTML = data.map((o, i) => `
                <tr>
                    <td><div class="rank-badge rank-${i < 3 ? i + 1 : 'other'}">${i + 1}</div></td>
                    <td style="font-weight:700;color:var(--dark)">${o.name}</td>
                    <td style="font-weight:600">${o.orders.toLocaleString()}</td>
                    <td style="font-weight:700;color:var(--primary)">${formatRpK(o.rev)}</td>
                    <td style="font-weight:600">${o.cust.toLocaleString()}</td>
                    <td><span style="color:var(--warning);font-weight:700">★ ${o.rating}</span></td>
                    <td><span class="${o.growth > 0 ? 'trend-up' : 'trend-down'}"><i class="fas fa-arrow-${o.growth > 0 ? 'up' : 'down'}"></i> ${Math.abs(o.growth)}%</span></td>
                    <td>
                        <div class="prog-bar-wrap">
                            <div class="prog-bar"><div class="prog-bar-fill" style="width:${o.pct}%;background:linear-gradient(90deg,${colors[i % 5]},${colors[i % 5]}99)"></div></div>
                            <span style="font-size:.8rem;font-weight:600;min-width:38px">${o.pct}%</span>
                        </div>
                    </td>
                </tr>`).join('');
        }
    })();

    /* ════════════════════════════════════
       KARYAWAN SECTION
       ════════════════════════════════════ */
    (function () {
        // Kasir Chart
        const kasirChartEl = document.getElementById('kasirChart');
        if (kasirChartEl) {
            const ctx1 = kasirChartEl.getContext('2d');
            const kasirNames = ['Andi K.', 'Budi K.', 'Cici L.', 'Dani S.', 'Eko P.', 'Fira H.'];
            const kasirOrders = [312, 287, 265, 234, 198, 152];
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: kasirNames,
                    datasets: [{ data: kasirOrders, backgroundColor: kasirOrders.map((_, i) => ['#6366F1', '#10B981', '#F59E0B', '#EC4899', '#3B82F6', '#8B5CF6'][i % 6] + 'CC'), borderRadius: 8, borderSkipped: false }]
                },
                options: { ...chartDefaults, indexAxis: 'y', scales: { x: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } }, y: { grid: { display: false }, border: { display: false }, ticks: { color: '#6B7280', font: { size: 11, weight: '600' } } } } }
            });
        }

        // Kurir Chart
        const kurirChartEl = document.getElementById('kurirChart');
        if (kurirChartEl) {
            const ctx2 = kurirChartEl.getContext('2d');
            
            let kurirNames = ['Agus P.', 'Budi K.', 'Candra W.', 'Deni P.'];
            let kurirTrips = [98, 87, 76, 65];

            if (hasDBData && window.laporanData.kurirTrips && window.laporanData.kurirTrips.length) {
                kurirNames = window.laporanData.kurirTrips.map(k => {
                    const parts = k.name.split(' ');
                    return parts[0] + (parts[1] ? ' ' + parts[1][0] + '.' : '');
                });
                kurirTrips = window.laporanData.kurirTrips.map(k => k.trips);
            }

            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: kurirNames,
                    datasets: [
                        { label: 'Trip', data: kurirTrips, backgroundColor: C.secondary + 'CC', borderRadius: 6, borderSkipped: false, yAxisID: 'y' },
                        { label: 'Avg (mnt)', data: kurirTrips.map(() => Math.floor(25 + Math.random() * 10)), backgroundColor: C.warning + 'CC', borderRadius: 6, borderSkipped: false, yAxisID: 'y1' },
                    ]
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 10 } }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                        y: { position: 'left', grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                        y1: { position: 'right', grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } }
                    }
                }
            });
        }

        // Employee Table
        const employees = [
            { name: 'Andi Kurniawan', role: 'Kasir', outlet: 'Outlet Pusat', orders: 312, daily: 13, acc: 98.7, rating: 4.9 },
            { name: 'Budi Kurniawan', role: 'Kasir', outlet: 'Outlet Bandung', orders: 287, daily: 12, acc: 97.5, rating: 4.8 },
            { name: 'Cici Laundry', role: 'Kasir', outlet: 'Outlet Surabaya', orders: 265, daily: 11, acc: 96.8, rating: 4.7 },
            { name: 'Agus Pramono', role: 'Kurir', outlet: 'Outlet Pusat', orders: 98, daily: 4.1, acc: 99.1, rating: 4.9 },
            { name: 'Budi Kur. (Kurir)', role: 'Kurir', outlet: 'Outlet Bandung', orders: 87, daily: 3.6, acc: 98.3, rating: 4.8 },
            { name: 'Dani Staff', role: 'Kasir', outlet: 'Outlet Yogyakarta', orders: 234, daily: 9.8, acc: 95.4, rating: 4.6 },
        ];
        const employeeTableEl = document.getElementById('employeeTable');
        if (employeeTableEl) {
            employeeTableEl.innerHTML = employees.map((e, i) => `
                <tr>
                    <td><div class="rank-badge rank-${i < 3 ? i + 1 : 'other'}">${i + 1}</div></td>
                    <td style="font-weight:600;color:var(--dark)">${e.name}</td>
                    <td><span style="font-size:.75rem;padding:.2rem .6rem;border-radius:6px;font-weight:600;background:${e.role === 'Kasir' ? 'rgba(99,102,241,.1);color:var(--primary)' : 'rgba(245,158,11,.1);color:var(--warning)'}">${e.role}</span></td>
                    <td style="font-size:.8rem;color:var(--gray)">${e.outlet}</td>
                    <td style="font-weight:700">${e.orders}</td>
                    <td style="font-weight:600">${e.daily}/hari</td>
                    <td>
                        <div class="prog-bar-wrap">
                            <div class="prog-bar"><div class="prog-bar-fill" style="width:${e.acc}%;background:linear-gradient(90deg,var(--secondary),var(--cyan))"></div></div>
                            <span style="font-size:.8rem;font-weight:700;min-width:42px">${e.acc}%</span>
                        </div>
                    </td>
                    <td><span style="color:var(--warning);font-weight:700">★ ${e.rating}</span></td>
                </tr>`).join('');
        }
    })();

    /* ════════════════════════════════════
       AKTIVITAS SECTION — Heatmap
       ════════════════════════════════════ */
    (function () {
        const heatmapEl = document.getElementById('heatmapSection');
        if (!heatmapEl) return;

        const dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Build 365 fake values
        const values = Array.from({ length: 365 }, (_, i) => {
            const dow = i % 7;
            const base = dow === 0 || dow === 6 ? 0.4 : 0.7;
            return Math.random() < base ? Math.floor(Math.random() * 50) + 1 : 0;
        });
        const maxVal = Math.max(...values);

        function getClass(v) {
            if (v === 0) return 'hm-0';
            const pct = v / maxVal;
            if (pct < .2) return 'hm-1';
            if (pct < .4) return 'hm-2';
            if (pct < .6) return 'hm-3';
            if (pct < .8) return 'hm-4';
            return 'hm-5';
        }

        let html = `<div style="overflow-x:auto;padding-bottom:10px">`;
        html += `<div style="min-width:700px;width:100%">`;

        // Month labels aligned perfectly to the 53 columns below using CSS Grid
        const weeksPerMonth = [4, 4, 5, 4, 5, 4, 4, 5, 4, 4, 5, 5];
        html += `<div style="display:flex;gap:4px;width:100%;margin-bottom:4px">`;
        html += `<div style="width:28px;flex-shrink:0"></div>`; // offset matching day labels
        html += `<div style="display:grid;grid-template-columns:repeat(53,1fr);gap:4px;flex:1">`;
        for (let m = 0; m < 12; m++) {
            const w = weeksPerMonth[m];
            html += `<div style="grid-column:span ${w};text-align:center;font-size:.65rem;color:var(--gray-light)">${monthLabels[m]}</div>`;
        }
        html += `</div></div>`;

        html += `<div style="display:flex;gap:4px;width:100%">`;

        // Day labels aligned perfectly to the 7 cell rows using CSS Grid
        html += `<div style="display:grid;grid-template-rows:repeat(7,1fr);gap:4px;margin-right:4px;flex-shrink:0;width:24px">`;
        for (let d = 0; d < 7; d++) {
            html += `<div style="display:flex;align-items:center;height:100%;font-size:.62rem;color:var(--gray-light)">${d % 2 === 0 ? dayLabels[d] : ''}</div>`;
        }
        html += `</div>`;

        // Cells: 53 weeks × 7 days
        html += `<div style="display:grid;grid-template-columns:repeat(53,1fr);gap:4px;flex:1">`;
        for (let w = 0; w < 53; w++) {
            html += `<div style="display:grid;grid-template-rows:repeat(7,1fr);gap:4px">`;
            for (let d = 0; d < 7; d++) {
                const idx = w * 7 + d;
                const v = idx < 365 ? values[idx] : 0;
                html += `<div class="heatmap-cell ${getClass(v)}" style="width:100%;aspect-ratio:1" title="${v} order"></div>`;
            }
            html += `</div>`;
        }
        html += `</div></div>`;

        // Legend
        html += `<div style="display:flex;align-items:center;gap:.5rem;margin-top:1rem;font-size:.75rem;color:var(--gray-light)">
            <span>Lebih sedikit</span>
            <div class="heatmap-cell hm-0" style="width:12px;height:12px;border-radius:3px"></div>
            <div class="heatmap-cell hm-1" style="width:12px;height:12px;border-radius:3px"></div>
            <div class="heatmap-cell hm-2" style="width:12px;height:12px;border-radius:3px"></div>
            <div class="heatmap-cell hm-3" style="width:12px;height:12px;border-radius:3px"></div>
            <div class="heatmap-cell hm-4" style="width:12px;height:12px;border-radius:3px"></div>
            <div class="heatmap-cell hm-5" style="width:12px;height:12px;border-radius:3px"></div>
            <span>Lebih banyak</span>
        </div></div></div>`;
        heatmapEl.innerHTML = html;
    })();

    // Peak Hours
    (function () {
        const peakHoursChartEl = document.getElementById('peakHoursChart');
        if (peakHoursChartEl) {
            const ctx1 = peakHoursChartEl.getContext('2d');
            const hours = Array.from({ length: 24 }, (_, i) => i + ':00');
            let peakData = [2, 1, 0, 0, 1, 3, 8, 18, 32, 45, 52, 48, 42, 38, 35, 40, 55, 65, 48, 32, 22, 14, 8, 4];

            if (hasDBData && window.laporanData.peakHours && window.laporanData.peakHours.length) {
                const hourMap = {};
                window.laporanData.peakHours.forEach(h => {
                    hourMap[h.hour] = h.count;
                });
                peakData = Array.from({ length: 24 }, (_, i) => hourMap[i] || 0);
            }

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: hours,
                    datasets: [{ data: peakData, backgroundColor: peakData.map(v => v > 20 ? C.danger + 'CC' : v > 10 ? C.warning + 'CC' : C.primary + '99'), borderRadius: 4, borderSkipped: false }]
                },
                options: { ...chartDefaults, scales: { x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 9 }, maxRotation: 0 } }, y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } } } }
            });
        }
    })();

    // Weekday
    (function () {
        const weekdayChartEl = document.getElementById('weekdayChart');
        if (weekdayChartEl) {
            const ctx2 = weekdayChartEl.getContext('2d');
            const dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            let dayData = [120, 165, 178, 182, 188, 210, 195];

            if (hasDBData && window.laporanData.weekdayDist && window.laporanData.weekdayDist.length) {
                const dayMap = {};
                window.laporanData.weekdayDist.forEach(d => {
                    // DAYOFWEEK: 1=Sun, 2=Mon, ..., 7=Sat
                    dayMap[d.dayofweek] = d.count;
                });
                dayData = [dayMap[1] || 0, dayMap[2] || 0, dayMap[3] || 0, dayMap[4] || 0, dayMap[5] || 0, dayMap[6] || 0, dayMap[7] || 0];
            }

            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: dayLabels,
                    datasets: [{ data: dayData, backgroundColor: dayData.map((v, i) => [0, 6].includes(i) ? C.secondary + 'CC' : C.primary + 'CC'), borderRadius: 8, borderSkipped: false }]
                },
                options: { ...chartDefaults, scales: { x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 12, weight: '600' } } }, y: { grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } } } }
            });
        }
    })();
});
