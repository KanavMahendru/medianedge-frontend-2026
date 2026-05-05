<div class="ms-container">
    <div class="ms-header">
        <span class="ms-title">Market Summary</span>
        <span class="ms-time">Updated 3 minutes ago</span>
    </div>
    <div class="ms-box" id="marketSummaryBox">
        <!-- JS will populate accordion items here -->
    </div>
</div>

@push('scripts')
<script>
    const DUMMY_SUMMARY = @json($marketSummary);
    let msRenderTimer;

    function renderMarketSummary(region = 'US') {
        const box = document.getElementById('marketSummaryBox');
        
        // Skeletons for Market Summary
        let skelHTML = '';
        for (let i = 0; i < 3; i++) {
            skelHTML += `
                <div class="ms-item">
                    <div class="ms-item-header">
                        <div class="skel" style="width: 60%; height: 16px; border-radius: 4px;"></div>
                        <i class="ph ph-caret-down ms-caret"></i>
                    </div>
                </div>
            `;
        }
        box.innerHTML = skelHTML;

        clearTimeout(msRenderTimer);

        msRenderTimer = setTimeout(() => {
            const data = DUMMY_SUMMARY[region] || DUMMY_SUMMARY['US'];
            let html = '';
            
            data.forEach((item, index) => {
                const isOpen = index === 0 ? 'open' : '';
                html += `
                    <div class="ms-item ${isOpen}">
                        <div class="ms-item-header" onclick="toggleMsItem(this.parentElement)">
                            <span>${item.title}</span>
                            <i class="ph ph-caret-down ms-caret"></i>
                        </div>
                        <div class="ms-item-body">
                            <p>${item.content}</p>
                            <div class="ms-sources">
                                <div class="ms-source-icons">
                                    <span class="ms-icon red"></span>
                                    <span class="ms-icon gray"></span>
                                </div>
                                ${item.sources} sources
                            </div>
                        </div>
                    </div>
                `;
            });
            
            box.innerHTML = html;
        }, 300); // 300ms flash to match top-assets
    }

    function toggleMsItem(el) {
        // Optional: Close all other items before opening this one (accordion behavior)
        // document.querySelectorAll('.ms-item').forEach(item => {
        //     if (item !== el) item.classList.remove('open');
        // });
        
        el.classList.toggle('open');
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        renderMarketSummary('US');
    });
</script>
@endpush
