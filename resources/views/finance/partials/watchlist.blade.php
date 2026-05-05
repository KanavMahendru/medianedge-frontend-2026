<div class="rp-section">
    <div class="rp-title">
        Create Watchlist
        <div class="rp-settings"><i class="ph ph-sliders"></i></div>
    </div>
    <div class="watchlist-card">
        @foreach($watchlist as $item)
        <div class="wl-v2-item">
            <div class="wl-v2-logo" style="background: {{ 
                $item['logo'] == 'H' ? '#e11d48' : 
                ($item['logo'] == 'I' ? '#f97316' : 
                ($item['logo'] == 'T' ? '#3b82f6' : '#854d0e')) 
            }};">
                {{ $item['logo'] }}
            </div>
            <div class="wl-v2-info">
                <div class="wl-v2-name">{{ $item['name'] }}</div>
                <div class="wl-v2-ticker">{{ $item['symbol'] }} · {{ $item['exchange'] }}</div>
            </div>
            <div class="wl-v2-price-col">
                <div class="wl-v2-price">₹{{ $item['price'] }}</div>
                <div class="wl-v2-change {{ $item['change_pct'] >= 0 ? 'pos' : 'neg' }}">
                    {{ $item['change_pct'] >= 0 ? '+' : '' }}{{ number_format($item['change_pct'], 2) }}%
                </div>
            </div>
            <div class="wl-v2-star"><i class="ph ph-star"></i></div>
        </div>
        @endforeach
    </div>
</div>
