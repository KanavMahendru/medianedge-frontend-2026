<div class="section-title" style="margin-bottom:12px; margin-top: 25px;">Standouts</div>
<div id="standoutsContainer">
    <!-- JS will populate this -->
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
    const STANDOUTS_DATA = @json($standouts);
    const standoutCharts = {};

    function renderStandouts(region) {
        const container = document.getElementById('standoutsContainer');
        const data = STANDOUTS_DATA[region] || [];
        
        container.innerHTML = '';

        if (data.length === 0) {
            container.innerHTML = '<div class="no-data">No standout data available for this region.</div>';
            return;
        }

        data.forEach((item, index) => {
            const isGain = item.change_pct >= 0;
            const statusClass = isGain ? 'up' : 'down';
            const chartId = `standout-chart-${region}-${index}`;
            const badgeClass = isGain ? 'badge-pos' : 'badge-neg';
            const arrow = isGain ? '▲' : '▼';

            const card = document.createElement('div');
            card.className = 'standout-card-v2';
            
            let statsHtml = '';
            for (const [label, value] of Object.entries(item.stats)) {
                statsHtml += `
                    <div class="sv2-stat-row">
                        <div class="sv2-stat-label">${label}</div>
                        <div class="sv2-stat-value">${value}</div>
                    </div>
                `;
            }

            card.innerHTML = `
                <div class="sv2-header">
                    <div class="sv2-company">
                        <div class="sv2-logo">${item.name.charAt(0)}</div>
                        <div class="sv2-info">
                            <div class="sv2-name">${item.name}</div>
                            <div class="sv2-ticker">${item.symbol} · ${item.exchange}</div>
                        </div>
                    </div>
                    <div class="sv2-price-block">
                        <div class="sv2-price">₹${item.price}</div>
                        <div class="sv2-change ${badgeClass}">${arrow} ${Math.abs(item.change_pct).toFixed(2)}%</div>
                    </div>
                </div>
                
                <div class="sv2-body">
                    <div class="sv2-chart-container">
                        <canvas id="${chartId}"></canvas>
                        <div class="sv2-prev-close-label" id="prev-label-${chartId}">Prev close: ₹${item.prev_close}</div>
                    </div>
                    <div class="sv2-stats">
                        ${statsHtml}
                    </div>
                </div>
                
                <div class="sv2-footer">
                    <div class="sv2-desc">${item.desc}</div>
                </div>
            `;
            
            container.appendChild(card);

            // Render Chart
            setTimeout(() => buildStandoutChart(chartId, item.history, item.history_labels, item.prev_close, isGain), 10);
        });
    }

    function buildStandoutChart(canvasId, history, labels, prevClose, isGain) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const color = isGain ? '#006a27' : '#aa3443';
        const bgColor = isGain ? 'rgba(0, 106, 39, 0.1)' : 'rgba(170, 52, 67, 0.1)';
        
        // Destroy existing chart if any
        if (standoutCharts[canvasId]) {
            standoutCharts[canvasId].destroy();
        }

        const isDark = document.body.classList.contains('dark-theme');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        standoutCharts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: history,
                    borderColor: color,
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, bgColor);
                        gradient.addColorStop(1, 'transparent');
                        return gradient;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#111111',
                        bodyColor: isDark ? '#f8fafc' : '#111111',
                        borderColor: isDark ? '#334155' : '#e5e5e5',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: (context) => `₹${context.parsed.y}`
                        }
                    },
                    zoom: {
                        zoom: {
                            wheel: { 
                                enabled: true,
                                speed: 0.05 
                            },
                            pinch: { enabled: true },
                            mode: 'x',
                        },
                        pan: {
                            enabled: true,
                            mode: 'x',
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 10 } }
                    },
                    y: {
                        position: 'left',
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 10 } }
                    }
                }
            },
            plugins: [{
                id: 'prevCloseLine',
                beforeDraw: (chart) => {
                    const {ctx, scales: {y}, chartArea: {left, right}} = chart;
                    const yPos = y.getPixelForValue(prevClose);
                    
                    ctx.save();
                    ctx.beginPath();
                    ctx.setLineDash([5, 5]);
                    ctx.moveTo(left, yPos);
                    ctx.lineTo(right, yPos);
                    ctx.strokeStyle = isDark ? 'rgba(255, 255, 255, 0.3)' : 'rgba(0, 0, 0, 0.3)';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    ctx.restore();

                    // Position the "Prev close" label
                    const label = document.getElementById(`prev-label-${canvasId}`);
                    if (label) {
                        label.style.top = `${yPos - 12}px`;
                        label.style.right = `10px`;
                    }
                }
            }]
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderStandouts('US'); // Initial load
    });
</script>
@endpush
