// ============================================
// CHARTS — Revenue Bar Chart
// ============================================
let revenueChart;

document.addEventListener('DOMContentLoaded', () => {
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const revenueCtx = revenueCanvas.getContext('2d');
        const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const revenueData = [52, 61, 58, 67, 72, 69, 75, 78, 71, 80, 76, 84];
        const orderData   = [820,910,870,980,1050,990,1100,1140,1020,1180,1120,1248];

        let labels = monthLabels;
        let revenueValues = revenueData;
        let orderValues = orderData;
        let revenueUnit = 'jt';
        let revenueDivisor = 1000000;

        if (window.dashboardChartData && window.dashboardChartData.revenueTrend && window.dashboardChartData.revenueTrend.length > 0) {
            labels = window.dashboardChartData.revenueTrend.map(item => item.date);
            
            const maxVal = Math.max(...window.dashboardChartData.revenueTrend.map(item => item.total));
            if (maxVal < 1000000) {
                revenueUnit = 'rb';
                revenueDivisor = 1000;
            } else if (maxVal >= 1000000000) {
                revenueUnit = 'M';
                revenueDivisor = 1000000000;
            }

            revenueValues = window.dashboardChartData.revenueTrend.map(item => item.total / revenueDivisor);
            orderValues = window.dashboardChartData.revenueTrend.map(item => item.count);
        }

        revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: revenueValues,
                        backgroundColor: function(ctx) {
                            const chart = ctx.chart;
                            const {ctx: c, chartArea} = chart;
                            if (!chartArea) return 'rgba(99,102,241,0.7)';
                            const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(99,102,241,0.85)');
                            gradient.addColorStop(1, 'rgba(139,92,246,0.4)');
                            return gradient;
                        },
                        borderRadius: 8,
                        borderSkipped: false,
                        borderColor: 'rgba(99,102,241,0)',
                        yAxisID: 'y',
                    },
                    {
                        label: 'Order',
                        data: orderValues,
                        type: 'line',
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(31,41,55,0.95)',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,0.7)',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) return ' Pendapatan: Rp ' + ctx.parsed.y.toFixed(1) + revenueUnit;
                                return ' Order: ' + ctx.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#9CA3AF', font: { size: 11 } }
                    },
                    y: {
                        position: 'left',
                        grid: { color: '#F3F4F6', drawBorder: false },
                        border: { display: false, dash: [4,4] },
                        ticks: {
                            color: '#9CA3AF', font: { size: 11 },
                            callback: v => 'Rp ' + v.toFixed(1) + revenueUnit
                        }
                    },
                    y1: {
                        position: 'right',
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#9CA3AF', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ============================================
    // DONUT CHART — Service Distribution
    // ============================================
    const donutCanvas = document.getElementById('donutChart');
    if (donutCanvas) {
        const donutCtx = donutCanvas.getContext('2d');
        
        let donutLabels = ['Cuci Setrika', 'Cuci Kering', 'Express', 'Satuan', 'Setrika Saja'];
        let donutValues = [38, 27, 18, 11, 6];

        if (window.dashboardChartData && window.dashboardChartData.serviceDist && window.dashboardChartData.serviceDist.length > 0) {
            const topServices = window.dashboardChartData.serviceDist.slice(0, 5);
            const totalCount = topServices.reduce((sum, item) => sum + item.count, 0) || 1;
            donutLabels = topServices.map(item => item.service_type);
            donutValues = topServices.map(item => Math.round((item.count / totalCount) * 100));
        }

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutValues,
                    backgroundColor: ['#6366F1','#10B981','#F59E0B','#EC4899','#9CA3AF'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(31,41,55,0.95)',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,0.7)',
                        padding: 10, cornerRadius: 10,
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + '%'
                        }
                    }
                }
            }
        });
    }
});

// Period Switcher (fallback logic for demo)
const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const revenueData = [52, 61, 58, 67, 72, 69, 75, 78, 71, 80, 76, 84];
const weekData    = [8.2, 9.1, 7.8, 10.4, 9.8, 11.2, 10.6];
const weekLabels  = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];

function switchPeriod(btn, period) {
    document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    let labels, data;
    let unit = 'jt';
    if (period === 'week') { 
        labels = weekLabels; 
        data = weekData; 
    } else if (period === 'month') { 
        if (window.dashboardChartData && window.dashboardChartData.revenueTrend && window.dashboardChartData.revenueTrend.length > 0) {
            labels = window.dashboardChartData.revenueTrend.map(item => item.date);
            const maxVal = Math.max(...window.dashboardChartData.revenueTrend.map(item => item.total));
            let divisor = 1000000;
            if (maxVal < 1000000) { unit = 'rb'; divisor = 1000; }
            else if (maxVal >= 1000000000) { unit = 'M'; divisor = 1000000000; }
            data = window.dashboardChartData.revenueTrend.map(item => item.total / divisor);
        } else {
            labels = monthLabels; 
            data = revenueData; 
        }
    } else { 
        labels = ['2019','2020','2021','2022','2023','2024']; 
        data = [420,380,510,640,720,840]; 
        unit = 'jt';
    }

    if (revenueChart) {
        revenueChart.data.labels = labels;
        revenueChart.data.datasets[0].data = data;
        
        // Match line chart order values
        if (period === 'month' && window.dashboardChartData && window.dashboardChartData.revenueTrend && window.dashboardChartData.revenueTrend.length > 0) {
            revenueChart.data.datasets[1].data = window.dashboardChartData.revenueTrend.map(item => item.count);
        } else {
            revenueChart.data.datasets[1].data = data.map(v => Math.round(v * 14.5));
        }

        // Update scales tooltip formatting
        revenueChart.options.scales.y.ticks.callback = v => 'Rp ' + v.toFixed(1) + unit;
        revenueChart.update('active');
    }
}

// Expose functions globally to be called from blade onclick attributes
window.switchPeriod = switchPeriod;
