<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
            padding: 24px;
            color: #102a43;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.02em;
        }

        .tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tab {
            padding: 10px 18px;
            border-radius: 999px;
            cursor: pointer;
            background: rgba(255,255,255,0.72);
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 600;
            color: #334e68;
        }

        .tab:hover {
            background: #fff;
            border-color: #cbd5e1;
        }

        .tab.active {
            background: #0f172a;
            color: #f8fafc;
            box-shadow: 0 10px 20px rgba(15,23,42,0.12);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .card {
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(148,163,184,0.2);
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(15,23,42,0.06);
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .card-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #0f172a;
            margin: 0;
        }

        .value {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
        }

        .metric {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .change {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .change.up {
            color: #16a34a;
            background: rgba(22,163,74,0.12);
        }

        .change.down {
            color: #dc2626;
            background: rgba(220,38,38,0.12);
        }

        .sparkline {
            width: 100%;
            height: 96px;
            position: relative;
        }

        svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .sparkline path {
            fill: none;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sparkline .line-up {
            stroke: #16a34a;
        }

        .sparkline .line-down {
            stroke: #dc2626;
        }

        .sparkline .area-up {
            fill: rgba(22,163,74,0.16);
        }

        .sparkline .area-down {
            fill: rgba(220,38,38,0.12);
        }

        .sparkline .baseline {
            stroke: rgba(148,163,184,0.24);
            stroke-dasharray: 4 5;
            stroke-width: 1; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Market Dashboard</h1>
                <p style="margin: 4px 0 0; color: #627d98;">Fast overview of regional indices with mini trend charts.</p>
            </div>
            <div class="tabs">
                <div class="tab active" onclick="switchTab('india', event)">INDIA</div>
                <div class="tab" onclick="switchTab('us', event)">US</div>
                <div class="tab" onclick="switchTab('canada', event)">CANADA</div>
            </div>
        </div>

        <div class="cards" id="cards-container"></div>
    </div>

    <script>
        const data = {
            india: [
                { name: "NIFTY 50", value: "23,997.55", change: "-0.74%", dir: "down", trend: [22.4, 22.6, 22.1, 22.9, 23.2, 23.0, 23.9] },
                { name: "SENSEX", value: "76,913.50", change: "-0.75%", dir: "down", trend: [75.5, 75.8, 76.2, 76.0, 76.5, 76.9, 76.9] },
                { name: "BANK NIFTY", value: "54,863.35", change: "-0.98%", dir: "down", trend: [55.0, 54.8, 54.4, 54.7, 54.9, 54.6, 54.8] },
                { name: "BITCOIN", value: "$77,072", change: "+2.07%", dir: "up", trend: [73.4, 74.1, 75.0, 75.8, 76.5, 76.9, 77.1] },
            ],
            us: [
                { name: "S&P 500", value: "5,200.10", change: "+0.45%", dir: "up", trend: [5.03, 5.10, 5.08, 5.15, 5.18, 5.19, 5.20] },
                { name: "NASDAQ", value: "16,300.20", change: "+0.60%", dir: "up", trend: [16.0, 16.1, 16.2, 16.15, 16.25, 16.28, 16.30] },
                { name: "DOW JONES", value: "38,900.11", change: "-0.20%", dir: "down", trend: [39.1, 39.0, 38.95, 38.92, 38.90, 38.88, 38.90] },
                { name: "BITCOIN", value: "$77,072", change: "+2.07%", dir: "up", trend: [73.4, 74.1, 75.0, 75.8, 76.5, 76.9, 77.1] },
            ],
            canada: [
                { name: "TSX", value: "22,100.45", change: "+0.30%", dir: "up", trend: [21.8, 21.9, 22.0, 22.05, 22.08, 22.10, 22.10] },
                { name: "S&P/TSX 60", value: "1,300.22", change: "-0.10%", dir: "down", trend: [1.31, 1.305, 1.302, 1.300, 1.298, 1.299, 1.300] },
                { name: "BANK INDEX", value: "400.55", change: "+0.25%", dir: "up", trend: [399.0, 399.5, 399.8, 400.1, 400.3, 400.4, 400.55] },
                { name: "BITCOIN", value: "$77,072", change: "+2.07%", dir: "up", trend: [73.4, 74.1, 75.0, 75.8, 76.5, 76.9, 77.1] },
            ]
        };

        function buildSparkline(trend, dir) {
            const values = trend;
            const min = Math.min(...values);
            const max = Math.max(...values);
            const width = 240;
            const height = 96;
            const step = width / (values.length - 1);
            const normalized = values.map(value => height - ((value - min) / (max - min || 1)) * (height - 16) - 8);

            const points = normalized.map((y, index) => `${index * step},${y}`);
            const path = `M ${points.join(' L ')}`;
            const areaPoints = [`0,${height - 8}`, ...points, `${width},${height - 8}`];

            return {
                path,
                area: `M ${areaPoints.join(' L ')} Z`,
                colorClass: dir === 'up' ? 'line-up' : 'line-down',
                areaClass: dir === 'up' ? 'area-up' : 'area-down'
            };
        }

        function renderCards(region) {
            const container = document.getElementById('cards-container');
            container.innerHTML = '';

            data[region].forEach(item => {
                const spark = buildSparkline(item.trend, item.dir);
                container.innerHTML += `
                    <div class="card">
                        <div class="card-header">
                            <div class="card-meta">
                                <div class="title">${item.name}</div>
                                <div class="value">${item.value}</div>
                            </div>
                            <div class="metric">
                                <div class="change ${item.dir}">${item.change}</div>
                            </div>
                        </div>
                        <div class="sparkline">
                            <svg viewBox="0 0 240 96" preserveAspectRatio="none">
                                <path class="${spark.areaClass}" d="${spark.area}" opacity="0.18"></path>
                                <path class="baseline" d="M0,88 L240,88"></path>
                                <path class="${spark.colorClass}" d="${spark.path}"></path>
                            </svg>
                        </div>
                    </div>
                `;
            });
        }

        function switchTab(region, event) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            event.currentTarget.classList.add('active');
            renderCards(region);
        }

        renderCards('india');
    </script>

</body>
</html>