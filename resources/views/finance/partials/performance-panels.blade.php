{{-- LEFT BOX --}}
<div class="box" id="box-left">
    <div class="tabs">
        <button class="tab active" onclick="setTab('stocks', this, 'left')">Stocks</button>
        <button class="tab" onclick="setTab('crypto', this, 'left')">Crypto</button>
        <button class="tab" onclick="setTab('commodities', this, 'left')">Commodities</button>
    </div>

    <h3>LEFT PANEL · 1 DAY PERFORMANCE</h3>
    <div id="list-left"></div>
</div>

{{-- RIGHT BOX --}}
<div class="box" id="box-right" style="margin-top: 20px;">
    <div class="tabs">
        <button class="tab active" onclick="setTab('stocks', this, 'right')">Stocks</button>
        <button class="tab" onclick="setTab('crypto', this, 'right')">Crypto</button>
        <button class="tab" onclick="setTab('commodities', this, 'right')">Commodities</button>
    </div>

    <h3>RIGHT PANEL · 1 DAY PERFORMANCE</h3>
    <div id="list-right"></div>
</div>
