


    const LIVE_URL   = '{{ route("finance.live") }}';
    const REFRESH_MS = 60_000;
    const aiCharts   = {};   // sparkline chart instances keyed by canvasId

    // ── Country config ──────────────────────────────────────────────────────────
    const COUNTRY_CONFIG = {
        US:     { flag: '🇺🇸', label: 'USA Markets',    short: 'US',     currency: { symbol: '$',  rate: () => 1 } },
        India:  { flag: '🇮🇳', label: 'India Markets',  short: 'IN',     currency: { symbol: '₹',  rate: () => cachedFx.INR } },
        Canada: { flag: '🇨🇦', label: 'Canada Markets', short: 'CA',     currency: { symbol: 'C$', rate: () => cachedFx.CAD } },
    };

    // ── State ───────────────────────────────────────────────────────────────────
    let cachedPortfolio = null;
    let cachedFx        = { INR: 83.5, CAD: 1.36 };
    let activeCountry   = 'US';
    let assetPage       = 0;
    const assetPerPage  = 4;
    let loadingLive     = false;

    // ── Country Switch (called from nav tabs) ───────────────────────────────────
    function switchCountry(country, el) {
        activeCountry = country;
        assetPage     = 0;

        document.querySelectorAll('.topnav-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');

        const cfg = COUNTRY_CONFIG[country];
        document.getElementById('selectorFlag').textContent = cfg.flag;
        document.getElementById('selectorName').textContent = cfg.label;

        if (cachedPortfolio) renderAssetCards(cachedPortfolio);
        
        // Render top asset dummy data mapped by country
        if (typeof renderTopAssets === 'function') {
            const mappedRegion = country === 'US' ? 'US' : country;
            renderTopAssets(mappedRegion, 0);
        }
    }

    // ── Format price ────────────────────────────────────────────────────────────
    function fmtP(usdVal) {
        if (usdVal == null) return '—';
        const { symbol, rate } = COUNTRY_CONFIG[activeCountry].currency;
        const v = usdVal * rate();
        return symbol + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ── Sparkline (minimal, image-style) ────────────────────────────────────────
    const dashedLinePlugin = {
        id: "dashedLinePlugin",
        beforeDraw: (chart) => {
            const ctx = chart.ctx;
            const yAxis = chart.scales.y;
            const xAxis = chart.scales.x;
            if (chart.data.datasets[0].data.length > 0) {
                const firstVal = chart.data.datasets[0].data[0];
                const yPos = yAxis.getPixelForValue(firstVal);
                ctx.save();
                ctx.beginPath();
                ctx.setLineDash([4, 4]);
                ctx.moveTo(xAxis.left, yPos);
                ctx.lineTo(xAxis.right, yPos);
                ctx.strokeStyle = "rgba(150, 150, 150, 0.4)";
                ctx.lineWidth = 1;
                ctx.stroke();
                ctx.restore();
            }
        }
    };

    function buildSparkline(canvasId, historyUSD, isGain) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        if (aiCharts[canvasId]) aiCharts[canvasId].destroy();

        const rate    = COUNTRY_CONFIG[activeCountry].currency.rate();
        const history = historyUSD.map(p => p * rate);
        
        // Theme aware colors (fallback to standard if variables fail, but we use canvas drawing)
        const isDark = document.body.classList.contains("dark-theme");
        const color = isGain ? (isDark ? "#34d399" : "#10b981") : (isDark ? "#f87171" : "#ef4444");
        
        const ctx = canvas.getContext("2d");
        let gradient = ctx.createLinearGradient(0, 0, 0, 40);
        if (isGain) {
            gradient.addColorStop(0, "rgba(16, 185, 129, 0.25)");
            gradient.addColorStop(1, "rgba(16, 185, 129, 0)");
        } else {
            gradient.addColorStop(0, "rgba(239, 68, 68, 0.25)");
            gradient.addColorStop(1, "rgba(239, 68, 68, 0)");
        }

        aiCharts[canvasId] = new Chart(canvas, {
            type: "line",
            data: {
                labels: history.map((_, i) => i),
                datasets: [{
                    data: history,
                    borderColor: color,
                    borderWidth: 1.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false } },
                layout: { padding: 0 },
            },
            plugins: [dashedLinePlugin]
        });
    }

    // ── Render Asset Cards ──────────────────────────────────────────────────────
    function renderAssetCards(data) {
        return; // Disabled to show dummy data
        const strip = document.getElementById("assetStrip");
        strip.innerHTML = "";

        const totalPages = Math.ceil(data.length / assetPerPage);
        const slice      = data.slice(assetPage * assetPerPage, (assetPage + 1) * assetPerPage);
        const cur        = COUNTRY_CONFIG[activeCountry].currency;

        slice.forEach(s => {
            const gain   = s.gain_pct;
            const isGain = gain != null && gain >= 0;
            const cls    = gain == null ? "neu" : isGain ? "pos" : "neg";
            const arrow  = gain == null ? "" : isGain ? "<i class=\"ph ph-arrow-up-right\"></i>" : "<i class=\"ph ph-arrow-down-right\"></i>";
            const sign   = gain != null && gain > 0 ? "" : ""; // sign already in logic
            const pctTxt = gain != null ? `${arrow} ${Math.abs(gain).toFixed(2)}%` : "—";

            const curP   = s.current_price != null ? fmtP(s.current_price) : "—";
            const absDiff = s.current_price != null ? (s.current_price - s.entry_price) * cur.rate() : null;
            const absTxt  = absDiff != null
                ? `${absDiff >= 0 ? "+" : ""}${cur.symbol}${Math.abs(absDiff).toFixed(2)}`
                : "—";
            const absCls  = absDiff == null ? "neu" : absDiff >= 0 ? "pos" : "neg";

            const cid = `ai-spark-${s.symbol}`;

            const card = document.createElement("div");
            card.className = "asset-item";
            card.innerHTML = `
                <div class="ai-row">
                    <div class="ai-name">${s.name || s.symbol}</div>
                    <div class="ai-pct ${cls}">${pctTxt}</div>
                </div>
                <div class="ai-row">
                   <div class="ai-price">${curP}</div>
                   <div class="ai-abs ${absCls}">${absTxt}</div>
                </div>
                <div class="ai-chart"><canvas id="${cid}"></canvas></div>`;
            strip.appendChild(card);

            setTimeout(() => buildSparkline(cid, s.history || [], isGain), 0);
        });

        // Update label + pagination
        const cfg = COUNTRY_CONFIG[activeCountry];
        document.getElementById("assetsLabel").textContent =
            `Portfolio · ${cfg.label.replace(" Markets","")} (${cfg.currency.symbol === "$" ? "USD" : cfg.currency.symbol === "₹" ? "INR" : "CAD"})`;
        document.getElementById("assetsRegion").textContent = cfg.short;

        // Pagination controls
        const pg = document.getElementById("assetPagination");
        if (totalPages > 1) {
            pg.style.display = "flex";
            document.getElementById("apPrev").disabled = assetPage === 0;
            document.getElementById("apNext").disabled = assetPage >= totalPages - 1;
            document.getElementById("apInfo").textContent = `Page ${assetPage + 1} / ${totalPages}`;
        } else {
            pg.style.display = "none";
        }
    }

    function assetNextPage() { if (cachedPortfolio) { assetPage++; renderAssetCards(cachedPortfolio); } }
    function assetPrevPage() { if (cachedPortfolio && assetPage > 0) { assetPage--; renderAssetCards(cachedPortfolio); } }

    // ── Fetch ────────────────────────────────────────────────────────────────────
    async function fetchLiveData() {
        if (loadingLive) return;
        loadingLive = true;
        document.getElementById('assetRefreshBtn').classList.add('loading');

        try {
            const r    = await fetch(LIVE_URL, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const json = await r.json();
            if (!json.success) throw new Error();

            // Remove skeletons
            document.querySelectorAll('[id^="skel-asset-"]').forEach(el => el.remove());

            cachedPortfolio = json.data;
            if (json.fx) cachedFx = json.fx;

            renderAssetCards(cachedPortfolio);

            if (json.market_status) {
                const ms   = json.market_status;
                const open = Object.values(ms).some(m => m.open);
                document.getElementById('navMarketStatus').textContent =
                    `${open ? 'Markets Open' : 'Markets Closed'} · ${ms.India?.time || ''}`;
            }

            const ts = new Date().toLocaleTimeString('en-IN', { hour12: true, timeZone: 'Asia/Kolkata' });
            document.getElementById('lastUpdatedTxt').textContent = `Updated ${ts}`;

        } catch (e) {
            document.getElementById('lastUpdatedTxt').textContent = 'Error — retrying…';
        } finally {
            loadingLive = false;
            document.getElementById('assetRefreshBtn').classList.remove('loading');
        }
    }

    fetchLiveData();
    setInterval(fetchLiveData, REFRESH_MS);
    

    const DATA = @json($data);

    let panelState = { left: 'stocks', right: 'stocks' };

    function setTab(tab, el, side) {
        panelState[side] = tab;
        document.querySelectorAll(`#box-${side} .tab`).forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        renderPanel(side);
    }

    function barColor(v) {
        if (v < 0) return '#ef4444';
        if (v < 1) return '#facc15';
        return '#22c55e';
    }

    function renderPanel(side) {
        const list = document.getElementById(`list-${side}`);
        list.innerHTML = '';
        let data = DATA[panelState[side]] || [];
        if (!data.length) { list.innerHTML = `<div style="color:#64748b;font-size:12px;padding:10px;">No data</div>`; return; }

        data.sort((a, b) => b.value - a.value);
        const max = Math.max(...data.map(d => Math.abs(d.value))) || 1;

        data.forEach(d => {
            const width = (Math.abs(d.value) / max) * 100;
            list.innerHTML += `
                <div class="row">
                    <div class="name">${d.name}</div>
                    <div class="bar"><div class="fill" style="width:${width}%;background:${barColor(d.value)};"></div></div>
                    <div class="val" style="color:${barColor(d.value)}">${d.value > 0 ? '+' : ''}${d.value.toFixed(2)}%</div>
                </div>`;
        });
    }

    function switchGLTab(el, id) {
        document.querySelectorAll('.gl-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    renderPanel('left');
    renderPanel('right');
    
