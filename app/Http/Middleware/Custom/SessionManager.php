<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Helpers\Qs;

class SessionManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Utiliser le système existant des settings comme référence principale
        $currentSessionName = Qs::getCurrentSession();

        // Injecter la session dans le container
        app()->instance('selected_school_year', $currentSessionName);

        // Partager avec toutes les vues
        view()->share('current_session', $currentSessionName);

        return $next($request);
    }
}
