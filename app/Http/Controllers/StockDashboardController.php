<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StockDashboardController extends Controller
{
    // ── Static portfolio (entry prices never change) ───────────────────────
    private array $portfolio = [
        ['symbol'=>'INTC','name'=>'Intel Corporation',               'exchange'=>'NASDAQ','mcap'=>'474.9B','entry_price'=>45.55, 'date_added'=>'2026-01-12'],
        ['symbol'=>'BE',  'name'=>'Bloom Energy Corporation',        'exchange'=>'NYSE',  'mcap'=>'68.1B', 'entry_price'=>148.97,'date_added'=>'2026-01-16'],
        ['symbol'=>'SIMO','name'=>'Silicon Motion Technology Corp.',  'exchange'=>'NASDAQ','mcap'=>'7.3B',  'entry_price'=>149.84,'date_added'=>'2026-04-24'],
        ['symbol'=>'VRT', 'name'=>'Vertiv Holdings Co',              'exchange'=>'NYSE',  'mcap'=>'126.2B','entry_price'=>234.41,'date_added'=>'2026-02-13'],

        // 👇 NEW STOCKS ADD KAR
        ['symbol'=>'AAPL','name'=>'Apple Inc','exchange'=>'NASDAQ','mcap'=>'2.9T','entry_price'=>180.00,'date_added'=>'2026-03-01'],
        ['symbol'=>'MSFT','name'=>'Microsoft Corp','exchange'=>'NASDAQ','mcap'=>'3.1T','entry_price'=>320.00,'date_added'=>'2026-03-05'],
        ['symbol'=>'TSLA','name'=>'Tesla Inc','exchange'=>'NASDAQ','mcap'=>'800B','entry_price'=>250.00,'date_added'=>'2026-03-10'],
        ['symbol'=>'NVDA','name'=>'NVIDIA Corp','exchange'=>'NASDAQ','mcap'=>'2.5T','entry_price'=>900.00,'date_added'=>'2026-03-15'],

    ];

    // ── Blade view ─────────────────────────────────────────────────────────
    public function index()
    {

       $data = [
            'stocks' => [
                ['name'=>'Apple', 'symbol'=>'AAPL', 'value'=>1.25],
                ['name'=>'Microsoft', 'symbol'=>'MSFT', 'value'=>0.98],
                ['name'=>'NVIDIA', 'symbol'=>'NVDA', 'value'=>3.12],
                ['name'=>'Tesla', 'symbol'=>'TSLA', 'value'=>-1.02],
                ['name'=>'Amazon', 'symbol'=>'AMZN', 'value'=>0.56],
                ['name'=>'Google', 'symbol'=>'GOOGL', 'value'=>0.88],
                ['name'=>'Meta', 'symbol'=>'META', 'value'=>-0.44],
                ['name'=>'AMD', 'symbol'=>'AMD', 'value'=>2.11],
                ['name'=>'Netflix', 'symbol'=>'NFLX', 'value'=>-0.75],
                ['name'=>'Intel', 'symbol'=>'INTC', 'value'=>0.21],
                ['name'=>'Berkshire', 'symbol'=>'BRK', 'value'=>0.61],
                ['name'=>'Visa', 'symbol'=>'V', 'value'=>0.44],
            ],

            'crypto' => [
                ['name'=>'Bitcoin', 'symbol'=>'BTC', 'value'=>2.44],
                ['name'=>'Ethereum', 'symbol'=>'ETH', 'value'=>1.67],
                ['name'=>'Solana', 'symbol'=>'SOL', 'value'=>-1.12],
                ['name'=>'XRP', 'symbol'=>'XRP', 'value'=>0.88],
                ['name'=>'Cardano', 'symbol'=>'ADA', 'value'=>-0.45],
                ['name'=>'Dogecoin', 'symbol'=>'DOGE', 'value'=>3.22],
                ['name'=>'Avalanche', 'symbol'=>'AVAX', 'value'=>1.02],
                ['name'=>'Polkadot', 'symbol'=>'DOT', 'value'=>0.61],
            ],

            'commodities' => [
                ['name'=>'Gold', 'symbol'=>'XAU', 'value'=>0.34],
                ['name'=>'Silver', 'symbol'=>'XAG', 'value'=>-0.12],
                ['name'=>'Crude Oil', 'symbol'=>'CL', 'value'=>1.22],
                ['name'=>'Natural Gas', 'symbol'=>'NG', 'value'=>-2.44],
                ['name'=>'Copper', 'symbol'=>'HG', 'value'=>0.77],
                ['name'=>'Wheat', 'symbol'=>'ZW', 'value'=>1.05],
                ['name'=>'Corn', 'symbol'=>'ZC', 'value'=>0.42],
                ['name'=>'Soybean', 'symbol'=>'ZS', 'value'=>0.18],
            ],
        ];


         return view('dashboard.stocks', compact('data'));


    }

    // ── JSON API ───────────────────────────────────────────────────────────
    public function liveData()
    {
        $fmpKey    = env('FMP_API_KEY');
        $twelveKey = env('TWELVE_API_KEY');

        // 1. FMP batch quote — current prices
        $symbols   = implode(',', array_column($this->portfolio, 'symbol'));
        $quotes    = Cache::remember("fmp_q_{$symbols}", 60, function () use ($fmpKey, $symbols) {
            $r = Http::timeout(10)->get("https://financialmodelingprep.com/api/v3/quote/{$symbols}", ['apikey' => $fmpKey]);
            return $r->successful() ? collect($r->json())->keyBy('symbol') : collect();
        });

        // 2. Currency rates USD→INR and USD→CAD
        $rates = Cache::remember('fx_rates', 3600, function () use ($twelveKey) {
            $inr = Http::timeout(8)->get('https://api.twelvedata.com/price', ['symbol'=>'USD/INR','apikey'=>$twelveKey]);
            $cad = Http::timeout(8)->get('https://api.twelvedata.com/price', ['symbol'=>'USD/CAD','apikey'=>$twelveKey]);
            return [
                'INR' => $inr->successful() ? (float)($inr->json()['price'] ?? 83.5) : 83.5,
                'CAD' => $cad->successful() ? (float)($cad->json()['price'] ?? 1.36) : 1.36,
            ];
        });

        // 3. Twelve Data history per symbol (direct time_series)
        $stocks = [];
        foreach ($this->portfolio as $item) {
            $sym   = $item['symbol'];
            $quote = $quotes->get($sym);

            $currentPrice = $quote ? (float)$quote['price'] : null;

            // Gain % = (current - entry) / entry × 100
            $gainPct = ($currentPrice && $item['entry_price'] > 0)
                ? round((($currentPrice - $item['entry_price']) / $item['entry_price']) * 100, 2)
                : null;

            // Twelve Data time_series — last 60 daily closes
            // URL format: https://api.twelvedata.com/time_series?symbol=INTC&interval=1day&apikey=KEY
            $history = Cache::remember("twelve_ts_{$sym}", 300, function () use ($twelveKey, $sym) {
                $r = Http::timeout(15)->get('https://api.twelvedata.com/time_series', [
                    'symbol'     => $sym,
                    'interval'   => '1day',
                    'outputsize' => 60,
                    'apikey'     => $twelveKey,
                ]);
                if (!$r->successful()) return [];
                $data   = $r->json();
                // Check for API-level error
                if (isset($data['code']) || isset($data['status']) && $data['status'] === 'error') return [];
                $values = $data['values'] ?? [];
                // Twelve returns newest first → reverse for oldest→newest
                return array_reverse(array_map(fn($v) => (float)$v['close'], $values));
            });

            // Last close from history (most recent)
            $lastClose = count($history) ? end($history) : $currentPrice;

            $stocks[] = [
                'symbol'        => $sym,
                'name'          => $item['name'],
                'exchange'      => $item['exchange'],
                'mcap'          => $item['mcap'],
                'date_added'    => $item['date_added'],
                'entry_price'   => $item['entry_price'],
                'current_price' => $currentPrice,
                'gain_pct'      => $gainPct,
                'history'       => $history,        // USD close prices array
                'last_updated'  => now()->toIso8601String(),
            ];
        }

        return response()->json([
            'success'       => true,
            'data'          => $stocks,
            'fx'            => $rates,              // {INR: 83.5, CAD: 1.36}
            'market_status' => $this->getMarketStatus(),
        ]);
    }

    // ── Market open/closed ─────────────────────────────────────────────────
    private function getMarketStatus(): array
    {
        $now = now();

        $usTime = $now->copy()->setTimezone('America/New_York');
        $usOpen = $usTime->isWeekday()
            && $usTime->format('H:i') >= '09:30'
            && $usTime->format('H:i') < '16:00';

        $inTime = $now->copy()->setTimezone('Asia/Kolkata');
        $inOpen = $inTime->isWeekday()
            && $inTime->format('H:i') >= '09:15'
            && $inTime->format('H:i') < '15:30';

        return [
            'US'     => ['open' => $usOpen, 'time' => $usTime->format('h:i A T')],
            'India'  => ['open' => $inOpen, 'time' => $inTime->format('h:i A T')],
            'Canada' => ['open' => $usOpen, 'time' => $usTime->format('h:i A T')],
        ];
    }

} 