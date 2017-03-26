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
                    ['url' => 'vestuario', 'icon' => 'iconfa-home', 'name' => 'Vesturario'],
                    ['url' => 'equipo', 'icon' => 'iconfa-shield', 'name' => 'Equipo'],
                    ['url' => 'jugadores', 'icon' => 'iconfa-group', 'name' => 'Jugadores'],
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