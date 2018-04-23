<?php
namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\TournamentRound;
use App\AdminMessage;
use App\Notification;

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
                        ['url' => 'equipos', 'icon' => 'fa fa-shield', 'name' => 'Equipos'],
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
                        ['url' => 'equipo', 'icon' => 'fa fa-shield', 'name' => 'Equipo'],
                        ['url' => 'jugadores', 'icon' => 'fa fa-group', 'name' => 'Jugadores'],
                        ['url' => 'estrategia', 'icon' => 'fa fa-gears', 'name' => 'Estrategia'],
                        ['url' => 'amistosos', 'icon' => 'fa fa-handshake-o', 'name' => 'Amistosos'],
                        ['url' => 'torneos', 'icon' => 'fa fa-trophy', 'name' => 'Torneos'],
                        ['url' => 'shopping', 'icon' => 'fa fa-shopping-cart', 'name' => 'Shopping']
                    ];

                    $user = Auth::user();
                    $team = $user->team;
                    if ($team) {
                        $retiring = $team->players->where('retiring', '=', 1);
                        $last_match = TournamentRound::where('datetime', '<', time())->orderBy('datetime', 'DESC')->first();
                        $upgraded = [];
                        if ($last_match) {
                            $upgraded = $team->players->where('last_upgraded', '>', date('Y-m-d H:i:s', $last_match['datetime']))->sortByDesc('last_upgraded');
                        }

                        $request_time = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
                        $messages = AdminMessage::where('valid_from', '<', $request_time)->where('valid_to', '>', $request_time)->orderBy('valid_from')->get();
                        $suspensions = $team->players()->join('player_cards', 'player_cards.player_id', '=', 'players.id')->where('player_cards.suspension', '>', 0)->get();
                        $injuries = $team->players()->where('recovery', '>', 0)->get();

                        $view->with('_user', $user)
                            ->with('_team', $team)
                            ->with('_messagesCount', $user->unreadMessages)
                            ->with('_notifications', $user->notifications->take(5))
                            ->with('_messages', $messages)
                            ->with('_suspensions', $suspensions)
                            ->with('_injuries', $injuries)
                            ->with('_playersAlertsCount', count($suspensions) + count($injuries) + count($retiring) + count($upgraded))
                            ->with('_retiring', $retiring)
                            ->with('_navigation', $navigation)
                            ->with('_upgraded', $upgraded);
                    }
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