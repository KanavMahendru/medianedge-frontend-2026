<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FinanceDashboardController extends Controller
{
    private array $portfolio = [
        ['symbol' => 'INTC', 'name' => 'Intel Corporation', 'exchange' => 'NASDAQ', 'mcap' => '474.9B', 'entry_price' => 45.55, 'date_added' => '2026-01-12'],
        ['symbol' => 'BE', 'name' => 'Bloom Energy Corporation', 'exchange' => 'NYSE', 'mcap' => '68.1B', 'entry_price' => 148.97, 'date_added' => '2026-01-16'],
        ['symbol' => 'SIMO', 'name' => 'Silicon Motion Technology Corp.', 'exchange' => 'NASDAQ', 'mcap' => '7.3B', 'entry_price' => 149.84, 'date_added' => '2026-04-24'],
        ['symbol' => 'VRT', 'name' => 'Vertiv Holdings Co', 'exchange' => 'NYSE', 'mcap' => '126.2B', 'entry_price' => 234.41, 'date_added' => '2026-02-13'],
        ['symbol' => 'AAPL', 'name' => 'Apple Inc', 'exchange' => 'NASDAQ', 'mcap' => '2.9T', 'entry_price' => 180.00, 'date_added' => '2026-03-01'],
        ['symbol' => 'MSFT', 'name' => 'Microsoft Corp', 'exchange' => 'NASDAQ', 'mcap' => '3.1T', 'entry_price' => 320.00, 'date_added' => '2026-03-05'],
        ['symbol' => 'TSLA', 'name' => 'Tesla Inc', 'exchange' => 'NASDAQ', 'mcap' => '800B', 'entry_price' => 250.00, 'date_added' => '2026-03-10'],
        ['symbol' => 'NVDA', 'name' => 'NVIDIA Corp', 'exchange' => 'NASDAQ', 'mcap' => '2.5T', 'entry_price' => 900.00, 'date_added' => '2026-03-15'],
    ];

    public function index()
    {
        $data = [
            'stocks' => [
                ['name' => 'Apple', 'symbol' => 'AAPL', 'value' => 1.25],
                ['name' => 'Microsoft', 'symbol' => 'MSFT', 'value' => 0.98],
                ['name' => 'NVIDIA', 'symbol' => 'NVDA', 'value' => 3.12],
                ['name' => 'Tesla', 'symbol' => 'TSLA', 'value' => -1.02],
                ['name' => 'Amazon', 'symbol' => 'AMZN', 'value' => 0.56],
                ['name' => 'Google', 'symbol' => 'GOOGL', 'value' => 0.88],
                ['name' => 'Meta', 'symbol' => 'META', 'value' => -0.44],
                ['name' => 'AMD', 'symbol' => 'AMD', 'value' => 2.11],
                ['name' => 'Netflix', 'symbol' => 'NFLX', 'value' => -0.75],
                ['name' => 'Intel', 'symbol' => 'INTC', 'value' => 0.21],
                ['name' => 'Berkshire', 'symbol' => 'BRK', 'value' => 0.61],
                ['name' => 'Visa', 'symbol' => 'V', 'value' => 0.44],
            ],
            'crypto' => [
                ['name' => 'Bitcoin', 'symbol' => 'BTC', 'value' => 2.44],
                ['name' => 'Ethereum', 'symbol' => 'ETH', 'value' => 1.67],
                ['name' => 'Solana', 'symbol' => 'SOL', 'value' => -1.12],
                ['name' => 'XRP', 'symbol' => 'XRP', 'value' => 0.88],
                ['name' => 'Cardano', 'symbol' => 'ADA', 'value' => -0.45],
                ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'value' => 3.22],
                ['name' => 'Avalanche', 'symbol' => 'AVAX', 'value' => 1.02],
                ['name' => 'Polkadot', 'symbol' => 'DOT', 'value' => 0.61],
            ],
            'commodities' => [
                ['name' => 'Gold', 'symbol' => 'XAU', 'value' => 0.34],
                ['name' => 'Silver', 'symbol' => 'XAG', 'value' => -0.12],
                ['name' => 'Crude Oil', 'symbol' => 'CL', 'value' => 1.22],
                ['name' => 'Natural Gas', 'symbol' => 'NG', 'value' => -2.44],
                ['name' => 'Copper', 'symbol' => 'HG', 'value' => 0.77],
                ['name' => 'Wheat', 'symbol' => 'ZW', 'value' => 1.05],
                ['name' => 'Corn', 'symbol' => 'ZC', 'value' => 0.42],
                ['name' => 'Soybean', 'symbol' => 'ZS', 'value' => 0.18],
            ],
        ];

        $topAssets = [
            "US" => [
                ["name" => "S&P 500", "price" => "5,234.18", "change_pct" => 1.25, "change_abs" => "+64.12", "history" => [5100, 5150, 5120, 5200, 5180, 5234.18], "symbol" => "SPX"],
                ["name" => "Dow Jones", "price" => "39,512.44", "change_pct" => 0.82, "change_abs" => "+320.10", "history" => [39000, 39100, 39050, 39300, 39400, 39512.44], "symbol" => "DJI"],
                ["name" => "Nasdaq", "price" => "16,428.82", "change_pct" => 1.55, "change_abs" => "+251.10", "history" => [16000, 16100, 16050, 16300, 16200, 16428.82], "symbol" => "IXIC"],
                ["name" => "Bitcoin", "price" => "US$80,797.49", "change_pct" => 1.33, "change_abs" => "+US$1,061.79", "history" => [79000, 79200, 78500, 79800, 80500, 80797.49], "symbol" => "BTC"],
                ["name" => "Apple Inc", "price" => "173.50", "change_pct" => -0.50, "change_abs" => "-0.87", "history" => [175, 174, 176, 172, 174, 173.50], "symbol" => "AAPL"],
                ["name" => "Tesla Inc", "price" => "170.83", "change_pct" => -2.12, "change_abs" => "-3.70", "history" => [180, 178, 175, 172, 171, 170.83], "symbol" => "TSLA"]
            ],
            "India" => [
                ["name" => "NIFTY 50", "price" => "23,922.10", "change_pct" => -0.82, "change_abs" => "-197.20", "history" => [24100, 24050, 24000, 24150, 23900, 23922.10], "symbol" => "NIFTY"],
                ["name" => "S&P BSE Sensex", "price" => "76,530.50", "change_pct" => -0.96, "change_abs" => "-738.90", "history" => [77000, 76800, 77200, 76500, 76400, 76530.50], "symbol" => "SENSEX"],
                ["name" => "Nifty Bank Index", "price" => "54,258.35", "change_pct" => -1.13, "change_abs" => "-620.15", "history" => [55000, 54800, 54900, 54100, 54000, 54258.35], "symbol" => "BANKNIFTY"],
                ["name" => "Nifty IT", "price" => "35,102.40", "change_pct" => 0.45, "change_abs" => "+158.20", "history" => [34800, 34900, 34700, 35000, 34950, 35102.40], "symbol" => "NIFTYIT"]
            ],
            "Canada" => [
                ["name" => "S&P/TSX Composite", "price" => "22,123.45", "change_pct" => 0.35, "change_abs" => "+78.20", "history" => [21900, 22000, 21950, 22100, 22050, 22123.45], "symbol" => "TSX"],
                ["name" => "S&P/TSX 60", "price" => "1,324.50", "change_pct" => 0.40, "change_abs" => "+5.20", "history" => [1310, 1315, 1312, 1320, 1318, 1324.50], "symbol" => "TSX60"],
                ["name" => "Shopify", "price" => "CA$105.20", "change_pct" => -1.25, "change_abs" => "-1.33", "history" => [110, 108, 109, 106, 107, 105.20], "symbol" => "SHOP.TO"],
                ["name" => "Royal Bank", "price" => "CA$135.40", "change_pct" => 0.15, "change_abs" => "+0.20", "history" => [134, 134.5, 134.2, 135, 134.8, 135.40], "symbol" => "RY.TO"]
            ]
        ];

        $standouts = $this->getStandoutsData();
        $watchlist = [
            [
                "name" => "HDFC Bank Limited",
                "symbol" => "HDFCBANK",
                "exchange" => "NSE",
                "price" => "773.20",
                "change_pct" => -0.80,
                "logo" => "H"
            ],
            [
                "name" => "ICICI Lombard General...",
                "symbol" => "ICICIGI",
                "exchange" => "NSE",
                "price" => "1,776.90",
                "change_pct" => 1.14,
                "logo" => "I"
            ],
            [
                "name" => "Tata Technologies Lim...",
                "symbol" => "TATATECH",
                "exchange" => "NSE",
                "price" => "624.05",
                "change_pct" => 5.58,
                "logo" => "T"
            ],
            [
                "name" => "Reliance Industries Li...",
                "symbol" => "RELIANCE",
                "exchange" => "BSE",
                "price" => "1,464.15",
                "change_pct" => 0.08,
                "logo" => "R"
            ]
        ];
        $predictionMarkets = [
            [
                "question" => "What will WTI Crude Oil (WTI) hit in May 2026?",
                "options" => [
                    ["label" => "↓ $100", "pct" => 91.0, "delta" => 0.0],
                    ["label" => "↑ $110", "pct" => 79.0, "delta" => -6.0],
                    ["label" => "↓ $95", "pct" => 76.0, "delta" => -1.0]
                ],
                "volume" => "7.3M",
                "more_count" => 18,
                "source" => "Polymarket"
            ],
            [
                "question" => "Largest Company end of June?",
                "options" => [
                    ["label" => "NVIDIA", "pct" => 70.0, "delta" => 1.0],
                    ["label" => "Alphabet", "pct" => 29.0, "delta" => -0.2],
                    ["label" => "Apple", "pct" => 2.0, "delta" => -0.6]
                ],
                "volume" => "9.6M",
                "more_count" => 4,
                "source" => "Polymarket"
            ],
            [
                "question" => "How many Fed rate cuts in 2026?",
                "options" => [
                    ["label" => "0 (0 bps)", "pct" => 59.0, "delta" => 4.8],
                    ["label" => "1 (25 bps)", "pct" => 19.0, "delta" => 2.0],
                    ["label" => "2 (50 bps)", "pct" => 12.0, "delta" => 0.0]
                ],
                "volume" => "12.4M",
                "more_count" => 7,
                "source" => "Polymarket"
            ]
        ];
        $marketTabs = [
            "gainers" => [
                ["name" => "Cemindia Projects Limit...", "symbol" => "CEMPRO", "exchange" => "BSE", "price" => "814.55", "change_pct" => 20.00, "logo" => "C"],
                ["name" => "ITD Cementation India Li...", "symbol" => "ITDCEM", "exchange" => "BSE", "price" => "814.55", "change_pct" => 20.00, "logo" => "I"],
                ["name" => "MTAR Technologies Li...", "symbol" => "MTARTECH", "exchange" => "BSE", "price" => "6,450.80", "change_pct" => 14.09, "logo" => "M"],
                ["name" => "Vodafone Idea Limited", "symbol" => "IDEA", "exchange" => "BSE", "price" => "10.80", "change_pct" => 2.56, "logo" => "V"]
            ],
            "losers" => [
                ["name" => "SpiceJet Limited", "symbol" => "SPICEJET", "exchange" => "BSE", "price" => "12.10", "change_pct" => -4.80, "logo" => "S"],
                ["name" => "Yes Bank Limited", "symbol" => "YESBANK", "exchange" => "BSE", "price" => "20.50", "change_pct" => -2.71, "logo" => "Y"],
                ["name" => "Suzlon Energy Limited", "symbol" => "SUZLON", "exchange" => "BSE", "price" => "42.30", "change_pct" => -1.15, "logo" => "S"],
                ["name" => "Zomato Limited", "symbol" => "ZOMATO", "exchange" => "BSE", "price" => "184.20", "change_pct" => -0.85, "logo" => "Z"]
            ],
            "active" => [
                ["name" => "Vodafone Idea Limited", "symbol" => "IDEA", "exchange" => "BSE", "price" => "10.80", "change_pct" => 2.56, "logo" => "V"],
                ["name" => "Jaiprakash Power Ventures...", "symbol" => "JPPOWER", "exchange" => "BSE", "price" => "19.20", "change_pct" => 0.73, "logo" => "J"],
                ["name" => "SpiceJet Limited", "symbol" => "SPICEJET", "exchange" => "BSE", "price" => "12.10", "change_pct" => -4.80, "logo" => "S"],
                ["name" => "Yes Bank Limited", "symbol" => "YESBANK", "exchange" => "BSE", "price" => "20.50", "change_pct" => 2.71, "logo" => "Y"]
            ]
        ];
        $cryptoData = [
            ["name" => "Bitcoin", "symbol" => "BTCUSD", "exchange" => "CRYPTO", "price" => "81,007.05", "change_pct" => 2.83, "logo" => "₿"],
            ["name" => "Ethereum", "symbol" => "ETHUSD", "exchange" => "CRYPTO", "price" => "2,379.39", "change_pct" => 1.80, "logo" => "Ξ"],
            ["name" => "Solana", "symbol" => "SOLUSD", "exchange" => "CRYPTO", "price" => "84.95", "change_pct" => 1.20, "logo" => "◎"],
            ["name" => "XRP", "symbol" => "XRPUSD", "exchange" => "CRYPTO", "price" => "1.41", "change_pct" => 0.99, "logo" => "✕"]
        ];
        $marketSummary = [
            "US" => [
                [
                    "title" => "Tech Stocks Lead Market Rally as AI Enthusiasm Grows",
                    "content" => "Nvidia and Microsoft drove significant gains in the S&P 500 today, as investors cheered strong forward guidance on AI infrastructure spending.",
                    "sources" => 12
                ],
                [
                    "title" => "Fed Officials Signal Caution on Future Interest Rate Cuts",
                    "content" => "Inflation data remains stubborn, leading some members to suggest that current rates might stay higher for longer than previously anticipated.",
                    "sources" => 8
                ],
                [
                    "title" => "Energy Stocks Slip as Oil Prices Pull Back",
                    "content" => "Crude oil prices declined slightly in global markets, dragging down major energy companies as demand outlook remains uncertain.",
                    "sources" => 6
                ],
                [
                    "title" => "Retail Sales Beat Expectations in Latest Data Release",
                    "content" => "Consumer spending showed resilience despite higher interest rates, indicating strong underlying economic momentum.",
                    "sources" => 9
                ],
                [
                    "title" => "Healthcare Sector Gains on Drug Approval News",
                    "content" => "Biotech and pharmaceutical stocks moved higher after positive regulatory updates boosted investor sentiment.",
                    "sources" => 7
                ]
            ],

            "India" => [
                [
                    "title" => "Nifty Bank Index Under Pressure Amid PSU Bank Sell-off",
                    "content" => "Several large public sector banks saw heavy volume selling today as quarterly results missed analyst projections on asset quality.",
                    "sources" => 5
                ],
                [
                    "title" => "IT Stocks Trade Flat Ahead of Earnings Season",
                    "content" => "Major IT firms remained range-bound as investors await guidance on global demand and deal pipelines.",
                    "sources" => 6
                ],
                [
                    "title" => "Auto Sector Gains on Strong Monthly Sales Data",
                    "content" => "Passenger vehicle and SUV sales showed strong growth, boosting optimism across the auto sector.",
                    "sources" => 7
                ],
                [
                    "title" => "Rupee Weakens Slightly Against US Dollar",
                    "content" => "The Indian rupee edged lower amid global dollar strength and rising crude oil prices.",
                    "sources" => 4
                ],
                [
                    "title" => "FMCG Stocks Show Defensive Buying Interest",
                    "content" => "Investors rotated into defensive sectors like FMCG as broader markets remained volatile.",
                    "sources" => 5
                ]
            ],

            "Canada" => [
                [
                    "title" => "Housing Market Shows Signs of Cooling in Major Cities",
                    "content" => "Recent data indicates a slowdown in housing sales across Toronto and Vancouver, as higher borrowing costs continue to impact buyer affordability.",
                    "sources" => 7
                ],
                [
                    "title" => "Energy Sector Supported by Stable Oil Prices",
                    "content" => "Canadian energy companies held steady as crude prices stabilized after recent volatility.",
                    "sources" => 6
                ],
                [
                    "title" => "Banking Stocks Remain Resilient Ahead of Earnings",
                    "content" => "Major Canadian banks traded within a narrow range as investors awaited quarterly earnings results.",
                    "sources" => 5
                ],
                [
                    "title" => "Tech Stocks Under Pressure Amid Global Sell-off",
                    "content" => "Technology shares in Canada followed global trends lower as investors trimmed positions in high-growth names.",
                    "sources" => 8
                ],
                [
                    "title" => "Inflation Data Keeps Rate Outlook Uncertain",
                    "content" => "Mixed inflation signals have left markets uncertain about the Bank of Canada's next policy move.",
                    "sources" => 6
                ]
            ]
        ];

        $heatmapData = [
            "US" => [
                [
                    "name" => "Technology",
                    "col" => "span 8",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "AAPL", "pct" => "+1.45%", "col" => "span 6", "row" => "span 6", "color" => "hm-g2", "name" => "Apple Inc.", "price" => "$182.52", "industry" => "Electronics", "summary" => "Apple shares climbed...", "sources" => 12],
                        ["ticker" => "MSFT", "pct" => "-0.82%", "col" => "span 6", "row" => "span 3", "color" => "hm-r1", "name" => "Microsoft Corp", "price" => "$415.10", "industry" => "Software", "summary" => "Microsoft pressure...", "sources" => 8],
                        ["ticker" => "NVDA", "pct" => "+3.10%", "col" => "span 3", "row" => "span 3", "color" => "hm-g3", "name" => "NVIDIA Corp", "price" => "$890.45", "industry" => "Chips", "summary" => "AI leader rises...", "sources" => 15],
                        ["ticker" => "AVGO", "pct" => "+0.95%", "col" => "span 3", "row" => "span 1", "color" => "hm-g1", "name" => "Broadcom Inc.", "price" => "$1350.20", "industry" => "Chips", "summary" => "Broadcom gains...", "sources" => 6],
                        ["ticker" => "AMD", "pct" => "+2.05%", "col" => "span 3", "row" => "span 2", "color" => "hm-g2", "name" => "Advanced Micro Devices", "price" => "$165.20", "industry" => "Chips", "summary" => "AMD gains...", "sources" => 9],
                        ["ticker" => "ADBE", "pct" => "-1.20%", "col" => "span 2", "row" => "span 2", "color" => "hm-r1", "name" => "Adobe Inc.", "price" => "$480.15", "industry" => "Software", "summary" => "Adobe dips...", "sources" => 4],
                        ["ticker" => "CRM", "pct" => "+0.55%", "col" => "span 2", "row" => "span 2", "color" => "hm-g1", "name" => "Salesforce", "price" => "$305.20", "industry" => "Software", "summary" => "CRM edges up...", "sources" => 5],
                        ["ticker" => "INTC", "pct" => "+0.12%", "col" => "span 2", "row" => "span 2", "color" => "hm-n", "name" => "Intel Corp", "price" => "$42.50", "industry" => "Chips", "summary" => "Intel stable...", "sources" => 6],
                        ["ticker" => "QCOM", "pct" => "+1.40%", "col" => "span 3", "row" => "span 1", "color" => "hm-g1", "name" => "Qualcomm", "price" => "$168.40", "industry" => "Chips", "summary" => "QCOM gains...", "sources" => 7]
                    ]
                ],
                [
                    "name" => "Comms & Consumer",
                    "col" => "span 4",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "GOOGL", "pct" => "+0.55%", "col" => "span 12", "row" => "span 3", "color" => "hm-g1", "name" => "Alphabet Inc.", "price" => "$154.20", "industry" => "Internet", "summary" => "Google cloud gains...", "sources" => 9],
                        ["ticker" => "META", "pct" => "+1.12%", "col" => "span 12", "row" => "span 3", "color" => "hm-g1", "name" => "Meta Platforms", "price" => "$485.30", "industry" => "Social", "summary" => "Meta ad growth...", "sources" => 7],
                        ["ticker" => "AMZN", "pct" => "-0.25%", "col" => "span 6", "row" => "span 3", "color" => "hm-n", "name" => "Amazon.com", "price" => "$178.15", "industry" => "Retail", "summary" => "Amazon flat...", "sources" => 11],
                        ["ticker" => "TSLA", "pct" => "-2.45%", "col" => "span 6", "row" => "span 3", "color" => "hm-r2", "name" => "Tesla Inc.", "price" => "$170.83", "industry" => "Auto", "summary" => "Tesla headwinds...", "sources" => 14],
                        ["ticker" => "NFLX", "pct" => "+1.75%", "col" => "span 6", "row" => "span 3", "color" => "hm-g2", "name" => "Netflix Inc.", "price" => "$610.25", "industry" => "Media", "summary" => "Netflix rises...", "sources" => 8],
                        ["ticker" => "DIS", "pct" => "-0.40%", "col" => "span 6", "row" => "span 3", "color" => "hm-n", "name" => "Disney", "price" => "$112.40", "industry" => "Media", "summary" => "Disney stable...", "sources" => 5]
                    ]
                ]
            ],
            "India" => [
                [
                    "name" => "Financials",
                    "col" => "span 6",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "HDFCBANK.NS", "pct" => "-1.25%", "col" => "span 12", "row" => "span 4", "color" => "hm-r1", "name" => "HDFC Bank", "price" => "₹1,450.20", "industry" => "Banks", "summary" => "HDFC declining...", "sources" => 4],
                        ["ticker" => "SBIN.NS", "pct" => "-0.74%", "col" => "span 6", "row" => "span 4", "color" => "hm-r05", "name" => "State Bank", "price" => "₹825.40", "industry" => "Banks", "summary" => "SBI lower...", "sources" => 8],
                        ["ticker" => "ICICIBANK.NS", "pct" => "-1.55%", "col" => "span 6", "row" => "span 4", "color" => "hm-r2", "name" => "ICICI Bank", "price" => "₹1,090.15", "industry" => "Banks", "summary" => "ICICI profit...", "sources" => 6],
                        ["ticker" => "AXISBANK.NS", "pct" => "-0.65%", "col" => "span 12", "row" => "span 4", "color" => "hm-r05", "name" => "Axis Bank", "price" => "₹1,120.30", "industry" => "Banks", "summary" => "Axis Bank lower...", "sources" => 6]
                    ]
                ],
                [
                    "name" => "Energy & IT",
                    "col" => "span 6",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "RELIANCE.NS", "pct" => "+0.12%", "col" => "span 12", "row" => "span 4", "color" => "hm-g05", "name" => "Reliance", "price" => "₹2,950.10", "industry" => "Oil", "summary" => "Reliance up...", "sources" => 16],
                        ["ticker" => "TCS.NS", "pct" => "-0.17%", "col" => "span 12", "row" => "span 4", "color" => "hm-n", "name" => "TCS", "price" => "₹3,920.45", "industry" => "IT", "summary" => "TCS range...", "sources" => 10],
                        ["ticker" => "INFY.NS", "pct" => "+0.25%", "col" => "span 6", "row" => "span 4", "color" => "hm-g1", "name" => "Infosys", "price" => "₹1,480.20", "industry" => "IT", "summary" => "Infosys buying...", "sources" => 7],
                        ["ticker" => "ONGC.NS", "pct" => "-0.80%", "col" => "span 6", "row" => "span 4", "color" => "hm-r1", "name" => "ONGC", "price" => "₹280.15", "industry" => "Oil", "summary" => "ONGC dips...", "sources" => 4]
                    ]
                ]
            ],
            "Canada" => [
                [
                    "name" => "Financials",
                    "col" => "span 8",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "RY.TO", "pct" => "+0.45%", "col" => "span 12", "row" => "span 6", "color" => "hm-g1", "name" => "Royal Bank", "price" => "C$138.20", "industry" => "Banks", "summary" => "RBC gains...", "sources" => 7],
                        ["ticker" => "TD.TO", "pct" => "+0.10%", "col" => "span 12", "row" => "span 6", "color" => "hm-n", "name" => "TD Bank", "price" => "C$82.15", "industry" => "Banks", "summary" => "Stable TD...", "sources" => 5]
                    ]
                ],
                [
                    "name" => "Energy & Tech",
                    "col" => "span 4",
                    "row" => "span 6",
                    "stocks" => [
                        ["ticker" => "ENB.TO", "pct" => "-0.35%", "col" => "span 12", "row" => "span 4", "color" => "hm-n", "name" => "Enbridge", "price" => "C$48.50", "industry" => "Midstream", "summary" => "Enbridge range...", "sources" => 8],
                        ["ticker" => "SU.TO", "pct" => "+0.55%", "col" => "span 12", "row" => "span 4", "color" => "hm-g1", "name" => "Suncor", "price" => "C$52.10", "industry" => "Oil", "summary" => "Suncor rises...", "sources" => 6],
                        ["ticker" => "SHOP.TO", "pct" => "-2.10%", "col" => "span 12", "row" => "span 4", "color" => "hm-r2", "name" => "Shopify", "price" => "C$102.45", "industry" => "Software", "summary" => "Shopify dip...", "sources" => 12]
                    ]
                ]
            ]
        ];

        $fmpKey = env('FMP_API_KEY');
        $recentDevelopments = Cache::remember('recent_developments_live', 600, function () use ($fmpKey) {
            try {
                $r = Http::timeout(10)->get("https://financialmodelingprep.com/api/v3/stock_news", [
                    'limit' => 3,
                    'apikey' => $fmpKey
                ]);

                if ($r->successful()) {
                    return collect($r->json())->map(function ($n) {
                        $site = $n['site'] ?? 'N';
                        $char = strtoupper(substr($site, 0, 1));
                        return [
                            "title" => $n['title'],
                            "time" => \Carbon\Carbon::parse($n['publishedDate'])->diffForHumans(),
                            "content" => $n['text'],
                            "sources_icons" => [
                                ["char" => $char, "bg" => "#3b82f6", "color" => "#fff"],
                                ["char" => "A", "bg" => "#000", "color" => "#fff"]
                            ]
                        ];
                    })->toArray();
                }
            } catch (\Exception $e) {
            }

            // Fallback: No dummy data to verify dynamic nature
            return [];
        });

        return view('finance.dashboard', compact('data', 'topAssets', 'standouts', 'watchlist', 'predictionMarkets', 'marketTabs', 'cryptoData', 'marketSummary', 'heatmapData', 'recentDevelopments'));
    }

    public function liveData()
    {
        $fmpKey = env('FMP_API_KEY');
        $twelveKey = env('TWELVE_API_KEY');

        $symbols = implode(',', array_column($this->portfolio, 'symbol'));
        $quotes = Cache::remember("fmp_q_{$symbols}", 60, function () use ($fmpKey, $symbols) {
            $r = Http::timeout(10)->get("https://financialmodelingprep.com/api/v3/quote/{$symbols}", ['apikey' => $fmpKey]);
            return $r->successful() ? collect($r->json())->keyBy('symbol') : collect();
        });

        $rates = Cache::remember('fx_rates', 3600, function () use ($twelveKey) {
            $inr = Http::timeout(8)->get('https://api.twelvedata.com/price', ['symbol' => 'USD/INR', 'apikey' => $twelveKey]);
            $cad = Http::timeout(8)->get('https://api.twelvedata.com/price', ['symbol' => 'USD/CAD', 'apikey' => $twelveKey]);
            return [
                'INR' => $inr->successful() ? (float) ($inr->json()['price'] ?? 83.5) : 83.5,
                'CAD' => $cad->successful() ? (float) ($cad->json()['price'] ?? 1.36) : 1.36,
            ];
        });

        $stocks = [];
        foreach ($this->portfolio as $item) {
            $sym = $item['symbol'];
            $quote = $quotes->get($sym);
            $currentPrice = $quote ? (float) $quote['price'] : null;
            $gainPct = ($currentPrice && $item['entry_price'] > 0)
                ? (($currentPrice - $item['entry_price']) / $item['entry_price']) * 100
                : 0;

            $history = Cache::remember("twelve_ts_{$sym}", 300, function () use ($twelveKey, $sym) {
                $r = Http::timeout(15)->get('https://api.twelvedata.com/time_series', [
                    'symbol' => $sym,
                    'interval' => '1day',
                    'outputsize' => 60,
                    'apikey' => $twelveKey,
                ]);
                if (!$r->successful())
                    return [];
                $data = $r->json();
                if (isset($data['code']) || isset($data['status']) && $data['status'] === 'error')
                    return [];
                $values = $data['values'] ?? [];
                return array_reverse(array_map(fn($v) => (float) $v['close'], $values));
            });

            $stocks[] = [
                'symbol' => $sym,
                'name' => $item['name'],
                'exchange' => $item['exchange'],
                'mcap' => $item['mcap'],
                'date_added' => $item['date_added'],
                'entry_price' => $item['entry_price'],
                'current_price' => $currentPrice,
                'gain_pct' => $gainPct,
                'history' => $history,
                'last_updated' => now()->toIso8601String(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stocks,
            'fx' => $rates,
            'market_status' => $this->getMarketStatus(),
        ]);
    }

    private function getStandoutsData()
    {
        return [
            "US" => [
                [
                    "name" => "NVIDIA Corporation",
                    "symbol" => "NVDA",
                    "exchange" => "NASDAQ",
                    "price" => "894.52",
                    "change_pct" => 3.12,
                    "prev_close" => "867.45",
                    "stats" => [
                        "Volume" => "42.1M",
                        "Market Cap" => "2.23T",
                        "P/E Ratio" => "74.12",
                        "Dividend Yield" => "0.02%"
                    ],
                    "desc" => "NVIDIA shares surged after reporting record-breaking revenue in its data center division, driven by insatiable demand for H100 AI chips across major cloud providers.",
                    "history" => [850, 860, 855, 870, 865, 894.52],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ],
                [
                    "name" => "Apple Inc.",
                    "symbol" => "AAPL",
                    "exchange" => "NASDAQ",
                    "price" => "172.62",
                    "change_pct" => -0.85,
                    "prev_close" => "174.10",
                    "stats" => [
                        "Volume" => "58.4M",
                        "Market Cap" => "2.66T",
                        "P/E Ratio" => "26.45",
                        "Dividend Yield" => "0.56%"
                    ],
                    "desc" => "Apple shares drifted lower as investors weighed concerns about slowing iPhone demand in China against optimism for upcoming AI announcements at WWDC.",
                    "history" => [176, 175, 177, 173, 174, 172.62],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ]
            ],
            "India" => [
                [
                    "name" => "CreditAccess Grameen Limited",
                    "symbol" => "CREDITACC",
                    "exchange" => "BSE",
                    "price" => "1,539.10",
                    "change_pct" => 16.66,
                    "prev_close" => "1,319.25",
                    "stats" => [
                        "Volume" => "1.05M",
                        "Market Cap" => "246.38B",
                        "P/E Ratio" => "50.07",
                        "Dividend Yield" => "N/A"
                    ],
                    "desc" => "CreditAccess Grameen surged sharply, likely driven by a strong relief rally and renewed investor optimism in the microfinance sector, as the stock rebounded decisively from recent lows.",
                    "history" => [1350, 1400, 1420, 1450, 1480, 1539.10],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ],
                [
                    "name" => "Titagarh Rail Systems Limited",
                    "symbol" => "TITAGARH",
                    "exchange" => "BSE",
                    "price" => "841.15",
                    "change_pct" => 9.25,
                    "prev_close" => "769.90",
                    "stats" => [
                        "Volume" => "1.4M",
                        "Market Cap" => "113.28B",
                        "P/E Ratio" => "62.54",
                        "Dividend Yield" => "0.12%"
                    ],
                    "desc" => "Titagarh Rail Systems surged sharply as BJP's victory in the 2026 West Bengal elections sparked strong buying interest in Bengal-linked stocks.",
                    "history" => [760, 755, 770, 810, 830, 841.15],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ],
                [
                    "name" => "CESC Limited",
                    "symbol" => "CESC",
                    "exchange" => "BSE",
                    "price" => "186.90",
                    "change_pct" => -6.01,
                    "prev_close" => "198.85",
                    "stats" => [
                        "Volume" => "543.88K",
                        "Market Cap" => "247.75B",
                        "P/E Ratio" => "17.08",
                        "Dividend Yield" => "3.21%"
                    ],
                    "desc" => "CESC shares declined sharply as the initial euphoria from the BJP's West Bengal election victory faded, with investors booking profits following the previous session's rally.",
                    "history" => [205, 195, 192, 188, 187, 186.90],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ]
            ],
            "Canada" => [
                [
                    "name" => "Shopify Inc.",
                    "symbol" => "SHOP",
                    "exchange" => "TSX",
                    "price" => "102.45",
                    "change_pct" => -2.10,
                    "prev_close" => "104.65",
                    "stats" => [
                        "Volume" => "2.8M",
                        "Market Cap" => "131.2B",
                        "P/E Ratio" => "N/A",
                        "Dividend Yield" => "N/A"
                    ],
                    "desc" => "Shopify shares faced pressure alongside global tech names as yields ticked higher, though analysts remain focused on its expanding merchant base and cross-border commerce tools.",
                    "history" => [108, 106, 107, 104, 103, 102.45],
                    "history_labels" => ["10", "11", "12", "13", "14", "15"]
                ]
            ]
        ];
    }

    private function getMarketStatus()
    {
        $usTime = now()->setTimezone('America/New_York');
        $usOpen = $usTime->isWeekday()
            && $usTime->format('H:i') >= '09:30'
            && $usTime->format('H:i') < '16:00';

        $inTime = now()->setTimezone('Asia/Kolkata');
        $inOpen = $inTime->isWeekday()
            && $inTime->format('H:i') >= '09:15'
            && $inTime->format('H:i') < '15:30';

        return [
            'US' => ['open' => $usOpen, 'time' => $usTime->format('h:i A T')],
            'India' => ['open' => $inOpen, 'time' => $inTime->format('h:i A T')],
            'Canada' => ['open' => $usOpen, 'time' => $usTime->format('h:i A T')],
        ];
    }
}