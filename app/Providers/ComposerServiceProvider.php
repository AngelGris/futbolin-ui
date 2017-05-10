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
                $navigation = [
                    ['url' => 'vestuario', 'icon' => 'fa fa-home', 'name' => 'Vestuario'],
                    ['url' => 'equipo', 'icon' => 'fa fa-star', 'name' => 'Equipo'],
                    ['url' => 'jugadores', 'icon' => 'fa fa-group', 'name' => 'Jugadores'],
                    ['url' => 'estrategia', 'icon' => 'fa fa-gears', 'name' => 'Estratégia'],
                    ['url' => 'equipos', 'icon' => 'fa fa-futbol-o', 'name' => 'Equipos'],
                ];

                $view->with('user', Auth::user())
                     ->with('team', Auth::user()->team)
                     ->with('navigation', $navigation);
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