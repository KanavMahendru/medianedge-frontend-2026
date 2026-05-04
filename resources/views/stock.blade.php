<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Market Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: white;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .symbol {
            font-size: 22px;
            font-weight: bold;
        }

        .price {
            font-size: 26px;
            margin: 10px 0;
        }

        .change {
            font-weight: bold;
        }

        .green {
            color: #22c55e;
        }

        .red {
            color: #ef4444;
        }

        .footer {
            margin-top: 10px;
            font-size: 14px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <h1>📊 Stock Market Dashboard</h1>

    <div class="grid">
        @foreach($stocks as $stock)

            @php
                $isPositive = $stock['change'] >= 0;
            @endphp

            <div class="card">

                <div class="symbol">
                    {{ $stock['symbol'] }}
                </div>

                <div class="name">
                    {{ $stock['name'] }}
                </div>

                <div class="price">
                    ${{ $stock['price'] }}
                </div>

                <div class="change {{ $isPositive ? 'green' : 'red' }}">
                    {{ $stock['change'] }} ({{ $stock['changesPercentage'] }}%)
                </div>

                <div class="footer">
                    Volume: {{ number_format($stock['volume']) }}
                </div>

            </div>

        @endforeach
    </div>

</body>
</html>