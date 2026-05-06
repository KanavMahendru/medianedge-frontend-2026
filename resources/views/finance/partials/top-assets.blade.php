{{-- Dynamic Top Assets Section - Theme Aware --}}
<div id="topAssetsContainer" class="asset-strip">
    {{-- Skeleton Loaders --}}
    @for ($i = 0; $i < 4; $i++)
        <div class="asset-item" style="opacity: 0.3;"></div>
    @endfor
</div>

<script>
    (function () {
        const TOP_ASSETS = @json($topAssets);
        const container = document.getElementById('topAssetsContainer');
        let topAssetRenderTimer;

        function renderTopAssets(region) {
            if (!container) return;
            clearTimeout(topAssetRenderTimer);

            // Skeleton display
            container.innerHTML = Array(4).fill(0).map(() =>
                `<div class="asset-item" style="opacity: 0.3;"></div>`
            ).join('');

            topAssetRenderTimer = setTimeout(() => {
                const data = (TOP_ASSETS[region] || []).slice(0, 4);
                container.innerHTML = '';

                data.forEach((asset, idx) => {
                    const isGain = asset.change_pct >= 0;
                    const color = isGain ? '#34d399' : '#f87171';
                    const sign = isGain ? '↑' : '↓';
                    const canvasId = `chart-${region}-${idx}`;

                    const card = document.createElement('div');
                    card.className = 'asset-item';

                    card.innerHTML = `
                        <div style="width: 100%;">
                            <div class="ai-row">
                                <div class="ai-name">${asset.name}</div>
                                <div class="ai-pct" style="color: ${color};">
                                    <span>${sign}</span>
                                    <span>${Math.abs(asset.change_pct).toFixed(2)}%</span>
                                </div>
                            </div>
                            <div class="ai-row">
                                <div class="ai-price">${asset.price}</div>
                                <div class="ai-abs" style="color: ${color}; opacity: 0.9;">${asset.change_abs}</div>
                            </div>
                        </div>
                        <div class="ai-chart">
                            <canvas id="${canvasId}"></canvas>
                        </div>
                    `;
                    container.appendChild(card);

                    if (typeof buildSparkline === 'function') {
                        setTimeout(() => buildSparkline(canvasId, asset.history, isGain), 50);
                    }
                });
            }, 300);
        }

        window.renderTopAssets = renderTopAssets;
        document.addEventListener('DOMContentLoaded', () => renderTopAssets('US'));
    })();
</script>