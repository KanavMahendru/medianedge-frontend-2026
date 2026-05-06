{{-- Dynamic Top Assets Section - Theme Aware --}}
<div id="topAssetsContainer" class="asset-strip">
    {{-- Skeleton Loaders --}}
    @for ($i = 0; $i < 4; $i++)
        <div class="asset-item" style="opacity: 0.3;"></div>
    @endfor
</div>
<div class="asset-pagination" id="topAssetPagination" style="display: none;">
    <button class="ap-btn" id="tapPrev" onclick="topAssetPrev()">
        <i class="ph ph-caret-left"></i> Prev
    </button>
    <div class="ap-info" id="tapInfo">Page 1 / 1</div>
    <button class="ap-btn" id="tapNext" onclick="topAssetNext()">
        Next <i class="ph ph-caret-right"></i>
    </button>
</div>

<script>
    (function () {
        const TOP_ASSETS = @json($topAssets);
        const container = document.getElementById('topAssetsContainer');
        const pagination = document.getElementById('topAssetPagination');
        let currentRegion = 'US';
        let currentPage = 0;
        const perPage = 4;
        let topAssetRenderTimer;

        function renderTopAssets(region, page = 0) {
            if (!container) return;
            currentRegion = region;
            currentPage = page;
            clearTimeout(topAssetRenderTimer);

            const allData = TOP_ASSETS[region] || [];
            const totalPages = Math.ceil(allData.length / perPage);

            // Skeleton display
            container.innerHTML = Array(4).fill(0).map(() =>
                `<div class="asset-item" style="opacity: 0.3;"></div>`
            ).join('');

            topAssetRenderTimer = setTimeout(() => {
                const data = allData.slice(currentPage * perPage, (currentPage + 1) * perPage);
                container.innerHTML = '';

                data.forEach((asset, idx) => {
                    const isGain = asset.change_pct >= 0;
                    const color = isGain ? '#006a27' : '#aa3443';
                    const canvasId = `chart-${region}-${idx}`;

                    const card = document.createElement('div');
                    card.className = 'asset-item';

                    card.innerHTML = `
                        <div class="ai-top-part">
                            <div class="ai-left">
                                <div class="ai-name">${asset.name}</div>
                                <div class="ai-price">${asset.price}</div>
                            </div>
                            <div class="ai-right" style="color: ${color};">
                                <div class="ai-pct">
                                    <i class="ph ${isGain ? 'ph-arrow-up-right' : 'ph-arrow-down-right'}" style="font-size: 14px;"></i>
                                    <span>${Math.abs(asset.change_pct).toFixed(2)}%</span>
                                </div>
                                <div class="ai-abs">${asset.change_abs}</div>
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

                // Pagination visibility
                if (totalPages > 1) {
                    pagination.style.display = 'flex';
                    document.getElementById('tapPrev').disabled = currentPage === 0;
                    document.getElementById('tapNext').disabled = currentPage >= totalPages - 1;
                    document.getElementById('tapInfo').textContent = `Page ${currentPage + 1} / ${totalPages}`;
                } else {
                    pagination.style.display = 'none';
                }
            }, 300);
        }

        window.topAssetNext = function() {
            const allData = TOP_ASSETS[currentRegion] || [];
            if ((currentPage + 1) * perPage < allData.length) {
                renderTopAssets(currentRegion, currentPage + 1);
            }
        };

        window.topAssetPrev = function() {
            if (currentPage > 0) {
                renderTopAssets(currentRegion, currentPage - 1);
            }
        };

        window.renderTopAssets = renderTopAssets;
        document.addEventListener('DOMContentLoaded', () => renderTopAssets('US'));
    })();
</script>