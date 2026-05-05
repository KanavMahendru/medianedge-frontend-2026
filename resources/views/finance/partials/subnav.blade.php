<div class="topnav-wrapper">
    <!-- ══ TOP NAV ═══════════════════════════════════════════ -->
    <nav class="topnav">
        <div class="topnav-left">
            {{-- Market Selector — updates dynamically --}}
            <div class="market-selector" id="marketSelector">
                <span class="flag" id="selectorFlag">🇺🇸</span>
                <span id="selectorName">USA Markets</span>
              <!--  <span class="arrow">▾</span> -->
            </div>
            <div class="topnav-tabs">
                <div class="topnav-tab active" data-tab="US"     onclick="switchCountry('US',this)">USA</div>
                <div class="topnav-tab"        data-tab="India"  onclick="switchCountry('India',this)">INDIA</div>
                <div class="topnav-tab"        data-tab="Canada" onclick="switchCountry('Canada',this)">CANADA</div>
            </div>
        </div>
        <div class="topnav-right">
            <div class="sentiment-badge">
                <div class="bars">
                    <span style="height:4px;"></span>
                    <span style="height:6px;"></span>
                    <span style="height:5px;"></span>
                    <span style="height:8px;"></span>
                    <span style="height:6px;"></span>
                    <span style="height:9px;"></span>
                    <span style="height:7px;"></span>
                    <span style="height:10px;"></span>
                </div>
                Uncertain Sentiment
            </div>
            <div class="market-status" id="navMarketStatus">Markets Closed · 2 May 2026, IST</div>
        </div>
    </nav>
</div>
