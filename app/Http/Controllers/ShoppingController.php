<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Player;
use App\ShoppingItem;
use Carbon\Carbon;
use DB;
use Session;
use Validator;

class ShoppingController extends Controller
{
    /**
     * Show index page
     *
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => ShoppingItem::where('in_shopping', TRUE)->get()
            ], 200);
        } else {
            $vars = [
                'icon'      => 'fa fa-shopping-cart',
                'title'     => __('headers.shopping_title'),
                'subtitle'  => __('headers.shopping_subtitle'),
                'items'     => ShoppingItem::where('in_shopping', TRUE)->get()
            ];

            return view('shopping.index', $vars);
        }
    }

    /**
     * Buy an item
     * @param Request $request
     *
     * @return boolean
     */
    public function buy(Request $request)
    {
        // Check id in $request
        $validator = Validator::make($request->all(), [
            'id'        => 'required|integer',
        ]);

        // If $input->id is 4 or 5 then player_id must be present
        $validator->sometimes('player_id', 'required|integer|exists:players,id', function($input) {
            return in_array($input->id, [4, 5]);
        });

        // If $input->id == 6 then credits must be present
        $validator->sometimes('credits', 'required|integer|min:1', function($input) {
            return $input->id == 6;
        });

        $validator->validate();

        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $user = Auth::user();
            } else {
                $user = Auth::guard('api')->user()->user;
            }
        } else {
            $user = Auth::user();
        }

        $shopping_item = ShoppingItem::find($request->input('id'));

        // If user doesn't have enough credit for the transaction
        // redirect to shopping with arning message
        if ($user->credits < $shopping_item->price) {
            $message = __('messages.not_enough_credits');
            if ($request->expectsJson()) {
                return response()->json([
                    'type'      => 'credit_insufficient',
                    'message'   => $message
                ], 400);
            } else {
                Session::flash('flash_danger', $message);
                return redirect()->route('shopping');
            }
        }

        // Route to go after done
        $redirect = redirect()->route('shopping');

        $ajax_response = [];

        // Complete transaction
        switch($shopping_item->id) {
            case 1:
                DB::table('players')->where('team_id', $user->team->id)->where('stamina', '>=', 80)->update(['stamina' => 100]);
                DB::table('players')->where('team_id', $user->team->id)->where('stamina', '<', 80)->increment('stamina', 20);
                $success_message = __('messages.recovered_20_stamina');
                break;
            case 2:
                DB::table('players')->where('team_id', $user->team->id)->update(['stamina' => 100]);
                $success_message = __('messages.recovered_all_stamina');
                break;
            case 3:
                $user->team->train();
                $user->team->trainer = Carbon::now()->addWeeks(1);
                $user->team->save();
                $success_message = __('messages.personal_trainer_hired', ['hired_until' => $user->team->trainer->format('d/m/Y H:i:s')]);
                break;
            case 4:
            case 5:
                $player = Player::find($request->input('player_id'));
                if ($player->team->id != $user->team->id) {
                    $message = __('messages.player_not_yours', ['player' => $player->short_name]);
                    if ($request->expectsJson()) {
                        return response()->json([
                            'type'      => 'player_owner',
                            'message'   => $message
                        ], 400);
                    } else {
                        Session::flash('flash_warning', $message);
                        return redirect()->route('player', $player->id);
                    }
                }

                if ($shopping_item->id == 4) {
                    $player->treat();
                    $success_message = __('messages.player_treated', ['player' => $player->short_name]);
                } else {
                    $sellable_count = $player->team->sellabel_count;
                    if ($sellable_count < 1) {
                        $message = __('messages.minimum_players_reached');
                        if ($request->expectsJson()) {
                            return response()->json([
                                'type'      => 'players_limit',
                                'message'   => $message
                            ], 400);
                        } else {
                            Session::flash('flash_warning', $message);
                            return redirect()->route('player', $player->id);
                        }
                    }
                    $player->setFree();
                    $success_message = __('messages.player_freed', ['player' => $player->short_name]);
                }

                $redirect = redirect()->route('player', $player->id);
                break;
            case 6:
                if ($request->input('credits') * \Config::get('constants.CREDITS_SELL_VALUE') <= \Config::get('constants.MAX_TEAM_FUNDS') - $user->team->funds) {
                    $shopping_item->price = $request->input('credits');
                    $value = $request->input('credits') * \Config::get('constants.CREDITS_SELL_VALUE');
                } else {
                    $value = \Config::get('constants.MAX_TEAM_FUNDS') - $user->team->funds;
                    $shopping_item->price = (int)ceil($value / \Config::get('constants.CREDITS_SELL_VALUE'));
                }

                if ($shopping_item->price == 0) {
                    $message = __('messages.maximum_funds_reached', ['maximum_funds' => formatCurrency(\Config::get('constants.MAX_TEAM_FUNDS'))]);
                    if ($request->expectsJson()) {
                        return response()->json([
                            'type'      => 'funds_limits',
                            'message'   => $message
                        ], 400);
                    } else {
                        Session::flash('flash_warning', $message);
                        return redirect()->route('finances');
                    }
                }

                $user->team->moneyMovement($value, \Config::get('constants.MONEY_MOVEMENTS_INCOME_SELLING_CREDITS'));
                $success_message = __('messages.credits_sold', ['credits' => $shopping_item->price, 'value' => formatCurrency($value)]);
                $redirect = redirect()->route('finances');
                break;
            case 7:
                $trainning_points = $user->team->keepTrainning();
                $ajax_response = [
                    'trained'       => TRUE,
                    'count'         => $user->team->trainning_count,
                    'points'        => $trainning_points,
                    'next'          => $user->team->trainable_remaining,
                    'show_options'  => FALSE
                ];
                break;
            default:
                Session::flash('flash_danger', __('messages.invalid_item'));
                return redirect()->route('shopping');
                break;
        }

        $user->credits -= $shopping_item->price;
        $user->save();

        $user->transactions()->create([
            'shopping_item_id'  => $shopping_item->id,
            'credits'           => $shopping_item->price
        ]);

        if ($request->expectsJson()) {
            return response()->json($ajax_response + [
                'credits'   => $user->credits
            ], 200);
        } else {
            Session::flash('flash_success', $success_message);
            return $redirect;
        }
    }
}
