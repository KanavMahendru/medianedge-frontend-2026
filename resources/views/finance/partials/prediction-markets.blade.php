<div class="rp-section">
    <div class="rp-title">Prediction Markets</div>
    
    <div class="pm-container">
        @foreach($predictionMarkets as $index => $pm)
        <div class="pm-card {{ $index >= 2 ? 'pm-hidden' : '' }}">
            <div class="pm-question">{{ $pm['question'] }}</div>
            
            <div class="pm-options">
                @foreach($pm['options'] as $opt)
                <div class="pm-opt">
                    <div class="pm-opt-label">{{ $opt['label'] }}</div>
                    <div class="pm-opt-pct">{{ number_format($opt['pct'], 1) }}%</div>
                    <div class="pm-opt-delta {{ $opt['delta'] > 0 ? 'pos' : ($opt['delta'] < 0 ? 'neg' : 'flat') }}">
                        @if($opt['delta'] > 0)
                            <i class="ph ph-arrow-up-right"></i> {{ number_format($opt['delta'], 1) }}%
                        @elseif($opt['delta'] < 0)
                            <i class="ph ph-arrow-down-right"></i> {{ number_format(abs($opt['delta']), 1) }}%
                        @else
                            <i class="ph ph-minus"></i> 0.0%
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="pm-footer">
                <div class="pm-vol">US${{ $pm['volume'] }} vol.</div>
                <div class="pm-more">+{{ $pm['more_count'] }} on {{ $pm['source'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    @if(count($predictionMarkets) > 2)
    <div class="pm-see-more" onclick="togglePredictionMarkets(this)">
        See {{ count($predictionMarkets) - 2 }} more
    </div>
    @endif
</div>

<script>
function togglePredictionMarkets(btn) {
    const container = btn.previousElementSibling;
    const items = container.querySelectorAll('.pm-hidden');
    const isExpanded = btn.getAttribute('data-expanded') === 'true';
    
    items.forEach(el => {
        if (!isExpanded) {
            el.classList.add('pm-show');
        } else {
            el.classList.remove('pm-show');
        }
    });

    if (!isExpanded) {
        btn.textContent = 'See less';
        btn.setAttribute('data-expanded', 'true');
    } else {
        btn.textContent = 'See {{ count($predictionMarkets) - 2 }} more';
        btn.setAttribute('data-expanded', 'false');
    }
}
</script>
