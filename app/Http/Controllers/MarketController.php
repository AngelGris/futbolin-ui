<?php

namespace App\Http\Controllers;

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
