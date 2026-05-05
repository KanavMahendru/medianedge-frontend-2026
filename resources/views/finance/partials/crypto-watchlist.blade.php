<div class="rp-section">
    <div class="rp-title">Popular Cryptocurrencies</div>
    
    <div class="crypto-card">
        @foreach($cryptoData as $crypto)
        <div class="cw-item">
            <div class="cw-logo" style="background: {{ 
                $crypto['name'] == 'Bitcoin' ? '#f59e0b' : 
                ($crypto['name'] == 'Ethereum' ? '#6366f1' : 
                ($crypto['name'] == 'Solana' ? '#14b8a6' : '#64748b'))
            }};">
                {{ $crypto['logo'] }}
            </div>
            <div class="cw-info">
                <div class="cw-name">{{ $crypto['name'] }}</div>
                <div class="cw-ticker">{{ $crypto['symbol'] }} · {{ $crypto['exchange'] }}</div>
            </div>
            <div class="cw-price-col">
                <div class="cw-price">US${{ $crypto['price'] }}</div>
                <div class="cw-change {{ $crypto['change_pct'] >= 0 ? 'pos' : 'neg' }}">
                    {{ $crypto['change_pct'] >= 0 ? '+' : '' }}{{ number_format($crypto['change_pct'], 2) }}%
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
