{{-- resources/views/dashboard/stocks.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portfolio · Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js + Treemap -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@2"></script>
        
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

        :root{
            --bg:#ffffff;--surface:#f9fafb;--border:#e5e7eb;--border-light:#f0f0f0;
            --text:#111827;--sub:#6b7280;--green:#16a34a;--red:#dc2626;
            --font:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
        }

        body{/*background:var(--bg);*/background:#f7f9fb; color:var(--text);font-family:var(--font);font-size:14px;min-height:100vh;-webkit-font-smoothing:antialiased;}

        /* ── NAV ── */
        .nav{
            display:flex;align-items:center;justify-content:space-between;
            padding:0 20px;height:50px;border-bottom:1px solid var(--border);
            background:#fff;position:sticky;top:0;z-index:60;
        }
        .nav-brand{display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px;}
        .nav-logo{width:26px;height:26px;background:#111827;border-radius:6px;display:flex;align-items:center;justify-content:center;}
        .nav-logo svg{color:#fff;}
        .nav-right{display:flex;align-items:center;gap:18px;}
        .mpill{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--sub);font-weight:500;}
        .mpill .dot{width:7px;height:7px;border-radius:50%;}
        .dot.open{background:#16a34a;} .dot.closed{background:#9ca3af;}
        .sentiment{display:flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px 3px 8px;font-size:11px;font-weight:600;color:#16a34a;}
        .mini-bars{display:flex;gap:1.5px;align-items:flex-end;}
        .mini-bars span{width:2.5px;background:#16a34a;border-radius:1px;display:block;}

        /* ── STATUS BAR ── */
        .statusbar{
            display:flex;justify-content:space-between;align-items:center;
            padding:4px 20px;font-size:11px;color:var(--sub);
            background:var(--surface);border-bottom:1px solid var(--border);
        }

        /* ── COUNTRY TABS ── */
        .tab-bar{
            display:flex;align-items:center;gap:0;
            padding:0 20px;
            border-bottom:1px solid var(--border);
            background:#fff;
        }
        .tab{
            padding:11px 18px;font-size:13px;font-weight:500;
            color:var(--sub);cursor:pointer;border:none;background:none;
            border-bottom:2px solid transparent;margin-bottom:-1px;
            transition:all .15s;display:flex;align-items:center;gap:6px;
        }
        .tab:hover{color:var(--text);}
        .tab.active{color:var(--text);border-bottom-color:var(--text);font-weight:600;}
        .tab .flag{font-size:15px;line-height:1;}
        .tab .currency-badge{
            font-size:10px;font-weight:600;background:var(--surface);
            border:1px solid var(--border);border-radius:4px;padding:1px 5px;
            color:var(--sub);
        }
        .tab.active .currency-badge{background:#111827;color:#fff;border-color:#111827;}

        /* ── STAT BOXES ── */
        .stat-section{background:#f7f9fb;padding:24px 0 16px;}
        .stat-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;max-width:1140px;margin:0 auto;padding:0 20px;}
        .stat-box{
            background:#fff;
            border:1px solid rgba(15,23,42,.08);
            border-radius:24px;
            box-shadow:0 18px 40px rgba(15,23,42,.05);
            padding:20px 22px 18px;
            min-height:190px;
            display:flex;
            flex-direction:column;
            transition:transform .24s ease,box-shadow .24s ease,border-color .24s ease;
        }
        .stat-box:hover{transform:translateY(-3px);border-color:rgba(15,23,42,.16);box-shadow:0 28px 60px rgba(15,23,42,.09);}
        .stat-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:18px;}
        .stat-name{font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.18em;text-transform:uppercase;}
        .stat-pct{font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:rgba(243,244,246,.9);white-space:nowrap;}
        .stat-pct.pos{color:#115e32;background:rgba(22,163,74,.12);} .stat-pct.neg{color:#991b1b;background:rgba(220,38,38,.12);} .stat-pct.neu{color:var(--sub);background:rgba(148,163,184,.14);}
        .stat-prices{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;gap:16px;flex-wrap:wrap;}
        .stat-cur{font-size:26px;font-weight:700;color:#111827;line-height:1;}
        .stat-abs{font-size:13px;font-weight:600;color:var(--sub);}
        .stat-abs.pos{color:#15803d;} .stat-abs.neg{color:#b91c1c;} .stat-abs.neu{color:var(--sub);}
        .stat-aux{font-size:12px;color:#6b7280;font-weight:500;} 
        .stat-chart-wrap{flex:1;position:relative;height:78px;border-radius:20px;overflow:hidden;background:#f3f4f6;}
        .stat-chart-wrap canvas{display:block;width:100%!important;height:78px!important;}

        /* ── TABLE ── */
        .content{max-width:1140px;margin:0 auto;padding:24px 20px 60px;background:#f7f9fb;}
        .table-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.04);}
        table{width:100%;border-collapse:collapse;}
        thead tr{border-bottom:1.5px solid var(--border);}
        thead th{padding:11px 18px;text-align:left;font-size:13px;font-weight:500;color:var(--sub);white-space:nowrap;background:#fff;}
        .th-btn{display:inline-flex;align-items:center;gap:4px;}
        .th-btn .arr{opacity:.4;} .th-btn .arr.active{opacity:1;color:var(--text);}
        tbody tr{border-bottom:1px solid var(--border-light);transition:background .1s;}
        tbody tr:last-child{border-bottom:none;}
        tbody tr:nth-child(even){background:#fafafa;}
        tbody tr:hover{background:#f5f7ff;}
        tbody td{padding:15px 18px;vertical-align:middle;}
        .sym-ticker{font-size:15px;font-weight:700;letter-spacing:-.01em;}
        .sym-exch{font-size:11.5px;font-weight:500;color:var(--sub);margin-left:6px;}
        .sym-name{font-size:12.5px;color:var(--sub);margin-top:2px;line-height:1.35;}
        .date-cell{display:flex;align-items:center;gap:7px;font-size:14px;}
        .cal{width:28px;height:28px;border:1.5px solid #d1d5db;border-radius:5px;overflow:hidden;display:flex;flex-direction:column;flex-shrink:0;background:#fff;}
        .cal-head{height:9px;background:#e5e7eb;border-bottom:1px solid #d1d5db;display:flex;align-items:center;padding:0 3px;gap:2px;}
        .cal-head b{width:3px;height:3px;background:#9ca3af;border-radius:50%;display:block;}
        .cal-body-i{flex:1;display:flex;align-items:center;justify-content:center;}
        .cal-body-i svg{width:11px;height:11px;color:#9ca3af;}
        .price{font-size:14px;}
        .gain{font-size:15px;font-weight:600;}
        .gain.pos{color:#15803d;} .gain.neg{color:#b91c1c;} .gain.neu{color:var(--sub);}

        /* ── SKELETON ── */
        .skel{background:linear-gradient(90deg,#f0f0f0 25%,#e4e4e4 50%,#f0f0f0 75%);background-size:200% 100%;animation:sh 1.4s infinite;border-radius:4px;display:inline-block;}
        @keyframes sh{0%{background-position:200% 0}100%{background-position:-200% 0}}

        /* ── BOTTOM BAR ── */
        .bottom-bar{display:flex;justify-content:space-between;align-items:center;margin-top:12px;}
        .bottom-bar span{font-size:12px;color:var(--sub);}
        .refresh-btn{display:flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:8px;background:#fff;padding:5px 14px;font-size:12.5px;font-family:var(--font);color:var(--sub);cursor:pointer;transition:all .15s;}
        .refresh-btn:hover{border-color:#9ca3af;color:var(--text);}
        .refresh-btn.loading svg{animation:spin .7s linear infinite;}

        /* ── PAGINATION ── */
        #statPagination{display:flex;justify-content:center;align-items:center;gap:12px;padding:16px 0;}
        .pagination-btn{border:1px solid rgba(15,23,42,.12);background:#fff;color:#111827;padding:10px 18px;border-radius:999px;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s ease,transform .2s ease;box-shadow:0 10px 25px rgba(15,23,42,.06);min-width:105px;}
        .pagination-btn:hover:not(:disabled){background:#111827;color:#fff;border-color:rgba(255,255,255,.12);transform:translateY(-1px);}
        .pagination-btn:disabled{opacity:.5;cursor:not-allowed;background:#f8fafb;border-color:rgba(15,23,42,.08);box-shadow:none;}
        .pagination-page{font-size:13px;color:#475569;font-weight:600;min-width:120px;text-align:center;}
        @keyframes spin{to{transform:rotate(360deg)}}

        .stat-header{
    max-width:1140px;
    margin:0 auto 16px;
    padding:0 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.country-switch{
    display:flex;
    gap:10px;
    background:#f3f4f6;
    padding:6px;
    border-radius:999px;
    border:1px solid #e5e7eb;
}

.cbtn{
    border:none;
    background:transparent;
    padding:8px 14px;
    border-radius:999px;
    font-size:12.5px;
    font-weight:600;
    color:#6b7280;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
    transition:all .2s ease;
}

.cbtn small{
    font-size:10px;
    opacity:.7;
    font-weight:600;
}

.cbtn:hover{
    color:#111827;
}

.cbtn.active{
    background:#111827;
    color:#fff;
    box-shadow:0 10px 20px rgba(0,0,0,.15);
}

.stat-title{
    font-size:13px;
    font-weight:700;
    color:#111827;
    letter-spacing:.3px;
}
    </style>

    <style>
        /* body{
            background:#0b1220;
            color:#fff;
            font-family:Inter,sans-serif;
            padding:20px;
        } */

        .box{
           /* max-width:1000px;
            margin:10px 50px; */
            background:#fff;
            padding:18px;
            border-radius:14px;
        }

        /* Tabs */
        .tabs{
            display:flex;
            gap:10px;
            margin-bottom:15px;
        }

        .tab{
            padding:6px 14px;
            border-radius:999px;
            border:1px solid #334155;
            background:#0f172a;
            cursor:pointer;
            font-size:12px;
            color:#94a3b8;
        }

        .tab.active{
            background:#22c55e;
            color:#000;
            font-weight:700;
        }

        /* Row */
        .row{
            display:flex;
            align-items:center;
            gap:10px;
            margin:10px 0;
        }

        .name{
            width:80px;
            font-size:12px;
            color:#092444;
        }

        .bar{
            flex:1;
            height:10px;
            background:#1e293b;
            border-radius:6px;
            overflow:hidden;
        }

        .fill{
            height:100%;
            width:0%;
            transition:width 1s ease;
            border-radius:6px;
        }

        .val{
            width:70px;
            text-align:right;
            font-size:12px;
        }

        .grid-wrap{
   /* display:grid; */
    background:#f7f9fb;
    grid-template-columns:1fr 1fr;
    gap:16px;
   /* max-width:1200px; */
   /* margin:auto; */
}


h3{
    font-size:12px;
    color:#94a3b8;
    margin:10px 0;
    letter-spacing:1px;
}

.news-wrap{
    max-width:1140px;
    margin:30px auto;
   /* padding:0 20px; */
}

.news-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
}

.news-header h3{
    font-size:14px;
    font-weight:700;
    color:#111827;
}

.news-meta{
    font-size:12px;
    color:#6b7280;
}

.news-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
}

.news-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:14px;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
    transition:.2s ease;
}

.news-card:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

.news-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:11px;
    color:#6b7280;
    margin-bottom:10px;
}

.news-title{
    font-size:14px;
    font-weight:700;
    color:#111827;
    margin-bottom:8px;
    line-height:1.4;
}

.news-desc{
    font-size:12px;
    color:#6b7280;
    line-height:1.5;
}

        .dashboard-summary{
           /* max-width:1140px; */
           max-width:1250px;
            margin:32px auto 0;
            display:grid;
            grid-template-columns:1.75fr 1fr;
            gap:18px;
            padding:0 20px;
        }
        .summary-left{display:grid;gap:18px;min-width: 890px;}
        .top-assets{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
        .asset-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px;box-shadow:0 8px 20px rgba(15,23,42,.05);}
        .asset-label{font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.16em;text-transform:uppercase;margin-bottom:10px;}
        .asset-title{font-size:15px;font-weight:700;color:#111827;margin-bottom:4px;}
        .asset-values{display:flex;align-items:center;justify-content:space-between;gap:8px;}
        .asset-value{font-size:14px;font-weight:700;color:#111827;}
        .asset-change{font-size:12px;font-weight:700;color:#16a34a;}
        .asset-value-small{font-size:12px;color:#6b7280;}

        .market-summary-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:24px;box-shadow:0 8px 24px rgba(15,23,42,.05);}
        .summary-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;}
        .summary-header h3{font-size:15px;font-weight:700;color:#111827;}
        .summary-header span{font-size:12px;color:#6b7280;}
        .summary-copy{font-size:13px;color:#374151;line-height:1.75;margin-bottom:16px;}
        .summary-list{display:grid;gap:10px;margin-bottom:18px;}
        .summary-list li{font-size:13px;color:#4b5563;line-height:1.7;}
        .summary-meta{display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#6b7280;}
        .summary-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:#f8fafc;border:1px solid #e5e7eb;color:#475569;font-weight:600;}

        .summary-right{display:grid;gap:16px;}
        .sidebar-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px;box-shadow:0 8px 20px rgba(15,23,42,.05);}
        .sidebar-card h3{font-size:14px;font-weight:700;color:#111827;margin-bottom:10px;}
        .sidebar-card small{font-size:11px;color:#6b7280;}
        .watchlist-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 0;border-top:1px solid #f1f5f9;}
        .watchlist-item:first-child{border-top:none;}
        .watchlist-title{font-size:13px;font-weight:700;color:#111827;}
        .watchlist-sub{font-size:11px;color:#6b7280;}
        .watchlist-change{font-size:12px;font-weight:700;}
        .watchlist-change.pos{color:#16a34a;}
        .watchlist-change.neg{color:#dc2626;}
        .prediction-list{display:grid;gap:10px;margin-top:14px;}
        .prediction-row{display:flex;justify-content:space-between;gap:10px;}
        .prediction-option{flex:1;background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:10px;}
        .prediction-option strong{display:block;font-size:13px;color:#111827;margin-bottom:4px;}
        .prediction-option span{font-size:12px;color:#6b7280;}
        .stats-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px;}
        .stats-card{background:#f8fafc;border-radius:14px;padding:12px;}
        .stats-card strong{display:block;font-size:13px;color:#111827;margin-bottom:4px;}
        .stats-card span{font-size:12px;color:#6b7280;}
        .heatmap-title{max-width:1140px;margin:30px auto 10px;padding:0 20px;display:flex;justify-content:space-between;align-items:center;gap:10px;font-size:15px;font-weight:700;color:#111827;}
        .heatmap-expand{font-size:12px;color:#6b7280;cursor:pointer;}

        .post-heatmap-grid{/* max-width:1140px;*/max-width:1250px; margin:24px auto 0;/*padding:0 20px;*/display:grid;grid-template-columns:1.75fr 1fr;gap:18px;}
        .post-main{display:grid;gap:18px;min-width: 890px;}
        .post-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:22px;box-shadow:0 8px 24px rgba(15,23,42,.05);}
        .post-card h3{font-size:15px;font-weight:700;color:#111827;display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:16px;}
        .post-card h3 span{font-size:12px;color:#6b7280;font-weight:500;}
        .development-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;}
        .dev-card{background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:16px;}
        .dev-card h4{font-size:13px;font-weight:700;color:#111827;margin-bottom:8px;}
        .dev-card p{font-size:12px;color:#475569;line-height:1.6;}
        .research-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:16px;}
        .research-card{background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:18px;}
        .research-card h4{font-size:13px;font-weight:700;color:#111827;margin-bottom:10px;}
        .research-card p{font-size:12px;color:#475569;line-height:1.7;margin-bottom:14px;}
        .research-actions{display:flex;gap:10px;flex-wrap:wrap;}
        .research-btn{border:1px solid #e5e7eb;background:#fff;color:#111827;padding:10px 14px;border-radius:12px;font-size:12px;font-weight:700;cursor:pointer;transition:all .15s;}
        .research-btn:hover{background:#f1f5f9;}
        .standout-card{display:grid;gap:16px;}
        .stock-highlight{background:#f8fafc;border:1px solid #e5e7eb;border-radius:18px;padding:18px;}
        .stock-highlight h4{font-size:14px;font-weight:700;color:#111827;margin-bottom:10px;}
        .stock-highlight .stock-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;color:#6b7280;margin-bottom:16px;}
        .stock-highlight .stock-chart{position:relative;height:120px;border-radius:16px;background:#fff;overflow:hidden;border:1px solid #e5e7eb;}
        .stock-highlight .stock-chart canvas{width:100%!important;height:100%!important;display:block;}
        .stock-highlight .stock-chart .chart-note{position:absolute;right:14px;bottom:12px;background:rgba(255,255,255,0.92);border:1px solid rgba(229,231,235,0.95);border-radius:999px;padding:8px 12px;font-size:11px;color:#475569;box-shadow:0 10px 30px rgba(15,23,42,.08);}
        .stock-highlight .stock-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:16px;}
        .stock-stat{background:#fff;border-radius:14px;padding:12px;border:1px solid #e5e7eb;}
        .stock-stat strong{display:block;font-size:13px;color:#111827;margin-bottom:6px;}
        .stock-stat span{font-size:12px;color:#6b7280;}

        .sidebar-small-card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px;box-shadow:0 8px 24px rgba(15,23,42,.05);}
        .crypto-row{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 0;border-top:1px solid #f1f5f9;}
        .crypto-row:first-child{border-top:none;}
        .crypto-title{font-size:13px;font-weight:700;color:#111827;}
        .crypto-value{font-size:12px;color:#6b7280;}
        .crypto-change{font-size:12px;font-weight:700;}
        .crypto-change.pos{color:#16a34a;}
        .crypto-change.neg{color:#dc2626;}

        /* container */
        #heatmapContainer {
            position: relative;
           /* width: 925px; */
            padding: 0 20px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        #heatmapChart {
            max-width: 600px;
            margin: 0 auto;
            height: 400px;
            display: block;
        }

/* hover card */
#hoverCard {
    position: fixed;
    display: none;
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    width: 280px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    z-index: 1000;
    pointer-events: none;
}

#hc-sector {
    font-size: 11px;
    color: #9ca3af;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

#hc-name {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
}

#hc-price {
    margin-top: 8px;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

#hc-price-change {
    font-size: 13px;
    margin-top: 4px;
    font-weight: 600;
}

#hc-desc {
    margin-top: 10px;
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
}



    </style>
</head>
<body>

{{-- ══ NAV ══ --}}
<nav class="nav">
    <div class="nav-brand">
        <div class="nav-logo">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 3h7v7H3zm11 0h7v7h-7zM3 14h7v7H3zm14 3.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"/>
            </svg>
        </div>
        Finance
    </div>
    <div class="nav-right">
        <div class="mpill"><span class="dot closed" id="us-dot"></span><span id="us-label">US</span></div>
        <div class="mpill"><span class="dot open"   id="in-dot"></span><span id="in-label">India</span></div>
        <div class="mpill"><span class="dot closed" id="ca-dot"></span><span id="ca-label">Canada</span></div>
        <div class="sentiment">
            <div class="mini-bars">
                <span style="height:4px"></span><span style="height:7px"></span>
                <span style="height:10px"></span><span style="height:7px"></span>
                <span style="height:5px"></span><span style="height:9px"></span>
                <span style="height:12px"></span>
            </div>
            Bullish Sentiment
        </div>
    </div>
</nav>

{{-- ══ STATUS BAR ══ --}}
<div class="statusbar">
    <span id="mktStatusTxt">Markets Closed</span>
    <span id="navTime">—</span>
</div>

{{-- ══ COUNTRY TABS ══ --}}
<!--<div class="tab-bar">
    <button class="tab active" onclick="switchTab('US')"   id="tab-US">
        <span class="flag">🇺🇸</span> US <span class="currency-badge">USD</span>
    </button>
    <button class="tab"        onclick="switchTab('India')"id="tab-India">
        <span class="flag">🇮🇳</span> India <span class="currency-badge">INR</span>
    </button>
    <button class="tab"        onclick="switchTab('Canada')"id="tab-Canada">
        <span class="flag">🇨🇦</span> Canada <span class="currency-badge">CAD</span>
    </button>
</div> -->
<div class="dashboard-summary">
    <div class="summary-left">

{{-- ══ STAT BOXES ══ --}}
<div class="stat-section">

    <div class="stat-header">
        <div class="country-switch">
            <button class="cbtn active" onclick="switchTab('US')" id="tab-US">
                🇺🇸 <span>US</span> <small>USD</small>
            </button>

            <button class="cbtn" onclick="switchTab('India')" id="tab-India">
                🇮🇳 <span>India</span> <small>INR</small>
            </button>

            <button class="cbtn" onclick="switchTab('Canada')" id="tab-Canada">
                🇨🇦 <span>Canada</span> <small>CAD</small>
            </button>
        </div>

        <div class="stat-title">
            Portfolio Performance
        </div>
    </div>


    <div class="stat-row" id="statRow">
        @for($i=0;$i<4;$i++)
        <div class="stat-box" id="skel-stat-{{$i}}">
            <div class="stat-top">
                <div>
                    <div class="skel" style="width:60px;height:12px;margin-bottom:10px;border-radius:8px"></div>
                    <div class="skel" style="width:100px;height:12px;border-radius:8px"></div>
                </div>
                <div class="skel" style="width:72px;height:26px;border-radius:999px"></div>
            </div>
            <div class="stat-prices" style="margin-bottom:18px;">
                <div class="skel" style="width:140px;height:28px;border-radius:12px"></div>
                <div class="skel" style="width:80px;height:14px;border-radius:8px"></div>
            </div>
            <div class="stat-chart-wrap"><div class="skel" style="height:100%;width:100%;border-radius:20px"></div></div>
        </div>
        @endfor
    </div>

    <div id="statPagination" style="display:flex;justify-content:center;gap:10px;padding:10px 0;">
    <!-- buttons yahan render honge -->
</div>
</div>

<div class="grid-wrap">

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

</div>



<div class="news-wrap">

    <div class="news-header">
        <h3>Market News</h3>
        <span class="news-meta">Live updates</span>
    </div>

    <div id="newsGrid" class="news-grid"></div>

</div>


        <div class="top-assets">
            <div class="asset-card">
                <div class="asset-label">Top Assets</div>
                <div class="asset-title">NIFTY 50</div>
                <div class="asset-values">
                    <span class="asset-value">23,997.55</span>
                    <span class="asset-change neg">-0.74%</span>
                </div>
                <div class="asset-value-small">23,997.55</div>
            </div>
            <div class="asset-card">
                <div class="asset-label">Top Assets</div>
                <div class="asset-title">S&P BSE Sensex</div>
                <div class="asset-values">
                    <span class="asset-value">76,913.50</span>
                    <span class="asset-change neg">-0.75%</span>
                </div>
                <div class="asset-value-small">76,913.50</div>
            </div>
            <div class="asset-card">
                <div class="asset-label">Top Assets</div>
                <div class="asset-title">Nifty Bank Ind.</div>
                <div class="asset-values">
                    <span class="asset-value">54,863.35</span>
                    <span class="asset-change neg">-0.98%</span>
                </div>
                <div class="asset-value-small">54,863.35</div>
            </div>
            <div class="asset-card">
                <div class="asset-label">Top Assets</div>
                <div class="asset-title">Bitcoin</div>
                <div class="asset-values">
                    <span class="asset-value">US$78,116.42</span>
                    <span class="asset-change pos">+1.28%</span>
                </div>
                <div class="asset-value-small">+US$998.68</div>
            </div>
        </div>

        <div class="market-summary-card">
            <div class="summary-header">
                <h3>Market Summary</h3>
                <span>Updated 4 minutes ago</span>
            </div>
            <div class="summary-copy">
                Sensex & Nifty Under Pressure Amid Geopolitical Tensions. The Sensex closed at 76,913.50, down 582.86 points (-0.75%), while the Nifty50 fell 180.11 points (-0.74%) to 23,997.55 on April 30. Markets remain cautious as the ongoing Gulf War and disruptions in the Strait of Hormuz continue to weigh on global sentiment.
            </div>
            <ul class="summary-list">
                <li>FMCG Sector Leads Sectoral Declines</li>
                <li>Crude Oil Surge Hampers OMCs; Fuel Price Hike on the Cards</li>
                <li>Nifty Bank & Financial Services Slip Nearly 1%</li>
                <li>Nifty IT Bucks the Trend With Modest Gains</li>
                <li>India’s Consumption Holds Firm Despite Gulf War Tremors</li>
            </ul>
            <div class="summary-meta">
                <span class="summary-badge">18 sources</span>
                <span>30 Apr 2026, 15:30 GMT+5:30</span>
            </div>
        </div>

       <!-- <div class="heatmap-title">
    <span>Top 500 Heatmap</span>
    <span class="heatmap-expand">Expand ↗</span>
</div> -->
    <div class="summary-header">
        <h3>Top 500 Heatmap</h3>
         <span>Expand ↗</span>
    </div>

<div id="heatmapContainer">


    <canvas id="heatmapChart"></canvas>

    <!-- Hover Card -->
    <div id="hoverCard">
        <div id="hc-sector"></div>
        <div id="hc-name"></div>
        <div id="hc-price">
            <span id="hc-price-val">₹ 0</span>
            <span id="hc-price-change" style="margin-left: 8px;"></span>
        </div>
        <div id="hc-desc"></div>
    </div>
</div>
    </div>

<div class="summary-right">
    
    <!-- 1. Create Watchlist (Tumhara existing tha, isko theek kiya) -->
    <div class="sidebar-card">
        <div class="summary-header">
            <h3>Create Watchlist</h3>
            <small>Live prices</small>
        </div>
        <div class="watchlist-item">
            <div>
                <div class="watchlist-title">Tata Tech.</div>
                <div class="watchlist-sub">TATATECH · NSE</div>
            </div>
            <div class="watchlist-change pos">+1.54%</div>
        </div>
        <div class="watchlist-item">
            <div>
                <div class="watchlist-title">HDFC Bank</div>
                <div class="watchlist-sub">HDFCBANK · NSE</div>
            </div>
            <div class="watchlist-change neg">-0.55%</div>
        </div>
    </div>

    <!-- 2. Equity Sectors (New) -->
    <div class="sidebar-card">
        <h3>Equity Sectors</h3>
        <div class="crypto-row">
            <div><div class="crypto-title">Technology</div><div class="crypto-value">US$3,248.07</div></div>
            <div class="crypto-change pos">+1.13%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Energy</div><div class="crypto-value">US$1,086.03</div></div>
            <div class="crypto-change neg">-0.45%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Financials</div><div class="crypto-value">US$2,983.67</div></div>
            <div class="crypto-change pos">+0.85%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Health Care</div><div class="crypto-value">US$3,093.36</div></div>
            <div class="crypto-change pos">+0.12%</div>
        </div>
    </div>

    <!-- 3. Bond Markets (New) -->
    <div class="sidebar-card">
        <h3>Bond Markets</h3>
        <div class="crypto-row">
            <div><div class="crypto-title">US Treasury</div><div class="crypto-value">US$94.61</div></div>
            <div class="crypto-change neg">-0.58%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Municipals</div><div class="crypto-value">US$1.00</div></div>
            <div class="crypto-change pos">+0.02%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">High Yield</div><div class="crypto-value">US$0.98</div></div>
            <div class="crypto-change neg">-0.02%</div>
        </div>
    </div>

    <!-- 4. Popular Cryptocurrencies (New) -->
    <div class="sidebar-card">
        <h3>Popular Cryptocurrencies</h3>
        <div class="crypto-row">
            <div><div class="crypto-title">Bitcoin</div><div class="crypto-value">US$78,116.42</div></div>
            <div class="crypto-change pos">+1.28%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Ethereum</div><div class="crypto-value">US$2,929.96</div></div>
            <div class="crypto-change pos">+0.66%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Solana</div><div class="crypto-value">US$58.31</div></div>
            <div class="crypto-change neg">-0.59%</div>
        </div>
    </div>

    <!-- 5. Commodities (New) -->
    <div class="sidebar-card">
        <h3>Commodities</h3>
        <div class="crypto-row">
            <div><div class="crypto-title">Gold</div><div class="crypto-value">US$3,180.20</div></div>
            <div class="crypto-change pos">+0.42%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Silver</div><div class="crypto-value">US$32.80</div></div>
            <div class="crypto-change pos">+0.90%</div>
        </div>
        <div class="crypto-row">
            <div><div class="crypto-title">Crude Oil</div><div class="crypto-value">US$104.35</div></div>
            <div class="crypto-change pos">+1.79%</div>
        </div>
    </div>

</div>
</div>



<div class="post-heatmap-grid">
    <div class="post-main">
        <div class="post-card">
            <h3>Recent Developments <span>Updated 6 minutes ago</span></h3>
            <div class="development-grid">
                <div class="dev-card">
                    <h4>1 May 2026</h4>
                    <p>NSE BSE Shut May Long Weekend Holiday Schedule. Indian stock exchanges observed a trading halt on May 1, 2026, for Maharashtra Day. NSE and BSE will next close on May 28 for Bakri Eid. All segments — equity, derivatives, currency, SLB, and EGR — remain suspended on both dates.</p>
                </div>
                <div class="dev-card">
                    <h4>30 Apr 2026</h4>
                    <p>Sensex Drops Nifty Slides April 30 Session Ends. Indian benchmark indices closed lower on April 30, 2026. The BSE Sensex fell 582.86 points, or 0.75%, while the Nifty50 slipped 180.11 points, or 0.74%, ending at 23,997.55.</p>
                </div>
                <div class="dev-card">
                    <h4>28 Apr 2026</h4>
                    <p>Fuel Costs Unchanged Amid Global Oil Price Swings. Retail petrol and diesel prices across Indian cities held steady on May 2, 2026, despite continued volatility in global crude oil markets linked to geopolitical tensions in West Asia.</p>
                </div>
            </div>
        </div>

        <div class="post-card">
            <h3>Popular Spaces for Finance Research</h3>
            <div class="research-grid">
                <div class="research-card">
                    <h4>S&P 500 Transcripts</h4>
                    <p>Query any S&P company transcript over the last two years.</p>
                    <div class="research-actions">
                        <button class="research-btn">Query transcripts</button>
                    </div>
                </div>
                <div class="research-card">
                    <h4>What would Buffett say?</h4>
                    <p>Get answers from Buffett shareholder letters and Berkshire Hathaway’s website.</p>
                    <div class="research-actions">
                        <button class="research-btn">Ask Buffett</button>
                    </div>
                </div>
                <div class="research-card">
                    <h4>Investor Question Generator</h4>
                    <p>Get five strategic questions to ask before a potential investment.</p>
                    <div class="research-actions">
                        <button class="research-btn">Generate questions</button>
                    </div>
                </div>
                <div class="research-card">
                    <h4>Market Intelligence</h4>
                    <p>Generate a quick briefing on the latest sectors and market movers.</p>
                    <div class="research-actions">
                        <button class="research-btn">Generate briefing</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="standout-card">
            <div class="stock-highlight">
                <h4>Vedanta Limited</h4>
                <div class="stock-meta">
                    <span>VEDL · BSE</span>
                    <span>₹271.60 · -24.84%</span>
                </div>
                <div class="stock-chart">
                    <canvas id="standout-chart-vedanta"></canvas>
                    <div class="chart-note">Prev close: ₹773.25</div>
                </div>
                <div class="stock-stats">
                    <div class="stock-stat"><strong>Volume</strong><span>3.28M</span></div>
                    <div class="stock-stat"><strong>Market Cap</strong><span>1.06T</span></div>
                    <div class="stock-stat"><strong>P/E Ratio</strong><span>7.54</span></div>
                    <div class="stock-stat"><strong>Dividend Yield</strong><span>6.59%</span></div>
                </div>
                <p style="font-size:13px;color:#475569;line-height:1.6;margin-top:14px;">Vedanta’s share price dropped sharply due to a technical ex-demerger price adjustment, as the stock began trading without the value of newly created independent spin-off companies.</p>
            </div>
            <div class="stock-highlight">
                <h4>Cemindo Projects Limited</h4>
                <div class="stock-meta">
                    <span>CEPMRO · BSE</span>
                    <span>₹814.55 · +20.00%</span>
                </div>
                <div class="stock-chart">
                    <canvas id="standout-chart-cemindo"></canvas>
                    <div class="chart-note">Prev close: ₹678.80</div>
                </div>
                <div class="stock-stats">
                    <div class="stock-stat"><strong>Volume</strong><span>1.02M</span></div>
                    <div class="stock-stat"><strong>Market Cap</strong><span>139.93B</span></div>
                    <div class="stock-stat"><strong>P/E Ratio</strong><span>29.85</span></div>
                    <div class="stock-stat"><strong>Dividend Yield</strong><span>0.25%</span></div>
                </div>
                <p style="font-size:13px;color:#475569;line-height:1.6;margin-top:14px;">Cemindo Projects surged the maximum daily limit on exceptional volume, driven by strong buying momentum in India’s infrastructure sector.</p>
            </div>
            <div class="stock-highlight">
                <h4>ITD Cementation India Limited</h4>
                <div class="stock-meta">
                    <span>ITDCEM · BSE</span>
                    <span>₹814.55 · +20.00%</span>
                </div>
                <div class="stock-chart">
                    <canvas id="standout-chart-itdcem"></canvas>
                    <div class="chart-note">Prev close: ₹678.80</div>
                </div>
                <div class="stock-stats">
                    <div class="stock-stat"><strong>Volume</strong><span>1.91K</span></div>
                    <div class="stock-stat"><strong>Market Cap</strong><span>139.93B</span></div>
                    <div class="stock-stat"><strong>P/E Ratio</strong><span>34.12</span></div>
                    <div class="stock-stat"><strong>Dividend Yield</strong><span>0.25%</span></div>
                </div>
                <p style="font-size:13px;color:#475569;line-height:1.6;margin-top:14px;">ITDCEM rallied after approval for a major township project, boosting investor confidence in its infrastructure business.</p>
            </div>
            <div class="stock-highlight">
                <h4>MTAR Technologies Limited</h4>
                <div class="stock-meta">
                    <span>MTARTECH · BSE</span>
                    <span>₹6,450.80 · +14.09%</span>
                </div>
                <div class="stock-chart">
                    <canvas id="standout-chart-mtar"></canvas>
                    <div class="chart-note">Prev close: ₹5,654.00</div>
                </div>
                <div class="stock-stats">
                    <div class="stock-stat"><strong>Volume</strong><span>233.97K</span></div>
                    <div class="stock-stat"><strong>Market Cap</strong><span>198.42B</span></div>
                    <div class="stock-stat"><strong>P/E Ratio</strong><span>312.54</span></div>
                    <div class="stock-stat"><strong>Dividend Yield</strong><span>N/A</span></div>
                </div>
                <p style="font-size:13px;color:#475569;line-height:1.6;margin-top:14px;">MTAR Technologies surged on strong quarterly results and robust defense-sector order momentum.</p>
            </div>
        </div>
    </div>

    <div class="sidebar-small-card">
        <h3>Popular Cryptocurrencies</h3>
        <div class="crypto-row">
            <div>
                <div class="crypto-title">Bitcoin</div>
                <div class="crypto-value">US$78,116.42</div>
            </div>
            <div class="crypto-change pos">+1.28%</div>
        </div>
        <div class="crypto-row">
            <div>
                <div class="crypto-title">Ethereum</div>
                <div class="crypto-value">US$2,929.96</div>
            </div>
            <div class="crypto-change pos">+0.66%</div>
        </div>
        <div class="crypto-row">
            <div>
                <div class="crypto-title">Solana</div>
                <div class="crypto-value">US$58.31</div>
            </div>
            <div class="crypto-change neg">-0.59%</div>
        </div>
        <div class="crypto-row">
            <div>
                <div class="crypto-title">XRP</div>
                <div class="crypto-value">US$1.38</div>
            </div>
            <div class="crypto-change pos">+0.52%</div>
        </div>
    </div>
</div>


{{-- ══ TABLE ══ --}}
<div class="content">
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th><div class="th-btn">Symbol <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg></div></th>
                    <th><div class="th-btn">MCap <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg></div></th>
                    <th><div class="th-btn">Date Added <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg></div></th>
                    <th><div class="th-btn">Entry Price <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg></div></th>
                    <th id="th-current"><div class="th-btn">Current Price <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg></div></th>
                    <th><div class="th-btn" style="color:var(--text)">Gains % <svg class="arr active" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 10V2M3 7l3 3 3-3"/></svg></div></th>
                </tr>
            </thead>
            <tbody id="stockBody">
                @for($i=0;$i<4;$i++)
                <tr class="skeleton-row">
                    <td><div class="skel" style="width:80px;height:16px;margin-bottom:5px;display:block"></div><div class="skel" style="width:155px;height:12px"></div></td>
                    <td><div class="skel" style="width:48px;height:14px"></div></td>
                    <td><div class="skel" style="width:105px;height:14px"></div></td>
                    <td><div class="skel" style="width:62px;height:14px"></div></td>
                    <td><div class="skel" style="width:62px;height:14px"></div></td>
                    <td><div class="skel" style="width:80px;height:18px;border-radius:6px"></div></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="bottom-bar">
        <span id="lastUpdated">Fetching live data…</span>
        <button class="refresh-btn" id="refreshBtn" onclick="fetchData()">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            Refresh
        </button>
    </div>
</div>


<script>
const LIVE_URL   = '{{ route("dashboard.stocks.live") }}';
const REFRESH_MS = 60_000;
const charts     = {};

// ── State ──────────────────────────────────────────────────────────────────
let cachedData = null;
let cachedFx   = { INR: 83.5, CAD: 1.36 };
let activeTab  = 'US';

let currentPage = 0;
const perPage = 4;

// ── Currency config ────────────────────────────────────────────────────────
const CURRENCY = {
    US:     { symbol:'$',  code:'USD', rate: () => 1 },
    India:  { symbol:'₹',  code:'INR', rate: () => cachedFx.INR },
    Canada: { symbol:'C$', code:'CAD', rate: () => cachedFx.CAD },
};

// ── Clock ──────────────────────────────────────────────────────────────────
function tick() {
    const t = new Date().toLocaleString('en-IN',{
        timeZone:'Asia/Kolkata',month:'short',day:'numeric',
        year:'numeric',hour:'numeric',minute:'2-digit',hour12:true
    });
    document.getElementById('navTime').textContent = t + ' IST';
}
tick(); setInterval(tick, 1000);

// ── Market badges ──────────────────────────────────────────────────────────
function setMarkets(ms) {
    const map = {US:'us', India:'in', Canada:'ca'};
    let anyOpen = false;
    for (const [k,id] of Object.entries(map)) {
        const s = ms[k];
        document.getElementById(`${id}-dot`).className = 'dot '+(s.open?'open':'closed');
        document.getElementById(`${id}-label`).textContent = `${k} · ${s.time}`;
        if (s.open) anyOpen = true;
    }
    document.getElementById('mktStatusTxt').textContent = anyOpen ? 'Markets Open' : 'Markets Closed';
}

// ── Tab switch ─────────────────────────────────────────────────────────────
// function switchTab(tab) {
//     activeTab = tab;
//     currentPage = 0; // 👈 reset
//     document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
//     document.getElementById(`tab-${tab}`).classList.add('active');
//     if (cachedData) {
//         renderAll(cachedData);
//     }
// }

function switchTab(tab) {
    activeTab = tab;
    currentPage = 0;

    document.querySelectorAll('.cbtn').forEach(el => el.classList.remove('active'));
    document.getElementById(`tab-${tab}`).classList.add('active');

    if (cachedData) {
        renderAll(cachedData);
    }
}

// ── Format price in active currency ───────────────────────────────────────
function fmtPrice(usdVal) {
    if (usdVal == null) return '—';
    const cur  = CURRENCY[activeTab];
    const val  = usdVal * cur.rate();
    const sym  = cur.symbol;
    const fmt  = val.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    return sym + fmt;
}

function fmtDate(d) {
    const [y,m,day] = d.split('-');
    return `${m}/${day}/${y}`;
}

// ── Calendar icon ──────────────────────────────────────────────────────────
function calHtml() {
    return `<div class="cal"><div class="cal-head"><b></b><b></b></div><div class="cal-body-i"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="1" y="2" width="10" height="9" rx="1"/><path d="M1 5h10M4 1v2M8 1v2"/></svg></div></div>`;
}

// ── Stat box sparkline ─────────────────────────────────────────────────────
// entry price = dashed baseline
// gain  → line above baseline → green fill above dashed
// loss  → line below baseline → red  fill below dashed
function buildChart(id, historyUSD, entryUSD, isGain) {
    const canvas = document.getElementById(id);
    if (!canvas || !historyUSD.length) return;
    if (charts[id]) charts[id].destroy();

    const rate    = CURRENCY[activeTab].rate();
    const history = historyUSD.map(p => p * rate);
    const entry   = entryUSD * rate;

    const lineColor = isGain ? '#16a34a' : '#dc2626';
    const fillColor = isGain ? 'rgba(22,163,74,0.18)' : 'rgba(220,38,38,0.18)';

    charts[id] = new Chart(canvas, {
        type: 'line',
        data: {
            labels: history.map((_,i) => i),
            datasets: [
                {
                    data: history,
                    borderColor: lineColor,
                    borderWidth: 2,
                    backgroundColor: fillColor,
                    fill: {
                        target: 1,
                        above: isGain ? fillColor : 'transparent',
                        below: isGain ? 'transparent' : fillColor,
                    },
                    tension: 0.36,
                    pointRadius: 0,
                    borderJoinStyle: 'round',
                    order: 1,
                },
                {
                    data: history.map(() => entry),
                    borderColor: 'rgba(107,114,128,0.32)',
                    borderWidth: 1,
                    borderDash: [4,4],
                    fill: false,
                    tension: 0,
                    pointRadius: 0,
                    order: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 300 },
            plugins: { legend:{display:false}, tooltip:{enabled:false} },
            scales: {
                x:{display:false},
                y:{display:false}
            },
            elements: { line:{cap:'round'} },
            layout: { padding: 0 },
        }
    });
}

// ── Render stat boxes ──────────────────────────────────────────────────────
function renderStatBoxes(data) {
    const cur    = CURRENCY[activeTab];
    const row    = document.getElementById('statRow');
    row.innerHTML = '';

       // 👇 pagination logic
    const start = currentPage * perPage;
    const paginatedData = data.slice(start, start + perPage);

        paginatedData.forEach(s => {
            const gain   = s.gain_pct;
            const isGain = gain != null && gain >= 0;
            const cls    = gain == null ? 'neu' : isGain ? 'pos' : 'neg';
            const sign   = gain != null && gain > 0 ? '+' : '';
            const pctTxt = gain != null ? `${sign}${gain.toFixed(2)}%` : '—';
            const arrow  = isGain ? '↑' : '↓';

            const absDiff = s.current_price != null ? (s.current_price - s.entry_price) * cur.rate() : null;
            const absSign = absDiff != null ? (absDiff >= 0 ? '+' : '-') : '';
            const absTxt  = absDiff != null
                ? `${absSign}${cur.symbol}${Math.abs(absDiff).toFixed(2)}`
                : '—';

            const canvasId = `sc-${s.symbol}`;
            const curP     = s.current_price != null ? fmtPrice(s.current_price) : '—';
            const entryP   = fmtPrice(s.entry_price);

            const box = document.createElement('div');
            box.className = 'stat-box';
            box.innerHTML = `
                <div class="stat-top">
                    <div>
                        <div class="stat-name">${s.symbol}</div>
                        <div class="stat-aux">${s.exchange} · Entry ${entryP}</div>
                    </div>
                    <div class="stat-pct ${cls}"><span>${arrow}</span> ${pctTxt}</div>
                </div>
                <div class="stat-prices">
                    <div class="stat-cur">${curP}</div>
                    <div class="stat-abs ${cls}">${absTxt}</div>
                </div>
                <div class="stat-chart-wrap"><canvas id="${canvasId}"></canvas></div>`;
            row.appendChild(box);

            setTimeout(() => buildChart(canvasId, s.history || [], s.entry_price, isGain), 0);
        });



    // data.forEach(s => {
    //     const gain   = s.gain_pct;
    //     const isGain = gain != null && gain >= 0;
    //     const cls    = gain == null ? 'neu' : isGain ? 'pos' : 'neg';
    //     const sign   = gain != null && gain > 0 ? '+' : '';
    //     const pctTxt = gain != null ? `${sign}${gain.toFixed(2)}%` : '—';
    //     const arrow  = isGain ? '↑' : '↓';

    //     // Absolute change in selected currency
    //     const absDiff = s.current_price != null ? (s.current_price - s.entry_price) * cur.rate() : null;
    //     const absSign = absDiff != null ? (absDiff >= 0 ? '+' : '-') : '';
    //     const absTxt  = absDiff != null
    //         ? `${absSign}${cur.symbol}${Math.abs(absDiff).toFixed(2)}`
    //         : '—';

    //     const canvasId = `sc-${s.symbol}`;
    //     const curP     = s.current_price != null ? fmtPrice(s.current_price) : '—';

    //     const box = document.createElement('div');
    //     box.className = 'stat-box';
    //     box.innerHTML = `
    //         <div class="stat-top">
    //             <div class="stat-name">${s.symbol}</div>
    //             <div class="stat-pct ${cls}"><span>${arrow}</span> ${pctTxt}</div>
    //         </div>
    //         <div class="stat-prices">
    //             <span class="stat-cur">${curP}</span>
    //             <span class="stat-abs ${cls}">${absTxt}</span>
    //         </div>
    //         <div class="stat-chart-wrap"><canvas id="${canvasId}"></canvas></div>`;
    //     row.appendChild(box);

    //     setTimeout(() => buildChart(canvasId, s.history || [], s.entry_price, isGain), 0);
    // });
       renderPaginationControls(data.length);
}

function renderPaginationControls(totalItems) {
    const container = document.getElementById('statPagination');
    const totalPages = Math.ceil(totalItems / perPage);

    container.innerHTML = `
        <button class="pagination-btn" type="button" onclick="prevPage()" ${currentPage === 0 ? 'disabled' : ''}>⬅ Prev</button>
        <span class="pagination-page">Page ${currentPage + 1} / ${totalPages}</span>
        <button class="pagination-btn" type="button" onclick="nextPage(${totalPages})" ${currentPage >= totalPages - 1 ? 'disabled' : ''}>Next ➡</button>
    `;
}

function nextPage(totalPages) {
    if (currentPage < totalPages - 1) {
        currentPage++;
        renderAll(cachedData);
    }
}

function prevPage() {
    if (currentPage > 0) {
        currentPage--;
        renderAll(cachedData);
    }
}

// ── Render table rows ──────────────────────────────────────────────────────
function renderRows(data) {
    const cur = CURRENCY[activeTab];
    // Update "Current Price" column header with currency
    document.getElementById('th-current').querySelector('.th-btn').innerHTML =
        `Current Price (${cur.code}) <svg class="arr" width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v8M3 5l3-3 3 3"/></svg>`;

    return data.map(s => {
        const g    = s.gain_pct;
        const cls  = g == null ? 'neu' : g >= 0 ? 'pos' : 'neg';
        const sign = g != null && g > 0 ? '+' : '';
        const gTxt = g != null ? `${sign}${g.toFixed(2)}%` : '—';
        const curP = s.current_price != null
            ? fmtPrice(s.current_price)
            : '<span style="color:#9ca3af">—</span>';
        const entP = fmtPrice(s.entry_price);

        return `<tr>
            <td>
                <div><span class="sym-ticker">${s.symbol}</span><span class="sym-exch">(${s.exchange})</span></div>
                <div class="sym-name">${s.name}</div>
            </td>
            <td>${s.mcap}</td>
            <td><div class="date-cell">${calHtml()} ${fmtDate(s.date_added)}</div></td>
            <td><span class="price">${entP}</span></td>
            <td><span class="price">${curP}</span></td>
            <td><span class="gain ${cls}">${gTxt}</span></td>
        </tr>`;
    }).join('');
}

// ── Render all ─────────────────────────────────────────────────────────────
function renderAll(data) {
    renderStatBoxes(data);
    document.getElementById('stockBody').innerHTML = renderRows(data);
}

// ── Fetch ──────────────────────────────────────────────────────────────────
let loading = false;

async function fetchData() {
    if (loading) return;
    loading = true;
    document.getElementById('refreshBtn').classList.add('loading');

    try {
        const r    = await fetch(LIVE_URL, {
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}
        });
        const json = await r.json();
        if (!json.success) throw new Error();

        // Remove skeletons
        document.querySelectorAll('.skeleton-row').forEach(el=>el.remove());
        document.querySelectorAll('[id^="skel-stat-"]').forEach(el=>el.remove());

        cachedData = json.data;
        if (json.fx) cachedFx = json.fx;

        renderAll(cachedData);

        if (json.market_status) setMarkets(json.market_status);

        const ts = new Date().toLocaleTimeString('en-IN',{hour12:true,timeZone:'Asia/Kolkata'});
        document.getElementById('lastUpdated').textContent = `Last updated · ${ts} IST`;

    } catch(e) {
        document.getElementById('lastUpdated').textContent = 'Error fetching data — retrying…';
    } finally {
        loading = false;
        document.getElementById('refreshBtn').classList.remove('loading');
    }
}

fetchData();
setInterval(fetchData, REFRESH_MS);
</script>


<script>
const DATA = @json($data);

// ─────────────────────────────────────────────
// STATE (separate for both panels)
// ─────────────────────────────────────────────
let state = {
    left: 'stocks',
    right: 'stocks'
};

// ─────────────────────────────────────────────
// TAB SWITCH
// ─────────────────────────────────────────────
function setTab(tab, el, side){

    state[side] = tab;

    // active class only inside that box
    document.querySelectorAll(`#box-${side} .tab`)
        .forEach(t => t.classList.remove('active'));

    el.classList.add('active');

    render(side);
}

// ─────────────────────────────────────────────
// COLOR LOGIC
// ─────────────────────────────────────────────
function color(v){
    if(v < 0) return '#ef4444';   // red
    if(v < 1) return '#facc15';   // yellow
    return '#22c55e';            // green
}

// ─────────────────────────────────────────────
// RENDER PANEL
// ─────────────────────────────────────────────
function render(side){

    const list = document.getElementById(`list-${side}`);
    list.innerHTML = '';

    let data = DATA[state[side]] || [];

    if(!data.length){
        list.innerHTML = `<div style="color:#64748b;font-size:12px;padding:10px;">
            No data available
        </div>`;
        return;
    }

    // sort high to low
    data.sort((a,b)=>b.value - a.value);

    const max = Math.max(...data.map(d => Math.abs(d.value))) || 1;

    data.forEach(d => {

        const width = (Math.abs(d.value) / max) * 100;

        list.innerHTML += `
            <div class="row">
                <div class="name">${d.name}</div>

                <div class="bar">
                    <div class="fill"
                         style="
                            width:${width}%;
                            background:${color(d.value)};
                         ">
                    </div>
                </div>

                <div class="val" style="color:${color(d.value)}">
                    ${d.value > 0 ? '+' : ''}${d.value.toFixed(2)}%
                </div>
            </div>
        `;
    });
}

// ─────────────────────────────────────────────
// INIT BOTH PANELS
// ─────────────────────────────────────────────
function init(){
    render('left');
    render('right');
}

// run on load
init();


const NEWS = [
    {
        time: "10 hours ago",
        title: "Senmac Drops 582 Points Thursday on Crude Surge",
        desc: "Indian benchmarks fell sharply in the prior session with crude oil prices crossing $120 per barrel.",
    },
    {
        time: "30 Apr 2026",
        title: "NSE, BSE Shut Friday for Maharashtra Day Holiday",
        desc: "Both exchanges will remain closed for equity, derivatives, and SLB segments.",
    },
    {
        time: "30 Apr 2026",
        title: "Vedanta Begins Trading Ex-Demerger After Five-Way Split",
        desc: "Vedanta shares adjusted after restructuring as demerger phase officially begins.",
    },
];

function renderNews(){

    const grid = document.getElementById('newsGrid');
    grid.innerHTML = '';

    NEWS.forEach(n => {

        grid.innerHTML += `
            <div class="news-card">
                <div class="news-top">
                    <span>📰</span>
                    <span>${n.time}</span>
                </div>

                <div class="news-title">${n.title}</div>

                <div class="news-desc">${n.desc}</div>
            </div>
        `;
    });
}

// init
renderNews();
</script>


<script>
// 🔥 STATIC DATA (10 stocks)
const stocks = [
    {
        name: "Larsen & Toubro Limited",
        symbol: "LT",
        sector: "Industrials",
        price: 4014.00,
        change: -2.00,
        value: 900,
        description: "L&T fell amid broader market weakness and global oil surge concerns."
    },
    {
        name: "Reliance Industries",
        symbol: "RELIANCE",
        sector: "Energy",
        price: 2890.50,
        change: 0.74,
        value: 1200,
        description: "Reliance gained on strong energy demand and telecom outlook."
    },
    {
        name: "HDFC Bank",
        symbol: "HDFCBANK",
        sector: "Financial Services",
        price: 1520.20,
        change: -0.55,
        value: 1100,
        description: "HDFC Bank slipped due to profit booking."
    },
    {
        name: "Infosys",
        symbol: "INFY",
        sector: "Technology",
        price: 1485.00,
        change: 1.29,
        value: 950,
        description: "Infosys jumped after strong earnings."
    },
    {
        name: "ICICI Bank",
        symbol: "ICICIBANK",
        sector: "Financial Services",
        price: 980.00,
        change: -1.19,
        value: 1000,
        description: "ICICI Bank declined due to sector weakness."
    },
    {
        name: "TCS",
        symbol: "TCS",
        sector: "Technology",
        price: 3850.00,
        change: 0.10,
        value: 1050,
        description: "TCS stable with steady deal pipeline."
    },
    {
        name: "Adani Enterprises",
        symbol: "ADANIENT",
        sector: "Energy",
        price: 3025.00,
        change: -1.80,
        value: 800,
        description: "Adani fell due to volatility."
    },
    {
        name: "ITC",
        symbol: "ITC",
        sector: "Consumer Defensive",
        price: 450.00,
        change: 0.60,
        value: 700,
        description: "ITC gained on FMCG strength."
    },
    {
        name: "Bharti Airtel",
        symbol: "BHARTIARTL",
        sector: "Communication Services",
        price: 1350.00,
        change: 0.15,
        value: 850,
        description: "Airtel steady growth in subscribers."
    },
    {
        name: "Maruti Suzuki",
        symbol: "MARUTI",
        sector: "Consumer Cyclical",
        price: 11200.00,
        change: -0.85,
        value: 900,
        description: "Maruti declined due to weak sales."
    }
    
];

// 🎨 color logic
function getColor(change) {
    if (change > 0) return 'rgba(22, 163, 74, 0.85)';
    if (change < 0) return 'rgba(220, 38, 38, 0.85)';
    return 'rgba(156, 163, 175, 0.6)';
}

const ctx = document.getElementById('heatmapChart');
const hoverCard = document.getElementById('hoverCard');

// Store for mouse tracking
let mouseX = 0, mouseY = 0;

// Track mouse position
document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
});

// Ensure hover card hides when leaving the heatmap canvas
ctx.addEventListener('mouseleave', () => {
    hoverCard.style.display = 'none';
});
ctx.addEventListener('mouseout', () => {
    hoverCard.style.display = 'none';
});

// 🔥 chart init
const chart = new Chart(ctx, {
    type: 'treemap',
    data: {
        datasets: [{
            tree: stocks,
            key: 'value',
            groups: ['sector'],
            spacing: 2,
            borderWidth: 2,
            borderColor: '#fff',

            //backgroundColor(ctx) {
               // if (!ctx.raw) return '#ccc';
              //  return getColor(ctx.raw.change);
           // },

                backgroundColor(ctx) {
                    const raw = ctx.raw;

                    if (!raw) return '#ccc';

                    // treemap group + leaf dono handle karo
                    const sector = raw.sector || raw.g || raw._data?.sector;

                    const sectorColors = {
                        'Technology': '#0369a1',
                        'Financial Services': '#114b26',
                        'Energy': '#b45309',
                        'Industrials': '#4b5563',
                        'Consumer Cyclical': '#a55151',
                        'Consumer Defensive': '#2d4636',
                        'Communication Services': '#5d7dc4',
                        'Healthcare': '#6baebe',
                        'Utilities': '#ac8860'
                    };

                    return sectorColors[sector] || '#9ca3af';
                },

            labels: {
                display: true,
                formatter: (ctx) => {
                    return ctx.raw.symbol;
                },
                color: '#fff',
                font: { 
                    size: 12,
                    weight: 'bold'
                }
            }
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: { 
                enabled: false
            },
            legend: {
                display: false
            }
        },
        onHover: (event, activeElements) => {
            if (activeElements && activeElements.length > 0) {
                const index = activeElements[0].index;
                const data = chart.data.datasets[0].tree[index];

                if (data && data.name) {
                    document.getElementById('hc-sector').innerText = data.sector || 'N/A';
                    document.getElementById('hc-name').innerText = `${data.symbol} · ${data.name}`;
                    document.getElementById('hc-price-val').innerText = `₹ ${data.price.toLocaleString('en-IN', {maximumFractionDigits: 2})}`;
                    const changeSpan = document.getElementById('hc-price-change');
                    const changeText = data.change > 0 ? `+${data.change.toFixed(2)}%` : `${data.change.toFixed(2)}%`;
                    changeSpan.innerText = changeText;
                    changeSpan.style.color = data.change > 0 ? '#16a34a' : '#dc2626';
                    document.getElementById('hc-desc').innerText = data.description;
                    hoverCard.style.display = 'block';
                    hoverCard.style.left = (mouseX + 15) + 'px';
                    hoverCard.style.top = (mouseY + 15) + 'px';
                    return;
                }
            }
            hoverCard.style.display = 'none';
        },
        onLeave: () => {
            hoverCard.style.display = 'none';
        }
    }
});

function renderStandoutCharts() {
    const datasets = [
        {
            id: 'standout-chart-vedanta',
            data: [820, 790, 760, 725, 700, 680, 660, 640, 620, 600, 580, 560, 540, 520, 500, 480, 460, 440, 420, 400, 380, 360, 340, 320, 300, 280],
            borderColor: '#dc2626',
            backgroundColor: 'rgba(220, 38, 38, 0.16)'
        },
        {
            id: 'standout-chart-cemindo',
            data: [680, 690, 700, 710, 720, 730, 740, 750, 760, 770, 780, 790, 800, 805, 810, 813, 814],
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.16)'
        },
        {
            id: 'standout-chart-itdcem',
            data: [680, 685, 690, 700, 710, 720, 730, 740, 750, 760, 770, 780, 790, 800, 805, 810, 814],
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.16)'
        },
        {
            id: 'standout-chart-mtar',
            data: [5650, 5700, 5750, 5800, 5900, 6000, 6100, 6200, 6300, 6350, 6400, 6430, 6450],
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.16)'
        }
    ];

    datasets.forEach(item => {
        const canvas = document.getElementById(item.id);
        if (!canvas) return; 
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: item.data.map((_, idx) => idx + 1),
                datasets: [{
                    data: item.data,
                    borderColor: item.borderColor,
                    backgroundColor: item.backgroundColor,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 0,
                    borderJoinStyle: 'round'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {legend:{display:false}, tooltip:{enabled:false}},
                scales: {x:{display:false}, y:{display:false}},
                elements: {line:{cap:'round'}}
            }
        });
    });
}

renderStandoutCharts();
</script>
</body>
</html>