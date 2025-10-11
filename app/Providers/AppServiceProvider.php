<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Mark;
use App\Observers\MarkObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the Mark observer
        Mark::observe(MarkObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        
        // Enregistrer le helper NumberToWords comme un alias
        $this->app->singleton('numberToWords', function () {
            return new \App\Helpers\NumberToWords();
        });
    }
}