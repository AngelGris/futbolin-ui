<?php
namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\TournamentRound;
use App\AdminMessage;

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
                        ['url' => 'mensajes', 'icon' => 'fa fa-envelope', 'name' => 'Mensajes'],
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

                    $team = Auth::user()->team;
                    $retiring = $team->players->where('retiring', '=', 1);
                    $last_match = TournamentRound::where('datetime', '<', time())->orderBy('datetime', 'DESC')->first();
                    $upgraded = [];
                    if ($last_match) {
                        $upgraded = $team->players->where('updated_at', '>', date('Y-m-d H:i:s', $last_match['datetime']));
                    }

                    $messages = AdminMessage::where('valid_to', '>', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']))->orderBy('valid_from')->get();

                    $view->with('_user', Auth::user())
                         ->with('_team', $team)
                         ->with('_messagesCount', count($messages))
                         ->with('_messages', $messages)
                         ->with('_playersAlertsCount', count($retiring) + count($upgraded))
                         ->with('_retiring', $retiring)
                         ->with('_navigation', $navigation)
                         ->with('_upgraded', $upgraded);
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