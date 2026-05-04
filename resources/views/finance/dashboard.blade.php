@extends('template.layout')

@section('title', 'Dashboard')

@section('content')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js + Treemap -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@2"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }


        a { text-decoration: none; color: inherit; }

        /* ══════════════════════════════════════
           TOP NAV BAR
        ══════════════════════════════════════ */
        .topnav {
            display: flex;
            align-items: center;
            justify-content: space-between;
           /* padding: 0 20px; */
            height: 40px;
            border-bottom: 1px solid #e5e5e5;
            background: #ffffff;
            position: sticky; top: 0; z-index: 100;
            width: 1160px;
            margin: 0 auto;
        }
        .topnav-left {
            display: flex; align-items: center; gap: 2px;
        }
        .market-selector {
            display: flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 6px;
            font-size: 12px; font-weight: 500; color: #333333;
            cursor: pointer; border: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        .market-selector .flag { font-size: 14px; }
        .market-selector .arrow { color: #999999; font-size: 9px; }
        .topnav-tabs { display: flex; align-items: center; gap: 0; margin-left: 8px; }
        .topnav-tab {
            padding: 0 12px; height: 40px; display: flex; align-items: center;
            font-size: 12px; color: #666666; cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color .15s;
            white-space: nowrap;
        }
        .topnav-tab:hover { color: #333333; }
       /* .topnav-tab.active { color: #333333; border-bottom-color: #333333; } */

        .topnav-right { display: flex; flex-direction: column; align-items: flex-end; gap: 1px; }
        .sentiment-badge {
            display: flex; align-items: center; gap: 4px;
            font-size: 10px; color: #f59e0b;
        }
        .sentiment-badge .bars { display: flex; gap: 1px; align-items: flex-end; }
        .sentiment-badge .bars span {
            width: 3px; background: #f59e0b; border-radius: 1px;
        }
        .market-status { font-size: 10px; color: #999999; }

        /* ══════════════════════════════════════
           LAYOUT: 2 columns
        ══════════════════════════════════════ */
        .page-body {
            display: flex;
            max-width: 1200px;
            margin: 0px auto;
        }
        .main-col {
            flex: 1;
            min-width: 0;
           /* border-right: 1px solid #e5e5e5; */
        }
        .side-col {
            width: 268px;
            flex-shrink: 0;
            background: #ffffff;
        }

        /* ══════════════════════════════════════
           SECTION WRAPPER
        ══════════════════════════════════════ */
        .section {
            padding: 14px 20px;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 24px;
           /* margin-top: 20px; */
             margin: 20px auto 0;
             max-width: 902px;
        }
        .section-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 10px;
        }
        .section-title { font-size: 13px; font-weight: 600; color: #333333; }
        .section-meta { font-size: 10px; color: #999999; }

        /* ══════════════════════════════════════
           TOP ASSETS
        ══════════════════════════════════════ */
        .assets-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 20px 6px;
        }
        .assets-label { font-size: 12px; font-weight: 600; color: #333333; }
        .assets-region {
            display: flex; align-items: center; gap: 4px;
            font-size: 11px; color: #666666;
           /* background: #f8f9fa; border: 1px solid #e0e0e0; */
            border-radius: 5px; padding: 3px 8px; cursor: pointer;
        }
        .asset-strip {
            display: flex; gap: 10px;
            overflow-x: auto; scrollbar-width: none;
            max-width: 895px;
            margin: 0 auto;
        }
        .asset-strip::-webkit-scrollbar { display: none; }
        .asset-item {
            flex: 1;
            min-width: 140px;
            padding: 8px 20px 10px;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 24px;
           /* box-shadow: 0 18px 40px rgba(15, 23, 42, .05); */
            transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
        }
        .asset-item:last-child { border-right: none; }
        .asset-name { font-size: 11px; font-weight: 600; color: #333333; margin-bottom: 2px; }
        .asset-price { font-size: 13px; font-weight: 600; color: #333333; }
        .asset-change { font-size: 10px; margin-top: 1px; }
        .asset-abs { font-size: 10px; color: #999999; }
        .asset-chart { height: 22px; margin-top: 4px; }
        .asset-chart svg { width: 100%; height: 22px; }

        .up   { color: #10b981; }
        .down { color: #ef4444; }
        .flat { color: #666666; }

        /* ══════════════════════════════════════
           MARKET SUMMARY
        ══════════════════════════════════════ */
        .ms-row {
            display: flex; align-items: flex-start; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid #f0f0f0;
            cursor: pointer; gap: 8px;
        }
        .ms-row:last-of-type { border-bottom: none; }
        .ms-row:hover .ms-text { color: #333333; }
        .ms-text { font-size: 11px; color: #666666; flex: 1; line-height: 1.45; }
        .ms-text.bold { font-weight: 600; font-size: 12px; color: #333333; }
        .ms-expand { color: #999999; font-size: 11px; flex-shrink: 0; margin-top: 1px; }
        .ms-body { font-size: 10px; color: #666666; line-height: 1.55; margin-top: 4px; padding-bottom: 4px; }
        .sources-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #f8f9fa; border: 1px solid #e0e0e0;
            color: #10b981; border-radius: 5px; padding: 3px 8px;
            font-size: 10px; margin-top: 8px; cursor: pointer;
        }

        /* ══════════════════════════════════════
           HEATMAP
        ══════════════════════════════════════ */
        .heatmap-wrap {
            background: #f8f9fa; border: 1px solid #e5e5e5;
            border-radius: 8px; padding: 10px; overflow: hidden;
        }
        .heatmap-sectors {
            display: flex; gap: 12px; margin-bottom: 8px; flex-wrap: wrap;
        }
        .hm-sector-label {
            font-size: 9px; color: #666666;
        }
        .hm-sector-label b { color: #333333; font-weight: 600; }
        .hm-grid {
            display: grid;
            grid-template-columns: repeat(14, 1fr);
            grid-auto-rows: 28px;
            gap: 2px;
        }
        .hm-cell {
            border-radius: 3px; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            font-size: 7px; font-weight: 600;
            color: rgba(255,255,255,.75); cursor: pointer;
            line-height: 1.2; padding: 2px;
            transition: opacity .15s;
        }
        .hm-cell:hover { opacity: .8; }
        .hm-cell .ticker { font-size: 6.5px; font-weight: 700; }
        .hm-cell .pct { font-size: 6px; opacity: .85; }

        /* colours */
        .hm-g3  { background: #10b981; }
        .hm-g2  { background: #34d399; }
        .hm-g1  { background: #6ee7b7; }
        .hm-g05 { background: #a7f3d0; }
        .hm-n   { background: #687081; }
        .hm-r05 { background: #fca5a5; }
        .hm-r1  { background: #f87171; }
        .hm-r2  { background: #ef4444; }
        .hm-r3  { background: #dc2626; }

        .hm-footer {
            display: flex; justify-content: space-between;
            margin-top: 7px; font-size: 9px; color: #999999;
        }
        .hm-scale { display: flex; gap: 5px; align-items: center; }

        /* ══════════════════════════════════════
           RECENT DEVELOPMENTS
        ══════════════════════════════════════ */
        .dev-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .dev-card {
            background: #f8f9fa; border: 1px solid #e5e5e5;
            border-radius: 8px; padding: 10px;
        }
        .dev-time {
            display: flex; align-items: center; gap: 5px;
            font-size: 10px; color: #999999; margin-bottom: 5px;
        }
        .dev-time .src-dot {
            width: 14px; height: 14px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; font-weight: 700; flex-shrink: 0;
        }
        .dev-title { font-size: 11px; font-weight: 600; color: #333333; margin-bottom: 5px; line-height: 1.4; }
        .dev-body { font-size: 10px; color: #666666; line-height: 1.55; }

        /* ══════════════════════════════════════
           POPULAR SPACES
        ══════════════════════════════════════ */
        .space-row {
            display: flex; align-items: center;
            padding: 9px 0; border-bottom: 1px solid #f0f0f0;
            gap: 10px;
        }
        .space-row:last-child { border-bottom: none; }
        .space-info { flex: 1; }
        .space-name { font-size: 12px; font-weight: 600; color: #333333; margin-bottom: 2px; }
        .space-desc { font-size: 10px; color: #666666; }
        .space-btn {
            background: #f8f9fa; border: 1px solid #e0e0e0;
            border-radius: 6px; padding: 5px 12px;
            font-size: 10px; color: #666666; cursor: pointer; white-space: nowrap;
            transition: border-color .15s, color .15s;
        }
        .space-btn:hover { border-color: #999999; color: #333333; }

        /* ══════════════════════════════════════
           STANDOUTS
        ══════════════════════════════════════ */
        .standout-card {
            background: #f8f9fa; border: 1px solid #e5e5e5;
            border-radius: 10px; padding: 14px; margin-bottom: 12px;
        }
        .sc-head {
            display: flex; align-items: center; gap: 8px; margin-bottom: 10px;
        }
        .sc-logo {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; flex-shrink: 0;
        }
        .sc-company { flex: 1; }
        .sc-name { font-size: 12px; font-weight: 600; color: #333333; }
        .sc-ticker { font-size: 9px; color: #666666; }
        .sc-price { font-size: 14px; font-weight: 700; color: #333333; text-align: right; }
        .sc-change { font-size: 11px; font-weight: 600; text-align: right; }
        .sc-chart { height: 90px; margin-bottom: 8px; }
        .sc-chart svg { width: 100%; height: 90px; }
        .sc-prev { font-size: 10px; color: #999999; text-align: right; margin-top: 3px; }
        .sc-stats {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 6px 16px; margin-top: 8px;
        }
        .sc-stat-row { display: flex; justify-content: space-between; align-items: center; }
        .sc-stat-label { font-size: 10px; color: #666666; }
        .sc-stat-value { font-size: 10px; color: #333333; font-weight: 500; }
        .sc-desc { font-size: 10px; color: #666666; line-height: 1.55; margin-top: 10px; }

        /* ══════════════════════════════════════
           RIGHT PANEL
        ══════════════════════════════════════ */
        .rp-section { padding: 30px 14px; border-bottom: 1px solid #f0f0f0; }
        .rp-title {
            font-size: 12px; font-weight: 600; color: #333333;
            margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;
        }
        .rp-add {
            width: 20px; height: 20px; background: #f8f9fa; border: 1px solid #e0e0e0;
            border-radius: 4px; display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: #666666; cursor: pointer; line-height: 1;
        }

        /* Watchlist */
        .wl-item {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 0; border-bottom: 1px solid #f0f0f0;
        }
        .wl-item:last-child { border-bottom: none; }
        .wl-logo {
            width: 22px; height: 22px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; font-weight: 700; flex-shrink: 0; overflow: hidden;
        }
        .wl-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
        .wl-info { flex: 1; min-width: 0; }
        .wl-name { font-size: 11px; color: #333333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .wl-ticker { font-size: 9px; color: #666666; }
        .wl-chart { width: 46px; flex-shrink: 0; }
        .wl-chart svg { width: 46px; height: 22px; }
        .wl-price-col { text-align: right; flex-shrink: 0; }
        .wl-price { font-size: 11px; color: #333333; }
        .wl-change { font-size: 10px; }
        .wl-star { color: #e5e5e5; font-size: 12px; cursor: pointer; margin-left: 3px; }
        .wl-star:hover { color: #f59e0b; }

        /* Prediction Markets */
        .pred-q { font-size: 11px; color: #333333; margin-bottom: 7px; line-height: 1.4; }
        .pred-opt { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .pred-lbl { font-size: 10px; color: #666666; width: 32px; flex-shrink: 0; }
        .pred-bar-bg { flex: 1; height: 4px; background: #e5e5e5; border-radius: 2px; overflow: hidden; }
        .pred-bar-fill { height: 100%; border-radius: 2px; background: #3b82f6; }
        .pred-pct { font-size: 10px; color: #666666; width: 32px; text-align: right; }
        .pred-delta { font-size: 10px; width: 34px; text-align: right; }
        .pred-vol { font-size: 9px; color: #999999; margin-top: 5px; }

        /* Largest company */
        .lc-item { padding: 4px 0; border-bottom: 1px solid #f0f0f0; }
        .lc-item:last-child { border-bottom: none; }
        .lc-row { display: flex; align-items: center; gap: 6px; }
        .lc-name { font-size: 11px; color: #333333; flex: 1; }
        .lc-bar-bg { width: 70px; height: 4px; background: #e5e5e5; border-radius: 2px; overflow: hidden; }
        .lc-bar-fill { height: 100%; border-radius: 2px; }
        .lc-pct { font-size: 10px; color: #666666; width: 30px; text-align: right; }
        .lc-delta { font-size: 10px; width: 34px; text-align: right; }

        /* Gainers tabs */
        .gl-tabs { display: flex; gap: 2px; margin-bottom: 8px; }
        .gl-tab {
            padding: 3px 10px; font-size: 11px; color: #666666;
            border-radius: 5px; cursor: pointer;
        }
        .gl-tab.active { background: #e5e5e5; color: #333333; }

        .gl-item {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 0; border-bottom: 1px solid #f0f0f0;
        }
        .gl-item:last-child { border-bottom: none; }
        .gl-logo {
            width: 22px; height: 22px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; font-weight: 700; flex-shrink: 0;
        }
        .gl-info { flex: 1; min-width: 0; }
        .gl-name { font-size: 11px; color: #333333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .gl-ticker { font-size: 9px; color: #666666; }
        .gl-right { text-align: right; flex-shrink: 0; }
        .gl-price { font-size: 11px; color: #333333; }
        .gl-change { font-size: 10px; }
        .see-all { font-size: 10px; color: #3b82f6; margin-top: 6px; display: inline-block; cursor: pointer; }

        /* Crypto */
        .cr-item {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 0; border-bottom: 1px solid #f0f0f0;
        }
        .cr-item:last-child { border-bottom: none; }
        .cr-logo {
            width: 22px; height: 22px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        .cr-info { flex: 1; }
        .cr-name { font-size: 11px; color: #333333; }
        .cr-ticker { font-size: 9px; color: #666666; }
        .cr-right { text-align: right; }
        .cr-price { font-size: 11px; color: #333333; }
        .cr-change { font-size: 10px; }

        /* Disclaimer */
        .disclaimer { padding: 10px 14px; font-size: 9px; color: #999999; line-height: 1.5; }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e5e5; border-radius: 2px; }

        .box{
            background:#fff;
            padding:18px;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 24px;
            max-width: 902px;
            margin: 20px auto;
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
            /* background:#0f172a;  */
            background:#f8f9fa;
            cursor:pointer;
            font-size:12px;
            /* color:#94a3b8; */
            color:#01060e;
        }

        .tab.active{
            /* background:#22c55e; */
            background:#98a3b3;
            /* color:#000; */
            color:#ffffff;
            font-weight:800;
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
            /* background:#1e293b; */
            background:#ace0ec;
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
            grid-template-columns:1fr 1fr;
            gap:16px;
        }

        /* ── Asset Cards (image-style) ── */
        .asset-strip-wrap { position: relative; max-width: 895px; margin: 0 auto; }
        .asset-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            overflow: hidden;
        }
        /* Override previous asset-item flex styles */
        .asset-item {
            flex: unset;
            min-width: unset;
            padding: 12px 14px 10px;
            border: 1px solid #e8eaed;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: box-shadow .15s;
        }
        .asset-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }

        /* Top row: name + badge */
        .ai-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
        .ai-name { font-size: 12px; font-weight: 600; color: #333; }
        .ai-badge {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 11px; font-weight: 700; padding: 2px 7px;
            border-radius: 6px;
        }

        .price-top { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        margin-bottom: 4px;
     }
        .ai-badge.pos { color: #16a34a; background: rgba(22,163,74,.1); }
        .ai-badge.neg { color: #dc2626; background: rgba(220,38,38,.1); }
        .ai-badge.neu { color: #6b7280; background: rgba(107,114,128,.1); }

        /* Price row */
        .ai-price { font-size: 15px; font-weight: 600; color: #111; margin-bottom: 1px; }
        .ai-abs   { font-size: 11px; color: #6b7280; margin-bottom: 6px; }
        .ai-abs.pos { color: #16a34a; }
        .ai-abs.neg { color: #dc2626; }

        /* Sparkline */
        .ai-chart { height: 44px; border-top: 1px dashed #e5e7eb; padding-top: 4px; }
        .ai-chart canvas { width: 100% !important; height: 40px !important; display: block; }

        /* Skeleton */
        .skel { background: linear-gradient(90deg,#f0f0f0 25%,#e4e4e4 50%,#f0f0f0 75%); background-size: 200% 100%; animation: sh 1.4s infinite; border-radius: 4px; display: inline-block; }
        @keyframes sh { 0% { background-position: 200% 0 } 100% { background-position: -200% 0 } }

        /* Pagination row below asset strip */
        .asset-pagination {
            display: flex; justify-content: space-between; align-items: center;
            max-width: 895px; margin: 8px auto 0; padding: 0 2px;
        }
        .ap-btn {
            border: 1px solid #e5e7eb; background: #f8f9fa; color: #374151;
            padding: 4px 14px; border-radius: 999px; font-size: 11px;
            font-weight: 600; cursor: pointer; transition: all .15s;
            display: flex; align-items: center; gap: 5px;
        }
        .ap-btn:hover:not(:disabled) { border-color: #9ca3af; background: #98a3b3; }
        .ap-btn:disabled { opacity: .4; cursor: not-allowed; }
        .ap-info { font-size: 11px; color: #9ca3af; font-weight: 500; }

        /* Refresh small */
        .asset-refresh { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #9ca3af; cursor: pointer; }
        .asset-refresh svg { transition: transform .5s; }
        .asset-refresh.loading svg { animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg) } }

        .topnav-tab {
    position: relative;
    color: #9aa0a6;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

/* hover effect */
.topnav-tab:hover {
    /* color: #00e5ff; */
    color: #043625;
    transform: translateY(-1px);
}

/* active tab */
.topnav-tab.active {
    /* color: #00e5ff; */
    color: #4f7d73;
    font-weight: 600;
}

/* animated underline */
.topnav-tab::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    width: 0%;
    height: 3px;
    /* background: linear-gradient(90deg, #00e5ff, #2979ff); */
    background: linear-gradient(90deg, #4f7d73, #26754a);
    border-radius: 50px;
    transition: all 0.4s ease;
    transform: translateX(-50%);
    /* box-shadow: 0 0 10px rgba(0, 229, 255, 0.6); */
}

/* active underline expands */
.topnav-tab.active::after {
    width: 100%;
}

/* subtle glow pulse */
/* @keyframes glowPulse {
    0% {
        box-shadow: 0 0 5px rgba(0, 229, 255, 0.3);
    }
    50% {
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.8);
    }
    100% {
        box-shadow: 0 0 5px rgba(0, 229, 255, 0.3);
    }
} */

.topnav-tab.active {
    animation: glowPulse 2s infinite;
}




    </style>

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

<!-- ══ PAGE BODY ══════════════════════════════════════════ -->
<div class="page-body">

  <!-- ══ MAIN COLUMN ══════════════════════════════════════ -->
  <div class="main-col">

    <!-- Top Assets — Portfolio Cards (paginated, image-style) -->
    <div class="assets-header">
        <div class="assets-label" id="assetsLabel">Portfolio · USA (USD)</div>
        <div style="display:flex;align-items:center;gap:10px;">
           <!-- <div class="assets-region" id="assetsRegion">US ▾</div> -->
            <div class="assets-region" id="assetsRegion">US</div>

            <div class="asset-refresh" id="assetRefreshBtn" onclick="fetchLiveData()">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                <span id="lastUpdatedTxt">Loading…</span>
            </div>
        </div>
    </div>

    {{-- Skeleton placeholders (4 cards) --}}
    <div class="asset-strip" id="assetStrip">
        @for($i=0;$i<4;$i++)
        <div class="asset-item" id="skel-asset-{{$i}}">
            <div class="ai-top">
                <div class="skel" style="width:70px;height:13px;border-radius:6px;"></div>
                <div class="skel" style="width:55px;height:18px;border-radius:6px;"></div>
            </div>
            <div class="skel" style="width:90px;height:16px;border-radius:6px;margin:6px 0 3px;"></div>
            <div class="skel" style="width:55px;height:12px;border-radius:6px;margin-bottom:6px;"></div>
            <div class="ai-chart"><div class="skel" style="width:100%;height:40px;border-radius:8px;"></div></div>
        </div>
        @endfor
    </div>

    <!-- Pagination row -->
    <div class="asset-pagination" id="assetPagination" style="display:none;">
        <button class="ap-btn" id="apPrev" onclick="assetPrevPage()">&#8592; Prev</button>
        <span class="ap-info" id="apInfo">Page 1 / 2</span>
        <button class="ap-btn" id="apNext" onclick="assetNextPage()">Next &#8594;</button>
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

    <!-- Top 500 Heatmap -->
<div class="section" style="padding:10px 20px;">
    <div class="section-head" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div class="section-title" style="font-size:14px; font-weight:600; color:#111;">Top 500 Heatmap</div>
        <span style="font-size:12px; color:#666; cursor:pointer; display:flex; align-items:center; gap:6px;">Expand ⤢</span>
    </div>

    <div class="heatmap-wrap" style="border:1px solid #e5e5e5; border-radius:12px; background:#fff; padding:12px;">
        
        <!-- Label Row 1 (Top sectors) -->
        <div style="display:flex; width:100%; font-size:10px; font-weight:600; color:#444; margin-bottom:6px;">
            <div class="hm-label" style="flex: 5;">Energy</div>
            <div class="hm-label" style="flex: 3;">Communication Services</div>
            <div class="hm-label" style="flex: 4; text-align:center;">Technology</div>
            <div class="hm-label" style="flex: 3; text-align:right; padding-right:10px;">Consumer Defensive</div>
            <div class="hm-label" style="flex: 2; text-align:right;">Healthcare</div>
            <div class="hm-label" style="flex: 1.5; text-align:right;">Utilities</div>
        </div>

        <!-- GRID -->
        <div style="display:grid; grid-template-columns: repeat(14, 1fr); gap:2px; grid-auto-rows: minmax(28px, auto);">
            <div class="hm-cell hm-r1" style="grid-column: span 5; grid-row: span 3; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">RELIANCE.NS</span><span class="pct">-1.06%</span>
            </div>
            <div class="hm-cell hm-r05" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">ONGC.NS</span><span class="pct">-0.26%</span>
            </div>
            <div class="hm-cell hm-n" style="grid-column: span 1;"><span class="ticker">IOC</span></div>
            <div class="hm-cell hm-n" style="grid-column: span 1;"><span class="ticker">BPCL</span></div>
            <div class="hm-cell hm-r2" style="grid-column: span 3; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">BHARTIARTL.NS</span><span class="pct">-0.10%</span>
            </div>
            <div class="hm-cell hm-n" style="grid-column: span 1;"><span class="ticker">IDEA</span></div>
            <div class="hm-cell hm-n" style="grid-column: span 4; grid-row: span 3; background:#f5f5f5; border:1px solid #ddd; color:#333; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">TCS.NS</span><span class="pct">+0.00%</span>
            </div>
            <div class="hm-cell hm-g1" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">INFY.NS</span><span class="pct">+1.29%</span>
            </div>
            <div class="hm-cell hm-g1"><span class="ticker">WIPRO.NS</span><span class="pct">+0.25%</span></div>
            <div class="hm-cell hm-r1" style="grid-column: span 3; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">HINDUNILVR.NS</span><span class="pct">-1.06%</span>
            </div>
            <div class="hm-cell hm-r05" style="grid-column: span 1;"><span class="ticker">ITC</span><span class="pct">-0.33%</span></div>
            <div class="hm-cell hm-r1" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">SUNPHARMA.NS</span><span class="pct">-0.80%</span>
            </div>
            <div class="hm-cell hm-g1"><span class="ticker">CIPLA</span><span class="pct">+0.90%</span></div>
            <div class="hm-cell hm-r05" style="grid-column: span 1.5; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">NTPC.NS</span><span class="pct">-0.35%</span>
            </div>
            <div class="hm-cell hm-n"><span class="ticker">POWERGRID</span></div>
        </div>

        <!-- LABEL ROW 2 -->
        <div style="display:flex; width:100%; font-size:10px; font-weight:600; color:#444; margin:6px 0 4px;">
            <div class="hm-label" style="flex: 6.5;">Financial Services</div>
            <div class="hm-label" style="flex: 3;">Consumer Cyclical</div>
            <div class="hm-label" style="flex: 3.5;">Basic Materials</div>
            <div class="hm-label" style="flex: 1.5;">Real Estate</div>
        </div>

        <!-- GRID ROW 2 -->
        <div style="display:grid; grid-template-columns: repeat(14, 1fr); gap:2px; grid-auto-rows: minmax(28px, auto);">
            <div class="hm-cell hm-r05" style="grid-column: span 6.5; grid-row: span 3; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">HDFCBANK.NS</span><span class="pct">-0.55%</span>
            </div>
            <div class="hm-cell hm-r2" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">SBIN.NS</span><span class="pct">-1.36%</span>
            </div>
            <div class="hm-cell hm-r1" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">ICICIBANK.NS</span><span class="pct">-1.19%</span>
            </div>
            <div class="hm-cell hm-g2" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">BAJFINANCE.NS</span><span class="pct">+1.04%</span>
            </div>
            <div class="hm-cell hm-n"><span class="ticker">KOTAKBANK</span></div>
            <div class="hm-cell hm-n"><span class="ticker">AXISBANK</span></div>
            <div class="hm-cell hm-r05" style="grid-column: span 3; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">MARUTI</span><span class="pct">-0.45%</span>
            </div>
            <div class="hm-cell hm-r2" style="grid-column: span 2; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">MIND</span><span class="pct">-1.84%</span>
            </div>
            <div class="hm-cell hm-g1" style="grid-column: span 3; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">ADANIPORTS.NS</span><span class="pct">+0.61%</span>
            </div>
            <div class="hm-cell hm-r05"><span class="ticker">TATASTEEL</span></div>
            <div class="hm-cell hm-g2"><span class="ticker">JSWSTEEL</span><span class="pct">+1.50%</span></div>
            <div class="hm-cell hm-r1" style="grid-column: span 1.5; grid-row: span 2; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span class="ticker">DLF</span><span class="pct">-0.50%</span>
            </div>
            <div class="hm-cell hm-n"><span class="ticker">GODREJPROP</span></div>
        </div>

        <!-- Footer -->
        <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:10px; color:#888;">
            <span style="display:flex; gap:4px;">
                <span style="color:#b91c1c;">■</span><span style="color:#e74c3c;">■</span>
                <span style="color:#ec9b9b;">■</span><span style="color:#f9d2d1;">■</span>
                <span style="color:#f2f2f2;">■</span><span style="color:#cfead6;">■</span>
                <span style="color:#85c979;">■</span><span style="color:#26a65b;">■</span>
                <span style="color:#1a6f3a;">■</span>
                <span style="margin-left:4px;">-3%</span><span>0</span><span>+3%</span>
            </span>
            <span>30 Apr 2026, 15:30 GMT+5:30</span>
        </div>
    </div>
</div>

    <!-- Recent Developments -->
    <div class="section">
        <div class="section-head">
            <div class="section-title">Recent Developments</div>
            <div class="section-meta">Updated 3 hours ago</div>
        </div>
        <div class="dev-grid">
            <div class="dev-card">
                <div class="dev-time">
                    <div class="src-dot" style="background:#e74c3c;color:#fff;">N</div>
                    23 hours ago
                </div>
                <div class="dev-title">NSE BSE Shut Following Maharashtra Day Closure</div>
                <div class="dev-body">Indian equity markets remained closed on May 1, 2026, for Maharashtra Day, with BSE and NSE suspending all trading segments including equities, derivatives, currency derivatives, SLB, and EGR.</div>
            </div>
            <div class="dev-card">
                <div class="dev-time">
                    <div class="src-dot" style="background:#f59e0b;color:#000;">C</div>
                    30 Apr 2026
                </div>
                <div class="dev-title">Sensex Nifty Decline Last April Session</div>
                <div class="dev-body">In the final trading session of April 2026, the Sensex fell 582.86 points, or 0.75%, to 76,913.50, while the Nifty dropped 180.11 points, or 0.74%, to 23,997.55.</div>
            </div>
            <div class="dev-card">
                <div class="dev-time">
                    <div class="src-dot" style="background:#3b82f6;color:#fff;">F</div>
                    5 hours ago
                </div>
                <div class="dev-title">Fuel Hike Signals Emerge Amid West Asia Tensions</div>
                <div class="dev-body">Government sources indicated a potential upward revision to petrol and diesel prices across major cities including Delhi, Mumbai, and Kolkata.</div>
            </div>
        </div>
    </div>

    <!-- Popular Spaces -->
    <div class="section">
        <div class="section-title" style="margin-bottom:10px;">Popular Spaces for Finance Research</div>
        <div class="space-row">
            <div class="space-info">
                <div class="space-name">S&amp;P 500 Transcripts</div>
                <div class="space-desc">Query any S&amp;P company transcript over the last two years.</div>
            </div>
            <div class="space-btn">Query transcripts</div>
        </div>
        <div class="space-row">
            <div class="space-info">
                <div class="space-name">What would Buffett say?</div>
                <div class="space-desc">Get answers from Buffett shareholder letters and Berkshire Hathaway's website.</div>
            </div>
            <div class="space-btn">Ask Buffett</div>
        </div>
        <div class="space-row">
            <div class="space-info">
                <div class="space-name">Investor Question Generator</div>
                <div class="space-desc">Get five strategic questions to ask before a potential investment</div>
            </div>
            <div class="space-btn">Generate questions</div>
        </div>
    </div>

    <!-- Standouts -->
    <div class="section">
        <div class="section-title" style="margin-bottom:12px;">Standouts</div>

        <div class="standout-card">
            <div class="sc-head">
                <div class="sc-logo" style="background:#2a1500;color:#f59e0b;font-size:8px;">—</div>
                <div class="sc-company">
                    <div class="sc-name">Vedanta Limited</div>
                    <div class="sc-ticker">VEDL · BSE</div>
                </div>
                <div>
                    <div class="sc-price">₹271.60</div>
                    <div class="sc-change down">▼ 64.88%</div>
                </div>
            </div>
            <div class="sc-chart">
                <svg viewBox="0 0 460 90" preserveAspectRatio="none">
                    <defs><linearGradient id="vg1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#26a65b" stop-opacity=".25"/><stop offset="100%" stop-color="#26a65b" stop-opacity=".02"/></linearGradient></defs>
                    <polygon points="0,75 80,70 160,62 230,55 300,45 380,35 460,28 460,90 0,90" fill="url(#rg1)"/>
                    <polyline points="0,75 80,70 160,62 230,55 300,45 380,35 460,28" fill="none" stroke="#d81515" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="sc-prev">Prev close: ₹678.80</div>
            <div class="sc-stats">
                <div class="sc-stat-row"><div class="sc-stat-label">Volume</div><div class="sc-stat-value">3.28M</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Market Cap</div><div class="sc-stat-value">1.06T</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">P/E Ratio</div><div class="sc-stat-value">7.54</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Dividend Yield</div><div class="sc-stat-value">6.59%</div></div>
            </div>
            <div class="sc-desc">Vedanta's share price dropped sharply due to a technical ex-demerger price adjustment, as the stock began trading without the value of newly created independent spin-off companies.</div>
        </div>

        <div class="standout-card">
            <div class="sc-head">
                <div class="sc-logo" style="background:#001f3f;color:#7dd3fc;font-size:8px;">—</div>
                <div class="sc-company">
                    <div class="sc-name">Tata Motors Ltd</div>
                    <div class="sc-ticker">TATAMOTORS · NSE</div>
                </div>
                <div>
                    <div class="sc-price">₹912.40</div>
                    <div class="sc-change up">▲ 3.75%</div>
                </div>
            </div>
            <div class="sc-chart">
                <svg viewBox="0 0 460 90" preserveAspectRatio="none">
                    <defs><linearGradient id="tg1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#3b82f6" stop-opacity=".25"/><stop offset="100%" stop-color="#3b82f6" stop-opacity=".02"/></linearGradient></defs>
                    <polygon points="0,60 80,55 160,50 230,48 300,42 380,35 460,30 460,90 0,90" fill="url(#tg1)"/>
                    <polyline points="0,60 80,55 160,50 230,48 300,42 380,35 460,30" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="sc-prev">Prev close: ₹879.20</div>
            <div class="sc-stats">
                <div class="sc-stat-row"><div class="sc-stat-label">Volume</div><div class="sc-stat-value">5.12M</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Market Cap</div><div class="sc-stat-value">3.02T</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">P/E Ratio</div><div class="sc-stat-value">24.10</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Dividend Yield</div><div class="sc-stat-value">0.65%</div></div>
            </div>
            <div class="sc-desc">Tata Motors witnessed strong upward momentum with increased buying activity across both domestic and global segments, driven by EV expansion and robust quarterly delivery numbers.</div>
        </div>

        <div class="standout-card">
            <div class="sc-head">
                <div class="sc-logo" style="background:#1a2600;color:#86efac;font-size:9px;">I</div>
                <div class="sc-company">
                    <div class="sc-name">ITD Cementation India Limited</div>
                    <div class="sc-ticker">ITDCEM · BSE</div>
                </div>
                <div>
                    <div class="sc-price">₹814.55</div>
                    <div class="sc-change up">▲ 20.00%</div>
                </div>
            </div>
            <div class="sc-chart">
                <svg viewBox="0 0 460 90" preserveAspectRatio="none">
                    <defs><linearGradient id="vg2" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#26a65b" stop-opacity=".25"/><stop offset="100%" stop-color="#26a65b" stop-opacity=".02"/></linearGradient></defs>
                    <polygon points="0,80 100,78 200,75 280,68 360,58 420,48 460,35 460,90 0,90" fill="url(#vg2)"/>
                    <polyline points="0,80 100,78 200,75 280,68 360,58 420,48 460,35" fill="none" stroke="#26a65b" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="sc-prev">Prev close: ₹678.80</div>
            <div class="sc-stats">
                <div class="sc-stat-row"><div class="sc-stat-label">Volume</div><div class="sc-stat-value">1.91K</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Market Cap</div><div class="sc-stat-value">139.93B</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">P/E Ratio</div><div class="sc-stat-value">34.12</div></div>
                <div class="sc-stat-row"><div class="sc-stat-label">Dividend Yield</div><div class="sc-stat-value">0.25%</div></div>
            </div>
            <div class="sc-desc">ITDCEM rallied after approval for a major township project, boosting investor confidence in its infrastructure business.</div>
        </div>

    </div><!-- /standouts -->

  </div><!-- /main-col -->

  <!-- ══ RIGHT PANEL ══════════════════════════════════════ -->
  <div class="side-col">

    <!-- Create Watchlist -->
    <div class="rp-section">
        <div class="rp-title">
            Create Watchlist
            <div class="rp-add">+</div>
        </div>
        <div class="wl-item">
            <div class="wl-logo" style="background:#b91c1c;color:#fff;">I</div>
            <div class="wl-info">
                <div class="wl-name">ICICI Lombard General...</div>
                <div class="wl-ticker">ICICIGI · NSE</div>
            </div>
            <div class="wl-chart">
                <svg viewBox="0 0 46 22"><polyline points="0,16 12,14 25,12 38,14 46,15" fill="none" stroke="#e74c3c" stroke-width="1.2"/></svg>
            </div>
            <div class="wl-price-col">
                <div class="wl-price">₹1,761</div>
                <div class="wl-change down">-0.54%</div>
            </div>
            <div class="wl-star">☆</div>
        </div>
        <div class="wl-item">
            <div class="wl-logo" style="background:#1e3a5f;color:#60a5fa;">T</div>
            <div class="wl-info">
                <div class="wl-name">Tata Technologies Li...</div>
                <div class="wl-ticker">TATATECH · NSE</div>
            </div>
            <div class="wl-chart">
                <svg viewBox="0 0 46 22"><polyline points="0,17 12,14 25,11 38,8 46,6" fill="none" stroke="#26a65b" stroke-width="1.2"/></svg>
            </div>
            <div class="wl-price-col">
                <div class="wl-price">₹581.00</div>
                <div class="wl-change up">+1.54%</div>
            </div>
            <div class="wl-star">☆</div>
        </div>
        <div class="wl-item">
            <div class="wl-logo" style="background:#1a3a1a;color:#4ade80;">R</div>
            <div class="wl-info">
                <div class="wl-name">Reliance Industries...</div>
                <div class="wl-ticker">RELIANCE · BSE</div>
            </div>
            <div class="wl-chart">
                <svg viewBox="0 0 46 22"><polyline points="0,14 12,13 25,12 38,11 46,10" fill="none" stroke="#26a65b" stroke-width="1.2"/></svg>
            </div>
            <div class="wl-price-col">
                <div class="wl-price">₹1,430.83</div>
                <div class="wl-change up">+0.33%</div>
            </div>
            <div class="wl-star">☆</div>
        </div>
        <div class="wl-item">
            <div class="wl-logo" style="background:#1e2a4a;color:#93c5fd;">I</div>
            <div class="wl-info">
                <div class="wl-name">Infosys Limited</div>
                <div class="wl-ticker">INFY · NSE</div>
            </div>
            <div class="wl-chart">
                <svg viewBox="0 0 46 22"><polyline points="0,15 12,13 25,11 38,9 46,7" fill="none" stroke="#26a65b" stroke-width="1.2"/></svg>
            </div>
            <div class="wl-price-col">
                <div class="wl-price">₹1,182.6</div>
                <div class="wl-change up">+1.29%</div>
            </div>
            <div class="wl-star">☆</div>
        </div>
    </div>

    <!-- Prediction Markets -->
    <div class="rp-section">
        <div class="rp-title">Prediction Markets</div>
        <div class="pred-q">What will WTI Crude Oil (WTI) hit in May 2026?</div>
        <div class="pred-opt">
            <div class="pred-lbl">↑ $105</div>
            <div class="pred-bar-bg"><div class="pred-bar-fill" style="width:90%;"></div></div>
            <div class="pred-pct">90.0%</div>
            <div class="pred-delta flat">−0.0%</div>
        </div>
        <div class="pred-opt">
            <div class="pred-lbl">↑ $95</div>
            <div class="pred-bar-bg"><div class="pred-bar-fill" style="width:79%;"></div></div>
            <div class="pred-pct">79.0%</div>
            <div class="pred-delta flat">−0.0%</div>
        </div>
        <div class="pred-opt">
            <div class="pred-lbl">↑ $110</div>
            <div class="pred-bar-bg"><div class="pred-bar-fill" style="width:73%;"></div></div>
            <div class="pred-pct">73.0%</div>
            <div class="pred-delta up">▲ 7.5%</div>
        </div>
        <div class="pred-vol">US$3.4M Vol.</div>
    </div>

    <!-- Largest Company end of May -->
    <div class="rp-section">
        <div class="rp-title">Largest Company end of May?</div>
        <div class="lc-item">
            <div class="lc-row">
                <div class="lc-name">NVIDIA</div>
                <div class="lc-bar-bg"><div class="lc-bar-fill" style="width:78%;background:#26a65b;"></div></div>
                <div class="lc-pct">78.0%</div>
                <div class="lc-delta up">▲ 0.8%</div>
            </div>
        </div>
        <div class="lc-item">
            <div class="lc-row">
                <div class="lc-name">Alphabet</div>
                <div class="lc-bar-bg"><div class="lc-bar-fill" style="width:22%;background:#3b82f6;"></div></div>
                <div class="lc-pct">22.0%</div>
                <div class="lc-delta up">▲ 0.4%</div>
            </div>
        </div>
        <div class="lc-item">
            <div class="lc-row">
                <div class="lc-name">Apple</div>
                <div class="lc-bar-bg"><div class="lc-bar-fill" style="width:1%;background:#666;"></div></div>
                <div class="lc-pct">1.0%</div>
                <div class="lc-delta down">▼ 0.1%</div>
            </div>
        </div>
        <div style="font-size:9px;color:#444;margin-top:5px;">US$899K vol.</div>
    </div>

    <!-- Gainers / Losers / Active -->
    <div class="rp-section">
        <div class="gl-tabs">
            <div class="gl-tab active" onclick="switchGLTab(this,'gainers')">Gainers</div>
            <div class="gl-tab" onclick="switchGLTab(this,'losers')">Losers</div>
            <div class="gl-tab" onclick="switchGLTab(this,'active')">Active</div>
        </div>
        <div id="gainers">
            <div class="gl-item">
                <div class="gl-logo" style="background:#1a2a1a;color:#4ade80;">C</div>
                <div class="gl-info">
                    <div class="gl-name">Cemindia Projects Limit...</div>
                    <div class="gl-ticker">CEMPRO · BSE</div>
                </div>
                <div class="gl-right">
                    <div class="gl-price">₹814.55</div>
                    <div class="gl-change up">+20.00%</div>
                </div>
            </div>
            <div class="gl-item">
                <div class="gl-logo" style="background:#1a2600;color:#86efac;">I</div>
                <div class="gl-info">
                    <div class="gl-name">ITD Cementation India Li...</div>
                    <div class="gl-ticker">ITDCEM · BSE</div>
                </div>
                <div class="gl-right">
                    <div class="gl-price">₹814.55</div>
                    <div class="gl-change up">+20.00%</div>
                </div>
            </div>
            <div class="gl-item">
                <div class="gl-logo" style="background:#1e2a4a;color:#93c5fd;">M</div>
                <div class="gl-info">
                    <div class="gl-name">MTAR Technologies Li...</div>
                    <div class="gl-ticker">MTARTECH · BSE</div>
                </div>
                <div class="gl-right">
                    <div class="gl-price">₹6,450.8</div>
                    <div class="gl-change up">+14.09%</div>
                </div>
            </div>
            <a class="see-all">See all ›</a>
        </div>
    </div>

    <!-- Popular Cryptocurrencies -->
    <div class="rp-section">
        <div class="rp-title">Popular Cryptocurrencies</div>
        <div class="cr-item">
            <div class="cr-logo" style="background:#2a1500;color:#f59e0b;">₿</div>
            <div class="cr-info"><div class="cr-name">Bitcoin</div><div class="cr-ticker">BTCUSD · CRYPTO</div></div>
            <div class="cr-right"><div class="cr-price">US$78,296.34</div><div class="cr-change up">+1.27%</div></div>
        </div>
        <div class="cr-item">
            <div class="cr-logo" style="background:#0d1a2a;color:#60a5fa;">Ξ</div>
            <div class="cr-info"><div class="cr-name">Ethereum</div><div class="cr-ticker">ETHUSD · CRYPTO</div></div>
            <div class="cr-right"><div class="cr-price">US$2,304.44</div><div class="cr-change up">+0.82%</div></div>
        </div>
        <div class="cr-item">
            <div class="cr-logo" style="background:#1a0d2a;color:#a855f7;">◎</div>
            <div class="cr-info"><div class="cr-name">Solana</div><div class="cr-ticker">SOLUSD · CRYPTO</div></div>
            <div class="cr-right"><div class="cr-price">US$83.87</div><div class="cr-change down">-0.27%</div></div>
        </div>
        <div class="cr-item">
            <div class="cr-logo" style="background:#0a0a1a;color:#94a3b8;">✕</div>
            <div class="cr-info"><div class="cr-name">XRP</div><div class="cr-ticker">XRPUSD · CRYPTO</div></div>
            <div class="cr-right"><div class="cr-price">US$1.39</div><div class="cr-change up">+0.52%</div></div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer">
        Financial information provided by Financial Modelling Prep. Options data provided by Unusual Whales. Earnings transcripts, audio, and documents provided by Quartr. Reported revenue and EPS data from Earnings powered by Fiscal.ai. Estimates directed to S&P Global. Prediction markets data from Polymarket. All data is provided for informational purposes only, and is not intended for trading purposes or financial, investment, tax, legal, accounting or other advice.
    </div>

  </div><!-- /side-col -->

</div><!-- /page-body -->


{{-- ══════════════════════════════════════════════════════════
     JAVASCRIPT — Dynamic Currency + Asset Cards
══════════════════════════════════════════════════════════ --}}
<script>
const LIVE_URL   = '{{ route("finance.live") }}';
const REFRESH_MS = 60_000;
const aiCharts   = {};   // sparkline chart instances keyed by canvasId

// ── Country config ──────────────────────────────────────────────────────────
const COUNTRY_CONFIG = {
    US:     { flag: '🇺🇸', label: 'USA Markets',    short: 'US',     currency: { symbol: '$',  rate: () => 1 } },
    India:  { flag: '🇮🇳', label: 'India Markets',  short: 'IN',     currency: { symbol: '₹',  rate: () => cachedFx.INR } },
    Canada: { flag: '🇨🇦', label: 'Canada Markets', short: 'CA',     currency: { symbol: 'C$', rate: () => cachedFx.CAD } },
};

// ── State ───────────────────────────────────────────────────────────────────
let cachedPortfolio = null;
let cachedFx        = { INR: 83.5, CAD: 1.36 };
let activeCountry   = 'US';
let assetPage       = 0;
const assetPerPage  = 4;
let loadingLive     = false;

// ── Country Switch (called from nav tabs) ───────────────────────────────────
function switchCountry(country, el) {
    activeCountry = country;
    assetPage     = 0;

    document.querySelectorAll('.topnav-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    const cfg = COUNTRY_CONFIG[country];
    document.getElementById('selectorFlag').textContent = cfg.flag;
    document.getElementById('selectorName').textContent = cfg.label;

    if (cachedPortfolio) renderAssetCards(cachedPortfolio);
}

// ── Format price ────────────────────────────────────────────────────────────
function fmtP(usdVal) {
    if (usdVal == null) return '—';
    const { symbol, rate } = COUNTRY_CONFIG[activeCountry].currency;
    const v = usdVal * rate();
    return symbol + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Sparkline (minimal, image-style) ────────────────────────────────────────
function buildSparkline(canvasId, historyUSD, isGain) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    if (aiCharts[canvasId]) aiCharts[canvasId].destroy();

    const rate    = COUNTRY_CONFIG[activeCountry].currency.rate();
    const history = historyUSD.map(p => p * rate);
    const color   = isGain ? '#16a34a' : '#dc2626';
    const fill    = isGain ? 'rgba(22,163,74,0.12)' : 'rgba(220,38,38,0.12)';

    aiCharts[canvasId] = new Chart(canvas, {
        type: 'line',
        data: {
            labels: history.map((_, i) => i),
            datasets: [{
                data: history,
                borderColor: color,
                borderWidth: 1.5,
                backgroundColor: fill,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            layout: { padding: 0 },
        }
    });
}

// ── Render Asset Cards ──────────────────────────────────────────────────────
function renderAssetCards(data) {
    const strip = document.getElementById('assetStrip');
    strip.innerHTML = '';

    const totalPages = Math.ceil(data.length / assetPerPage);
    const slice      = data.slice(assetPage * assetPerPage, (assetPage + 1) * assetPerPage);
    const cur        = COUNTRY_CONFIG[activeCountry].currency;

    slice.forEach(s => {
        const gain   = s.gain_pct;
        const isGain = gain != null && gain >= 0;
        const cls    = gain == null ? 'neu' : isGain ? 'pos' : 'neg';
        const arrow  = gain == null ? '' : isGain ? '↗' : '↘';
        const sign   = gain != null && gain > 0 ? '+' : '';
        const pctTxt = gain != null ? `${arrow} ${sign}${gain.toFixed(2)}%` : '—';

        const curP   = s.current_price != null ? fmtP(s.current_price) : '—';
        const absDiff = s.current_price != null ? (s.current_price - s.entry_price) * cur.rate() : null;
        const absTxt  = absDiff != null
            ? `${absDiff >= 0 ? '+' : ''}${cur.symbol}${Math.abs(absDiff).toFixed(2)}`
            : '—';
        const absCls  = absDiff == null ? '' : absDiff >= 0 ? 'pos' : 'neg';

        const cid = `ai-spark-${s.symbol}`;

        const card = document.createElement('div');
        card.className = 'asset-item';
        card.innerHTML = `
            <div class="ai-top">
                <div class="ai-name">${s.symbol}</div>
                <div class="ai-badge ${cls}">${pctTxt}</div>
            </div>
            <div class="price-top">
               <div class="ai-price">${curP}</div>
               <div class="ai-abs ${absCls}">${absTxt}</div>
            </div>
            <div class="ai-chart"><canvas id="${cid}"></canvas></div>`;
        strip.appendChild(card);

        setTimeout(() => buildSparkline(cid, s.history || [], isGain), 0);
    });

    // Update label + pagination
    const cfg = COUNTRY_CONFIG[activeCountry];
    document.getElementById('assetsLabel').textContent =
        `Portfolio · ${cfg.label.replace(' Markets','')} (${cfg.currency.symbol === '$' ? 'USD' : cfg.currency.symbol === '₹' ? 'INR' : 'CAD'})`;
    //document.getElementById('assetsRegion').textContent = cfg.short + ' ▾';
    document.getElementById('assetsRegion').textContent = cfg.short;


    // Pagination controls
    const pg = document.getElementById('assetPagination');
    if (totalPages > 1) {
        pg.style.display = 'flex';
        document.getElementById('apPrev').disabled = assetPage === 0;
        document.getElementById('apNext').disabled = assetPage >= totalPages - 1;
        document.getElementById('apInfo').textContent = `Page ${assetPage + 1} / ${totalPages}`;
    } else {
        pg.style.display = 'none';
    }
}

function assetNextPage() { if (cachedPortfolio) { assetPage++; renderAssetCards(cachedPortfolio); } }
function assetPrevPage() { if (cachedPortfolio && assetPage > 0) { assetPage--; renderAssetCards(cachedPortfolio); } }

// ── Fetch ────────────────────────────────────────────────────────────────────
async function fetchLiveData() {
    if (loadingLive) return;
    loadingLive = true;
    document.getElementById('assetRefreshBtn').classList.add('loading');

    try {
        const r    = await fetch(LIVE_URL, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const json = await r.json();
        if (!json.success) throw new Error();

        // Remove skeletons
        document.querySelectorAll('[id^="skel-asset-"]').forEach(el => el.remove());

        cachedPortfolio = json.data;
        if (json.fx) cachedFx = json.fx;

        renderAssetCards(cachedPortfolio);

        if (json.market_status) {
            const ms   = json.market_status;
            const open = Object.values(ms).some(m => m.open);
            document.getElementById('navMarketStatus').textContent =
                `${open ? 'Markets Open' : 'Markets Closed'} · ${ms.India?.time || ''}`;
        }

        const ts = new Date().toLocaleTimeString('en-IN', { hour12: true, timeZone: 'Asia/Kolkata' });
        document.getElementById('lastUpdatedTxt').textContent = `Updated ${ts}`;

    } catch (e) {
        document.getElementById('lastUpdatedTxt').textContent = 'Error — retrying…';
    } finally {
        loadingLive = false;
        document.getElementById('assetRefreshBtn').classList.remove('loading');
    }
}

fetchLiveData();
setInterval(fetchLiveData, REFRESH_MS);
</script>


{{-- ══ Bar Chart Panels (Stocks / Crypto / Commodities) ══ --}}
<script>
const DATA = @json($data);

let panelState = { left: 'stocks', right: 'stocks' };

function setTab(tab, el, side) {
    panelState[side] = tab;
    document.querySelectorAll(`#box-${side} .tab`).forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    renderPanel(side);
}

function barColor(v) {
    if (v < 0) return '#ef4444';
    if (v < 1) return '#facc15';
    return '#22c55e';
}

function renderPanel(side) {
    const list = document.getElementById(`list-${side}`);
    list.innerHTML = '';
    let data = DATA[panelState[side]] || [];
    if (!data.length) { list.innerHTML = `<div style="color:#64748b;font-size:12px;padding:10px;">No data</div>`; return; }

    data.sort((a, b) => b.value - a.value);
    const max = Math.max(...data.map(d => Math.abs(d.value))) || 1;

    data.forEach(d => {
        const width = (Math.abs(d.value) / max) * 100;
        list.innerHTML += `
            <div class="row">
                <div class="name">${d.name}</div>
                <div class="bar"><div class="fill" style="width:${width}%;background:${barColor(d.value)};"></div></div>
                <div class="val" style="color:${barColor(d.value)}">${d.value > 0 ? '+' : ''}${d.value.toFixed(2)}%</div>
            </div>`;
    });
}

function switchGLTab(el, id) {
    document.querySelectorAll('.gl-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

renderPanel('left');
renderPanel('right');
</script>


@endsection