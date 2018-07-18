<?php

namespace App\Http\Controllers;

use App\Player;
use App\PlayerSelling;
use App\MarketTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketController extends Controller
{
    /**
     * Show market main page
     */
    public function index(Request $request)
    {
        $filters = [];
        $players = PlayerSelling::join('players', 'players.id', 'player_sellings.player_id');

        // Position filter
        if (!empty($request->query('filter_position'))) {
            $filters['filter_position'] = True;
            $filters['pos'] = $request->query('pos');
            $players = $players->where('position', $filters['pos']);
        }

        // Attribute filter
        if (!empty($request->query('filter_attribute'))) {
            $filters['filter_attribute'] = True;
            $filters['attr'] = $request->query('attr');
            $filters['attr_from'] = min($request->query('attr_from'), $request->query('attr_to'));
            $filters['attr_to'] = max($request->query('attr_from'), $request->query('attr_to'));
            $players = $players->whereBetween($filters['attr'], [$filters['attr_from'], $filters['attr_to']]);
        }

        // Value filter
        if (!empty($request->query('filter_value'))) {
            $filters['filter_value'] = True;
            $filters['value_from'] = (int)$request->query('value_from');
            $filters['value_to'] = (int)$request->query('value_to');
            $players = $players->whereBetween('best_offer_value', [$filters['value_from'], $filters['value_to']]);
        }

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Mercado de pases',
            'subtitle'      => 'A buscar refuerzos',
            'transferables' => $players->orderBy('player_sellings.updated_at', 'DESC')->paginate(30),
            'filters'       => $filters,
            'offers'        => FALSE
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

    /**
     * Show user's current offers
     */
    public function offers()
    {
        $team = Auth::user()->team;
        $players = PlayerSelling::where('best_offer_team', $team->id)->orderBy('updated_at', 'DESC')->paginate(30);

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Mercado de pases',
            'subtitle'      => 'A buscar refuerzos',
            'transferables' => $players,
            'filter'        => '',
            'offers'        => TRUE
        ];

        return view('market.index', $vars);
    }

    /**
     * Show previous transactions
     */
    public function transactions(Request $request)
    {
        $transactions = MarketTransaction::OrderBy('created_at', 'DESC')->paginate(30);


        if ($request->expectsJson()) {
            return response()->json($transactions, 200);
        } else {
            $vars = [
                'icon'          => 'fa fa-retweet',
                'title'         => 'Transacciones finalizadas',
                'subtitle'      => 'Compras y ventas',
                'transactions'  => $transactions,
            ];

            return view('market.transactions', $vars);
        }
    }
}
