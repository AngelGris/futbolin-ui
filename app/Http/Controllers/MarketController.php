<?php

namespace App\Http\Controllers;

use App\Player;
use App\PlayerSelling;
use App\MarketTransaction;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    /**
     * Show market main page
     */
    public function index(Request $request)
    {
        if (empty($request->query('pos')) || $request->query('pos') == 'all') {
            $filter = '';
            $players = PlayerSelling::orderBy('updated_at', 'DESC')->paginate(30);
        } else {
            $filter = $request->query('pos');
            $players = PlayerSelling::join('players', 'players.id', 'player_sellings.player_id')->where('position', $filter)->orderBy('player_sellings.updated_at', 'DESC')->paginate(15);
        }

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Mercado de pases',
            'subtitle'      => 'A buscar refuerzos',
            'transferables' => $players,
            'filter'        => $filter
        ];

        return view('market.index', $vars);
    }

    /**
     * List all players in market
     */
    public function listing(Request $request)
    {
        $market = [];
        $players = PlayerSelling::orderBy('updated_at', 'DESC')->get();
        foreach ($players as $player) {
            $pla = Player::find($player->player_id);
            $market[] = [
                'player'    => [
                    'id'        => $pla->id,
                    'name'      => $pla->short_name,
                    'position'  => $pla->position,
                    'age'       => $pla->age,
                    'average'   => $pla->average,
                    'icons'     => $pla->icons,
                    'team_id'   => $pla->team_id,
                    'team'      => is_null($pla->team_id) ? '' : $pla->team->name
                ],
                'value'         => $player->value,
                'offer_value'   => $player->best_offer_value,
                'offer_team'    => $player->best_offer_team,
                'closes_at'     => $player->closes_at->timestamp,
            ];
        }

        return response()->json([
            'market'   => $market
        ], 200);
    }

    /*
     * Show previous transactions
     */
    public function transactions()
    {
        $transactions = MarketTransaction::OrderBy('created_at', 'DESC')->paginate(30);

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Transacciones finalizadas',
            'subtitle'      => 'Compras y ventas',
            'transactions'  => $transactions,
        ];

        return view('market.transactions', $vars);
    }
}
