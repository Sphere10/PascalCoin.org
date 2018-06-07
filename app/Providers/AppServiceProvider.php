<?php

namespace App\Providers;

use App\CoinMarketCap;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        \URL::forceScheme('https');

        if(php_sapi_name() !== 'cli') {
            $symbols = config('pascal.cmc.currencies', []);
            $tickers = CoinMarketCap::whereIn('symbol', $symbols)
                ->orderBy('created_at', 'DESC')
                ->take(count($symbols))
                ->get();

            foreach ($tickers as $ticker) {
                \View::share('cmc_' . $ticker->symbol, $ticker);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
