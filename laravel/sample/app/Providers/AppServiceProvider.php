<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ページ切り替えると毎回呼ばれるようだ
        // SQL文のログを出力する(デバッグ用)
        if (config('app.env') !== 'production') {
            if (DB::listen(function ($query) {
                Log::info("Queary Time:{$query->time}s $query->sql");
            }));
        }
    }
}
