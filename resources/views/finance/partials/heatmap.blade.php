<div class="section" style="padding:10px 20px;">
    <div class="section-head" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div class="section-title" style="font-size:14px; font-weight:600; color:var(--text-main);">Top 500 Heatmap</div>
        <span style="font-size:12px; color:var(--text-muted); cursor:pointer; display:flex; align-items:center; gap:6px;">Expand <i class="ph ph-arrows-out-simple"></i></span>
    </div>

    <div class="heatmap-wrap" style="border:1px solid var(--border-main); border-radius:12px; background:var(--bg-panel); padding:12px; position:relative;">
        <!-- Sector Labels -->
        <div id="hm-labels" style="display:flex; width:100%; font-size:10px; font-weight:600; color:var(--text-muted); margin-bottom:8px;"></div>
        
        <!-- Grid -->
        <div id="hm-grid" style="display:grid; grid-template-columns: repeat(14, 1fr); gap:2px; grid-auto-rows: minmax(32px, auto);"></div>

        <!-- Legend Row -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; font-size:10px; color:var(--text-muted);">
            <div style="display:flex; align-items:center; gap:4px;">
                <span>-3%</span>
                <div style="display:flex; gap:2px;">
                    <span style="width:8px; height:8px; background:#ef4444; border-radius:1px;"></span>
                    <span style="width:8px; height:8px; background:#f87171; border-radius:1px;"></span>
                    <span style="width:8px; height:8px; background:#334155; border-radius:1px;"></span>
                    <span style="width:8px; height:8px; background:#4ade80; border-radius:1px;"></span>
                    <span style="width:8px; height:8px; background:#10b981; border-radius:1px;"></span>
                </div>
                <span>+3%</span>
            </div>
            <div id="hm-timestamp">5 May 2026, 12:48 GMT+5:30</div>
            <div></div>
        </div>
    </div>
</div>

<!-- Tooltip Element (Fixed at body level for correct positioning) -->

@push('scripts')
<script>
    const HEATMAP_DATA = @json($heatmapData);
    let currentHeatmapRegion = 'US';

    function renderHeatmap(region = 'US') {
        currentHeatmapRegion = region;
        const sectors = HEATMAP_DATA[region];
        if (!sectors) return;

        // 1. Clear previous content
        const labelBox = document.getElementById('hm-labels');
        labelBox.innerHTML = ''; 
        
        const gridBox = document.getElementById('hm-grid');
        // Master Grid for Sectors
        gridBox.style.display = 'grid';
        gridBox.style.gridTemplateColumns = 'repeat(12, 1fr)';
        gridBox.style.gridAutoRows = 'minmax(40px, auto)';
        gridBox.style.gap = '16px';
        gridBox.innerHTML = '';

        // 2. Render Sector Blocks
        sectors.forEach(s => {
            const sectorDiv = document.createElement('div');
            sectorDiv.className = 'hm-sector-block';
            // Apply Sector Level Spans
            sectorDiv.style.gridColumn = s.col || 'span 4';
            sectorDiv.style.gridRow = s.row || 'span 4';
            sectorDiv.style.display = 'flex';
            sectorDiv.style.flexDirection = 'column';
            sectorDiv.style.gap = '6px';

            sectorDiv.innerHTML = `
                <div class="hm-sector-title" style="font-size:11px; font-weight:600; color:var(--text-muted); padding-left:2px; text-transform:uppercase; letter-spacing:0.8px; opacity:0.8;">
                    ${s.name}
                </div>
                <div class="hm-sector-grid" style="display:grid; grid-template-columns: repeat(12, 1fr); grid-auto-rows: 32px; gap:4px; flex:1;">
                    ${s.stocks.map(c => `
                        <div class="hm-cell ${c.color}" 
                             data-ticker="${c.ticker}"
                             style="grid-column:${c.col}; grid-row:${c.row}; display:flex; flex-direction:column; align-items:center; justify-content:center; cursor:pointer; border-radius:4px; overflow:hidden; transition: all 0.2s ease;">
                            <span class="ticker" style="font-size:10px; font-weight:700; color:rgba(255,255,255,0.95); line-height:1.1;">${c.ticker}</span>
                            <span class="pct" style="font-size:8px; font-weight:500; color:rgba(255,255,255,0.8); line-height:1.2;">${c.pct}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            gridBox.appendChild(sectorDiv);
        });

        initHeatmapTooltips(region);
    }

    function initHeatmapTooltips(region) {
        const sectors = HEATMAP_DATA[region];
        if (!sectors) return;

        // Collect all stocks for easy lookup
        const allStocks = [];
        sectors.forEach(s => allStocks.push(...s.stocks));

        // Initialize Tippy for all cells
        tippy('.hm-cell', {
            theme: 'heatmap',
            allowHTML: true,
            arrow: false,
            animation: 'shift-away',
            placement: 'right-start',
            followCursor: true,
            offset: [20, 20],
            content(reference) {
                const ticker = reference.getAttribute('data-ticker');
                const d = allStocks.find(c => c.ticker === ticker);
                if (!d) return '';

                const isNeg = d.pct.startsWith('-');
                const arrow = isNeg ? '↘' : '↗';
                const color = isNeg ? '#ef4444' : '#10b981';

                return `
                    <div class="hm-tooltip">
                        <div class="hmt-inner">
                            <div class="hmt-sector">${d.name} • ${d.industry || 'Market'}</div>
                            <div class="hmt-header">
                                <div class="hmt-logo"></div>
                                <div class="hmt-ticker-wrap">
                                    <span class="hmt-ticker">${d.ticker}</span>
                                    <span class="hmt-name">${d.name}</span>
                                </div>
                            </div>
                            <div class="hmt-price-row">
                                <span class="hmt-price">${d.price}</span>
                                <span class="hmt-change" style="color:${color}">${arrow} ${d.pct.replace('+','').replace('-','')}</span>
                            </div>
                            <p class="hmt-summary">${d.summary}</p>
                            <div class="hmt-footer">
                                <span class="hmt-time">12:49 ${region === 'US' ? 'EST' : 'IST'}</span>
                                <div class="hmt-sources">
                                    <i class="ph ph-intersect" style="font-size:14px; margin-right:4px;"></i>
                                    ${d.sources} sources
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => renderHeatmap('US'));
</script>
@endpush
