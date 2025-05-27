<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\DateHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Enregistrer le DateHelper comme singleton
        $this->app->singleton('DateHelper', function ($app) {
            return new DateHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Configuration de Carbon pour la locale française
        \Carbon\Carbon::setLocale('fr');
        
        // Définir les formats de date par défaut
        if (!defined('DEFAULT_DATE_FORMAT')) {
            define('DEFAULT_DATE_FORMAT', 'DD/MM/YYYY');
        }
        
        if (!defined('DEFAULT_DATETIME_FORMAT')) {
            define('DEFAULT_DATETIME_FORMAT', 'DD/MM/YYYY à HH:mm');
        }
        
        if (!defined('SHORT_DATE_FORMAT')) {
            define('SHORT_DATE_FORMAT', 'DD/MM/YY');
        }
    }
}