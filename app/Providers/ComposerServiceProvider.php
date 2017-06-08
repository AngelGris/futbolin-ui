<?php
namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function($view){
            if (Auth::check())
            {
                if (Auth::user()->isAdmin) {
                    $navigation = [
                        ['url' => '', 'icon' => 'fa fa-dashboard', 'name' => 'Panel'],
                        ['url' => 'usuarios', 'icon' => 'fa fa-group', 'name' => 'Usuarios'],
                        ['url' => 'equipos', 'icon' => 'fa fa-futbol-o', 'name' => 'Equipos'],
                        ['url' => 'torneos', 'icon' => 'fa fa-trophy', 'name' => 'Torneos'],
                        ['url' => 'partidos', 'icon' => 'fa fa-star', 'name' => 'Partidos'],
                    ];

                    $view->with('_domain', getDomain())
                        ->with('title', 'Administrador')
                        ->with('_navigation', $navigation);
                } else {
                    $navigation = [
                        ['url' => 'vestuario', 'icon' => 'fa fa-home', 'name' => 'Vestuario'],
                        ['url' => 'equipo', 'icon' => 'fa fa-star', 'name' => 'Equipo'],
                        ['url' => 'jugadores', 'icon' => 'fa fa-group', 'name' => 'Jugadores'],
                        ['url' => 'estrategia', 'icon' => 'fa fa-gears', 'name' => 'Estrategia'],
                        ['url' => 'amistosos', 'icon' => 'fa fa-handshake-o', 'name' => 'Amistosos'],
                        ['url' => 'torneos', 'icon' => 'fa fa-trophy', 'name' => 'Torneos'],
                    ];

                    $view->with('_user', Auth::user())
                         ->with('_team', Auth::user()->team)
                         ->with('_navigation', $navigation);
                }
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}