<?php

namespace App\Traits;

use App\Helpers\Qs;
use Illuminate\Database\Eloquent\Builder;

trait SchoolYearScope
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootSchoolYearScope()
    {
        static::addGlobalScope('school_year', function (Builder $builder) {
            // Récupérer l'année scolaire sélectionnée
            $selectedYear = app('selected_school_year') ?? Qs::getCurrentSession();
            
            // Filtrer les données par année scolaire
            $builder->where('year', $selectedYear);
        });
    }
    
    /**
     * Désactiver temporairement le scope d'année scolaire
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutSchoolYearScope()
    {
        return static::withoutGlobalScope('school_year');
    }
    
    /**
     * Récupérer les données pour toutes les années scolaires
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function allYears()
    {
        return static::withoutSchoolYearScope();
    }
}