<?php

namespace App\Http\Controllers;

use App\Player;
use App\PlayerSelling;
use App\MarketTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class MarketController extends Controller
{
    /**
     * Start following a player in the market
     */
    public function follow(Request $request)
    {
        // Check id in $request
        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer',
        ]);

        $validator->validate();

        if (empty(Auth::guard('api')->user())) {
            Auth::user()->followPlayer($request->input('id'));
        } else {
            Auth::guard('api')->user()->user->followPlayer($request->input('id'));
        }

        return response()->json([
            'id' => $request->input('id')
        ], 200);
    }

    /**
     * Show followed players
     */
    public function following(Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        // Players following
        $following = [];
        foreach ($user->following as $follow) {
            $following[] = $follow->player_id;
        }

        $players = PlayerSelling::whereIn('player_id', $following)->orderBy('player_sellings.updated_at', 'DESC')->paginate(30);

        if ($request->expectsJson()) {
            foreach ($players as $player) {
                $pla = Player::find($player->player_id);
                $market[] = [
                    'id'            => $player->id,
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
                    'following'     => in_array($pla->id, $user->followingList),
                    'value'         => $player->value,
                    'offer_value'   => $player->best_offer_value,
                    'offer_team'    => $player->best_offer_team,
                    'closes_at'     => $player->closes_at->timestamp,
                ];
            }

            return response()->json([
                'market'   => $market
            ], 200);
        } else {
            $vars = [
                'icon'          => 'fa fa-retweet',
                'title'         => 'Mercado de pases',
                'subtitle'      => 'A buscar refuerzos',
                'transferables' => $players,
                'following'     => json_encode($following),
                'offers'        => TRUE
            ];

            return view('market.index', $vars);
        }
    }

    /**
     * Show market main page
     */
    public function index(Request $request)
    {
        $filters = [];
        $user = Auth::user();
        $players = PlayerSelling::select('player_sellings.*')->join('players', 'players.id', 'player_sellings.player_id');

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

        // Players following
        $follows = $user->following()->select('player_id')->get();
        $following = [];
        foreach ($follows as $follow) {
            $following[] = $follow->player_id;
        }

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Mercado de pases',
            'subtitle'      => 'A buscar refuerzos',
            'transferables' => $players->orderBy('player_sellings.updated_at', 'DESC')->paginate(30),
            'following'     => json_encode($following),
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
        $user = Auth::guard('api')->user()->user;

        $market = [];
        $players = PlayerSelling::select('player_sellings.*')->join('players', 'players.id', 'player_sellings.player_id');
        if (!empty($request->input('position')) && in_array($request->input('position'), ['ARQ', 'DEF', 'MED', 'ATA'])) {
            $players = $players->where('position', $request->input('position'));
        }
        if (!empty($request->input('attribute')) && in_array($request->input('attribute'), ['average', 'goalkeeping', 'defending', 'dribbling', 'heading', 'jumping', 'passing', 'precision', 'speed', 'strength', 'tackling'])) {
            $from = 0;
            $to = 100;
            if (!empty($request->input('attribute_from'))) {
                $from = (int)$request->input('attribute_from');
            }
            if (!empty($request->input('attribute_to'))) {
                $to = (int)$request->input('attribute_to');
            }
            $players = $players->whereBetween($request->input('attribute'), [min($from, $to), max($from, $to)]);
        }
        if (!empty($request->input('value_from')) || !empty($request->input('value_to'))) {
            $value_from = 0;
            $value_to = \Config::get('constants.MAX_PLAYER_VALUE');
            if (!empty($request->input('value_from'))) {
                $value_from = (int)$request->input('value_from');
            }
            if (!empty($request->input('value_to'))) {
                $value_to = (int)$request->input('value_to');
            }
            $players = $players->whereBetween('best_offer_value', [min($value_from, $value_to), max($value_from, $value_to)]);
        }
        $players = $players->orderBy('player_sellings.updated_at', 'DESC')->get();
        foreach ($players as $player) {
            $pla = Player::find($player->player_id);
            $market[] = [
                'id'            => $player->id,
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
                'following'     => in_array($pla->id, $user->followingList),
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
        $user = Auth::user();
        $players = PlayerSelling::where('best_offer_team', $user->team->id)->orderBy('updated_at', 'DESC')->paginate(30);

        $vars = [
            'icon'          => 'fa fa-retweet',
            'title'         => 'Mercado de pases',
            'subtitle'      => 'A buscar refuerzos',
            'transferables' => $players,
            'following'     => json_encode($user->followingList),
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

    /**
     * Stop following a player in the market
     */
    public function unfollow(Request $request)
    {
        // Check id in $request
        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer',
        ]);

        $validator->validate();

        if (empty(Auth::guard('api')->user())) {
            Auth::user()->unfollowPlayer($request->input('id'));
        } else {
            Auth::guard('api')->user()->user->unfollowPlayer($request->input('id'));
        }

        return response()->json([
            'id' => $request->input('id')
        ], 200);
    }
}
