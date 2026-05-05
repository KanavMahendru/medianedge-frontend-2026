<div class="rp-section">
    <div class="gl-tabs-v2">
        <div class="gl-tab-v2 active" data-tab="gainers" onclick="switchGLTabV2(this, 'gainers')">Gainers</div>
        <div class="gl-tab-v2" data-tab="losers" onclick="switchGLTabV2(this, 'losers')">Losers</div>
        <div class="gl-tab-v2" data-tab="active" onclick="switchGLTabV2(this, 'active')">Active</div>
    </div>
    
    <div class="market-tabs-card">
        <div id="marketTabsContent">
            <!-- JS will populate this -->
        </div>
        <div class="mt-footer">
            <a href="#" class="mt-see-all">See all <i class="ph ph-caret-right"></i></a>
        </div>
    </div>
</div>

<script>
    const MARKET_TABS_DATA = @json($marketTabs);

    function renderMarketTab(category) {
        const container = document.getElementById('marketTabsContent');
        const data = MARKET_TABS_DATA[category] || [];
        
        container.innerHTML = '';
        
        data.forEach(item => {
            const isGain = item.change_pct >= 0;
            const itemHtml = `
                <div class="mt-item">
                    <div class="mt-logo" style="background: ${
                        item.logo == 'C' ? '#065f46' : 
                        (item.logo == 'I' ? '#854d0e' : 
                        (item.logo == 'M' ? '#1e40af' : 
                        (item.logo == 'V' ? '#991b1b' : 
                        (item.logo == 'S' ? '#b91c1c' : '#334155'))))
                    }; color: #fff;">
                        ${item.logo}
                    </div>
                    <div class="mt-info">
                        <div class="mt-name">${item.name}</div>
                        <div class="mt-ticker">${item.symbol} · ${item.exchange}</div>
                    </div>
                    <div class="mt-price-col">
                        <div class="mt-price">₹${item.price}</div>
                        <div class="mt-change ${isGain ? 'pos' : 'neg'}">
                            ${isGain ? '+' : ''}${item.change_pct.toFixed(2)}%
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += itemHtml;
        });
    }

    function switchGLTabV2(el, category) {
        document.querySelectorAll('.gl-tab-v2').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        renderMarketTab(category);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderMarketTab('gainers');
    });
</script>
