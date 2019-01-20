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
            $message = __('messages.cant_free_player_from_other_team');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if (!empty($player->selling->id)) {
            $message = __('messages.cant_free_transferable_player');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if ($player->freeValue > $user->team->funds) {
            $message = __('messages.not_enough_founds_to_free_player', ['player' => $player->short_name]);
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        $sellable_count = $player->team->sellabel_count;
        if ($sellable_count < 1) {
            $message = __('messages.minimum_players_reached');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_free',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        $user->team->moneyMovement(-$player->freeValue, \Config::get('constants.MONEY_MOVEMENTS_OUTCOME_CONTRACT_TERMINATED'), ['player' => $player->full_name]);
        $player = $player->setFree();
        if ($request->expectsJson()) {
            return response()->json([
                'player' => $player
            ], 200);
        } else {
            Session::flash('flash_success', __('messages.player_freed', ['player' => $player->short_name]));
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
                'title' => __('headers.player_title', ['player_name' => $player['first_name'] . ' ' . $player['last_name']]),
                'subtitle' => __('headers.player_subtitle'),
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
            $message = __('messages.invalid_offer_value');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        $player = Player::find($request->input('player_id'));
        if (!$player->selling) {
            $message = __('messages.player_not_transferable', ['player' => $player->short_name]);
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if ($request->input('offer') <= $player->selling->offer_value) {
            $message = __('messages.offer_must_be_greater', ['player' => $player->short_name, 'value' => formatCurrency($player->selling->offer_value)]);
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        if ($request->input('offer') > $user->team->calculateSpendingMargin()) {
            $message = __('messages.cant_offer_more_than', ['value' => formatCurrency($user->team->calculateSpendingMargin())]);
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        $selling = PlayerSelling::where('player_id', $player->id)->first();

        if ($selling->best_offer_team != $user->team->id && !$user->team->canHire) {
            $message = __('messages.maximum_players_reached');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_offer',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
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
            Session::flash('flash_success', __('messages.offer_made'));
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
                'title'     => __('headers.players_title'),
                'subtitle'  => __('headers.players_subtitle'),
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
            $message = __('messages.cant_make_transferable_other_team');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
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
                    Session::flash('flash_success', __('messages.transferable_until', ['player' => $player->shortName, 'end_date' => $selling->closes_at->format('d/m/Y H:i'), 'initial_value' => formatCurrency($selling->value)]));
                }
            } else {
                $message = __('messages.minimum_players_reached_cant_sell');
                if ($request->expectsJson()){
                    return response()->json([
                        'errors' => [
                            'type'      => 'player_sell',
                            'message'   => $message
                        ]
                    ], 400);
                } else {
                    Session::flash('flash_warning', $message);
                }
            }
        } else {
            $message = __('messages.already_transferable', ['player' => $player->short_name, 'end_date' => $selling->closes_at->format('d/m/Y H:i')]);
            if ($request->expectsJson()){
                return response()->json([
                    'errors' => [
                        'type'      => 'player_sell',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
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
            $message = __('messages.invalid_market_value');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        if ($player->team->id != $user->team->id) {
            $message = __('messages.cant_modify_value_other_team');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
                return redirect()->back();
            }
        }

        if (!empty($player->selling->id)) {
            $message = __('messages.cant_modify_value_transferable');
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'type'      => 'player_value',
                        'message'   => $message
                    ]
                ], 400);
            } else {
                Session::flash('flash_warning', $message);
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
            Session::flash('flash_success', __('messages.value_updated'));
            return redirect()->back();
        }
    }
}
