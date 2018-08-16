<?php

namespace App\Http\Controllers;

use App\Notification;
use App\Player;
use App\PlayerSelling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;

class PlayerController extends Controller
{
    /**
     * Free player
     */
    public function free(Request $request, Player $player)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        if ($player->team->id != $user->team->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => 'No puedes liberar a un jugador de otro equipo.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No puedes liberar a un jugador de otro equipo.');
                return redirect()->back();
            }
        }

        if (!empty($player->selling->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => 'No se puede liberar a un jugador transferible.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No se puede liberar a un jugador transferible.');
                return redirect()->back();
            }
        }

        if ($player->freeValue > $user->team->funds) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => 'Faltan fondos para liberar a ' . $player->short_name . '.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'Faltan fondos para liberar a ' . $player->short_name . '.');
                return redirect()->back();
            }
        }

        $sellable_count = $player->team->sellabel_count;
        if ($sellable_count < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => 'Ha alcanzado el mínimo de jugadores en su equipo.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'Ha alcanzado el mínimo de jugadores en su equipo.');
                return redirect()->back();
            }
        }

        $user->team->moneyMovement(-$player->freeValue, 'Rescisión de contrato de ' . $player->first_name . ' ' . $player->last_name);
        $player = $player->setFree();
        if ($request->expectsJson()) {
            return response()->json([
                'player' => $player
            ], 200);
        } else {
            Session::flash('flash_success', $player->short_name . ' ha sido liberado.');
            return redirect()->back();
        }
    }

    /**
     * Show player info
     */
    public function index(Player $player, Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        // Players following
        $follows = $user->following()->select('player_id')->get();
        $following = [];
        foreach ($follows as $follow) {
            $following[] = $follow->player_id;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'player'    => $player
            ], 200);
        } else {
            $vars = [
                'icon' => 'fa fa-user',
                'title' => $player['first_name'] . ' ' . $player['last_name'],
                'subtitle' => 'Una parte del todo',
                'header_team' => $player['team'],
                'following'     => json_encode($following),
                'player' => $player
            ];

            return view('player.index', $vars);
        }
    }

    /**
     * Make a new offer on a transferable player
     */
    public function offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'required|integer',
            'offer'     => 'required|integer'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => 'Valor de la oferta inválido.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'Valor de la oferta inválido.');
                return redirect()->back();
            }
        }

        $player = Player::find($request->input('player_id'));
        if (!$player->selling) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $player->short_name . ' no es transferible.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $player->short_name . ' no es transferible.');
                return redirect()->back();
            }
        }

        if ($request->input('offer') <= $player->selling->offer_value) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => 'La oferta por ' . $player->short_name . ' tiene que ser superior a '  . formatCurrency($player->selling->offer_value) . '.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'La oferta por ' . $player->short_name . ' tiene que ser superior a '  . formatCurrency($player->selling->offer_value) . '.');
                return redirect()->back();
            }
        }

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        if ($request->input('offer') > $user->team->calculateSpendingMargin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => 'No puedes hacer una oferta mayor a ' . formatCurrency($user->team->calculateSpendingMargin()) . '.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No puedes hacer una oferta mayor a ' . formatCurrency($user->team->calculateSpendingMargin()) . '.');
                return redirect()->back();
            }
        }

        $selling = PlayerSelling::where('player_id', $player->id)->first();

        if ($selling->best_offer_team != $user->team->id && !$user->team->canHire) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => 'Ya alcanzaste el máximo de jugadores en un equipo, no puedes comprar más jugadores.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'Ya alcanzaste el máximo de jugadores en un equipo, no puedes comprar más jugadores.');
                return redirect()->back();
            }
        }

        if ($selling->best_offer_team && $selling->best_offer_team != $user->team->id) {
            // Notify team with previous offer
            Notification::create([
                'user_id' => $selling->offeringTeam->user->id,
                'title' => 'Tu oferta por ' . $selling->player->first_name . ' ' . $selling->player->last_name . ' ha sido superada.',
                'message' => $user->team->name . ' hizo una mejor oferta por <a href="/jugador/' . $selling->player->id . '/">' . $selling->player->first_name . ' ' . $selling->player->last_name . '</a>, ¿vas a dejar que se lo queden?',
            ]);
        }

        $selling->best_offer_value = $request->input('offer');
        $selling->best_offer_team = $user->team->id;
        $selling->save();

        $user->followPlayer($selling->id);

        if ($request->expectsJson()) {
            return response()->json([], 204);
        } else {
            Session::flash('flash_success', 'Oferta realizada.');
            return redirect()->back();
        }
    }

    /**
     * Show players list
     */
    public function showListing(Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
            return response()->json([
                'players'   => (is_null($user->team) ? null : $user->team->players)
            ], 200);
        } else {
            $vars = [
                'icon'      => 'fa fa-group',
                'title'     => 'Jugadores',
                'subtitle'  => 'Los engranajes de la máquina',
                'players'   => Auth::user()->team->players
            ];

            return view('player.listing', $vars);
        }
    }

    /**
     * Start selling a player
     */
    public function startSelling(Request $request, Player $player)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        $selling = $player->selling;
        if ($player->team->id != $user->team->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => 'No se puede declarar transferible a un jugador de otro equipo.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No se puede declarar transferible a un jugador de otro equipo.');
            }
        } elseif (empty($selling->id)) {
            $sellable_count = $player->team->sellabel_count;
            if ($sellable_count > 0) {
                $selling = $player->startSelling();
                if ($request->expectsJson()){
                    return response()->json([
                        'selling' => $selling
                    ], 200);
                } else {
                    Session::flash('flash_success', $player->shortName . ' es transferible hasta el ' . $selling->closes_at->format('d/m/Y H:i') . ' con un valor inicial de ' . formatCurrency($selling->value) . '.');
                }
            } else {
                if ($request->expectsJson()){
                    return response()->json([
                        'errors' => [
                            'type'      => 'player_sell',
                            'message'   => 'Ha alcanzado el mínimo de jugadores en su equipo, no puede vender mas jugadores hasta no aumentar la plantilla.'
                        ]
                    ], 400);
                } else {
                    Session::flash('flash_warning', 'Ha alcanzado el mínimo de jugadores en su equipo, no puede vender mas jugadores hasta no aumentar la plantilla.');
                }
            }
        } else {
            if ($request->expectsJson()){
                return response()->json([
                    'errors' => [
                        'type'      => 'player_sell',
                        'message'   => $player->shortName . ' ya es transferible hasta el ' . $selling->closes_at->format('d/m/Y H:i') . '.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $player->shortName . ' ya es transferible hasta el ' . $selling->closes_at->format('d/m/Y H:i') . '.');
            }
        }
        return redirect()->back();
    }

    /**
     * Update player's value
     */
    public function updateValue(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|integer|min:' . $player->value . '|max:' . $player->team->calculateSpendingMargin($player->value, FALSE)
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => 'Nuevo valor de mercado inválido.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'Nuevo valor de mercado inválido.');
                return redirect()->back();
            }
        }

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        if ($player->team->id != $user->team->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => 'No se puede modificar el valor de mercado de un jugador de otro equipo.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No se puede modificar el valor de mercado de un jugador de otro equipo.');
                return redirect()->back();
            }
        }

        if (!empty($player->selling->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => 'No se puede modificar el valor de mercado de un jugador transferible.'
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', 'No se puede modificar el valor de mercado de un jugador transferible.');
                return redirect()->back();
            }
        }

        $player->value = $request->input('value');
        $player->save();

        if ($request->expectsJson()) {
            return response()->json([
                'player' => $player
            ], 200);
        } else {
            Session::flash('flash_success', 'Valor actualizado.');
            return redirect()->back();
        }
    }
}
