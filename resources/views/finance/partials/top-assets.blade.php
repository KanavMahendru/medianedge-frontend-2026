<div class="asset-strip" id="assetStrip">
    <!-- JS will populate this instantly -->
</div>

<!-- Pagination row -->
<div class="asset-pagination" id="assetPagination" style="display: none;">
    <button class="ap-btn" id="apPrev" onclick="prevTopAssetPage()"><i class="ph ph-arrow-left"></i> Prev</button>
    <span class="ap-info" id="apInfo">Page 1 / 1</span>
    <button class="ap-btn" id="apNext" onclick="nextTopAssetPage()">Next <i class="ph ph-arrow-right"></i></button>
</div>

@push('scripts')
    <script>
        const DUMMY_ASSETS = @json($topAssets);
        let currentTopAssetRegion = 'US';
        let currentTopAssetPage = 0;
        let itemsPerPage = window.innerWidth <= 768 ? 2 : 4;
        let topAssetRenderTimer;

        window.addEventListener('resize', () => {
            const newItemsPerPage = window.innerWidth <= 768 ? 2 : 4;
            if (newItemsPerPage !== itemsPerPage) {
                itemsPerPage = newItemsPerPage;
                renderTopAssets(currentTopAssetRegion, 0);
            }
        });

        function renderTopAssets(region = currentTopAssetRegion, page = 0) {
            currentTopAssetRegion = region;
            currentTopAssetPage = page;

            const strip = document.getElementById('assetStrip');
            const pg = document.getElementById('assetPagination');

            // Hide pagination and show skeleton
            pg.style.display = 'none';
            strip.innerHTML = '';

            let skelHTML = '';
            for (let i = 0; i < 4; i++) {
                skelHTML += `
                    <div class="asset-item" id="skel-asset-${i}">
                        <div class="ai-row">
                            <div class="skel" style="width:70px; height:12px;"></div>
                            <div class="skel" style="width:40px; height:12px;"></div>
                        </div>
                        <div class="ai-row">
                            <div class="skel" style="width:60px; height:12px;"></div>
                            <div class="skel" style="width:30px; height:12px;"></div>
                        </div>
                        <div class="ai-chart">
                            <div class="skel" style="width:100%; height:40px; border-radius:4px;"></div>
                        </div>
                    </div>
                `;
            }
            strip.innerHTML = skelHTML;

            // Clear any existing timer to prevent overlapping renders
            clearTimeout(topAssetRenderTimer);

            topAssetRenderTimer = setTimeout(() => {
                strip.innerHTML = '';

                const data = DUMMY_ASSETS[region] || [];
                const totalPages = Math.ceil(data.length / itemsPerPage);

                // Handle Pagination Visibility
                if (data.length > itemsPerPage) {
                    pg.style.display = 'flex';
                    document.getElementById('apPrev').disabled = (currentTopAssetPage === 0);
                    document.getElementById('apNext').disabled = (currentTopAssetPage >= totalPages - 1);
                    document.getElementById('apInfo').textContent = `Page ${currentTopAssetPage + 1} / ${totalPages}`;
                }

                const slice = data.slice(currentTopAssetPage * itemsPerPage, (currentTopAssetPage + 1) * itemsPerPage);

                slice.forEach(asset => {
                    const isGain = asset.change_pct >= 0;
                    const cls = isGain ? 'pos' : 'neg';
                    const arrow = isGain ? '<i class="ph ph-arrow-up-right"></i>' : '<i class="ph ph-arrow-down-right"></i>';
                    const cid = 'ai-spark-' + asset.symbol;

                    const card = document.createElement('div');
                    card.className = 'asset-item';
                    card.innerHTML = `
                        <div class="ai-row">
                            <div class="ai-name">${asset.name}</div>
                            <div class="ai-pct ${cls}">${arrow} ${Math.abs(asset.change_pct).toFixed(2)}%</div>
                        </div>
                        <div class="ai-row">
                            <div class="ai-price">${asset.price}</div>
                            <div class="ai-abs ${cls}">${asset.change_abs}</div>
                        </div>
                        <div class="ai-chart"><canvas id="${cid}"></canvas></div>
                    `;
                    strip.appendChild(card);

                    // Wait a tiny bit for the DOM to append canvas before drawing
                    setTimeout(() => {
                        if (typeof buildSparkline === 'function') {
                            buildSparkline(cid, asset.history, isGain);
                        }
                    }, 10);
                });
            }, 300); // 300ms loading flash
        }

        function nextTopAssetPage() {
            const data = DUMMY_ASSETS[currentTopAssetRegion] || [];
            const totalPages = Math.ceil(data.length / itemsPerPage);
            if (currentTopAssetPage < totalPages - 1) {
                renderTopAssets(currentTopAssetRegion, currentTopAssetPage + 1);
            }
        }

        function prevTopAssetPage() {
            if (currentTopAssetPage > 0) {
                renderTopAssets(currentTopAssetRegion, currentTopAssetPage - 1);
            }
        }

        // Initial instant render
        document.addEventListener('DOMContentLoaded', () => {
            renderTopAssets('US', 0);
        });
    </script>
@endpush