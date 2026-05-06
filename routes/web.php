<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\StockDashboardController;
use App\Http\Controllers\FinanceDashboardController;


// ===== real data =======
Route::get('/', [FinanceDashboardController::class, 'index'])->name('index');
Route::get('/test', [FinanceDashboardController::class, 'index'])->name('test.index');
Route::get('/finance', [FinanceDashboardController::class, 'index'])->name('finance.index');
Route::get('/finance/live-data', [FinanceDashboardController::class, 'liveData'])->name('finance.live');
// Stock Dashboard Routes
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/stocks', [StockDashboardController::class, 'index'])->name('stocks');
    Route::get('/stocks/live-data', [StockDashboardController::class, 'liveData'])->name('stocks.live');
});


Route::get('/stock', function () {
    $apiKey = env('TWELVE_API_KEY');
    $response = Http::get("https://api.twelvedata.com/quote?symbol=INTC&apikey=$apiKey");
    return $response->json();
});

Route::get('/test-fmp', function () {
    $apiKey = env('FMP_API_KEY');
    $response = Http::get("https://financialmodelingprep.com/api/v3/quote/AAPL?apikey=$apiKey");

    $data = $response->json();

    return $data; // direct JSON output
});

Route::get('/stocks', function () {

    $apiKey = env('FMP_API_KEY');
    $symbols = "AAPL,TSLA,MSFT,ICL,AMZN,GOOGL,NVDA,INTC,AMD";

    $response = Http::get("https://financialmodelingprep.com/api/v3/quote/$symbols?apikey=$apiKey");

    $stocks = $response->json();

    return $stocks;

   // return view('stock', compact('stocks'));
});

Route::get('/company/{symbol}', function ($symbol) {

    $apiKey = env('FMP_API_KEY');

    $response = Http::get("https://financialmodelingprep.com/api/v3/profile/$symbol?apikey=$apiKey");

    $company = $response->json()[0];

    return $company;

    //return view('company', compact('company'));
});

Route::get('/financials/{symbol}', function ($symbol) {

    $apiKey = env('FMP_API_KEY');

    $response = Http::get("https://financialmodelingprep.com/api/v3/income-statement/$symbol?limit=3&apikey=$apiKey");

    $financials = $response->json();

    return $financials;

    //return view('financials', compact('financials'));
});


Route::get('/test-price', function () {

    $apiKey = env('TWELVE_API_KEY');

    $response = Http::get("https://api.twelvedata.com/price", [
        'symbol' => 'AAPL',
        'apikey' => $apiKey
    ]);

    return $response->json();
});

Route::get('/compare', function () {

    $apiKey = env('TWELVE_API_KEY');

    $symbols = ['AAPL','TSLA','MSFT'];
    $data = [];

    foreach ($symbols as $symbol) {
        $res = Http::get("https://api.twelvedata.com/price", [
            'symbol' => $symbol,
            'apikey' => $apiKey
        ]);

        $data[$symbol] = $res->json();
    }

    return $data;
});

Route::get('/dashboard/{symbol}', function ($symbol) {

    $apiKey = env('TWELVE_API_KEY');

    $price = Http::get("https://api.twelvedata.com/price", [
        'symbol' => $symbol,
        'apikey' => $apiKey
    ])->json();

    $chart = Http::get("https://api.twelvedata.com/time_series", [
        'symbol' => $symbol,
        'interval' => '1day',
        'outputsize' => 10,
        'apikey' => $apiKey
    ])->json();

    return [
        'price' => $price,
        'chart' => $chart
    ];
});

Route::get('/ohlc/{symbol}', function ($symbol) {

    $apiKey = env('TWELVE_API_KEY');

    $response = Http::get("https://api.twelvedata.com/quote", [
        'symbol' => $symbol,
        'apikey' => $apiKey
    ]);

    return $response->json();
});

Route::get('/trend/{symbol}', function ($symbol) {

    $apiKey = env('TWELVE_API_KEY');

    $price = Http::get("https://api.twelvedata.com/price", [
        'symbol' => $symbol,
        'apikey' => $apiKey
    ])->json();

    $history = Http::get("https://api.twelvedata.com/time_series", [
        'symbol' => $symbol,
        'interval' => '1day',
        'outputsize' => 5,
        'apikey' => $apiKey
    ])->json();

    return [
        'current_price' => $price,
        'trend_data' => $history
    ];
});


// =====  start of new routes for stock screener =====

Route::get('/dashboard', function () {
    return view('dashboard');
});
