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
                'title'     => 'Shopping',
                'subtitle'  => 'Hora de hacer compras',
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
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        $shopping_item = ShoppingItem::find($request->input('id'));

        // If user doesn't have enough credit for the transaction
        // redirect to shopping with arning message
        if ($user->credits < $shopping_item->price) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type'      => 'credit_insufficient',
                    'message'   => 'Crédito insuficientes para la transacción'
                ], 400);
            } else {
                Session::flash('flash_danger', 'No tienes suficientes Fúlbos.');
                return redirect()->route('shopping');
            }
        }

        // Route to go after done
        $redirect = redirect()->route('shopping');

        // Complete transaction
        switch($shopping_item->id) {
            case 1:
                DB::table('players')->where('team_id', $user->team->id)->where('stamina', '>=', 80)->update(['stamina' => 100]);
                DB::table('players')->where('team_id', $user->team->id)->where('stamina', '<', 80)->increment('stamina', 20);
                $success_message = 'Tus jugadores han recuperado 20 puntos de energía.';
                break;
            case 2:
                DB::table('players')->where('team_id', $user->team->id)->update(['stamina' => 100]);
                $success_message = 'Tus jugadores han recuperado TODA su energía.';
                break;
            case 3:
                $user->team->train();
                $user->team->trainer = Carbon::now()->addWeeks(1);
                $user->team->save();
                $success_message = 'El entrenador ha sido contratado hasta el ' . $user->team->trainer->format('d/m/Y H:i:s') . '.';
                break;
            case 4:
            case 5:
                $player = Player::find($request->input('player_id'));
                if ($player->team->id != $user->team->id) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'type'      => 'player_owner',
                            'message'   => $player->short_name . ' no es de tu equipo'
                        ], 400);
                    } else {
                        Session::flash('flash_warning', $player->short_name . ' no es de tu equipo.');
                        return redirect()->route('player', $player->id);
                    }
                }

                if ($shopping_item->id == 4) {
                    $player->treat();
                    $success_message = $player->short_name . ' fue tratado por su lesión.';
                } else {
                    $sellable_count = $player->team->sellabel_count;
                    if ($sellable_count < 1) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'type'      => 'players_limit',
                                'message'   => 'Ha alcanzado el mínimo de jugadores en su equipo'
                            ], 400);
                        } else {
                            Session::flash('flash_warning', 'Ha alcanzado el mínimo de jugadores en su equipo.');
                            return redirect()->route('player', $player->id);
                        }
                    }
                    $player->setFree();
                    $success_message = $player->short_name . ' ha sido liberado.';
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
                    if ($request->expectsJson()) {
                        return response()->json([
                            'type'      => 'funds_limits',
                            'message'   => 'Has alcanzado el límite de ' . formatCurrency(\Config::get('constants.MAX_TEAM_FUNDS')) . ', no puedes tener más dinero.'
                        ], 400);
                    } else {
                        Session::flash('flash_warning', 'Has alcanzado el límite de ' . formatCurrency(\Config::get('constants.MAX_TEAM_FUNDS')) . ', no puedes tener más dinero.');
                        return redirect()->route('finances');
                    }
                }

                $user->team->moneyMovement($value, 'Venta de Fúlbos');
                $success_message = 'Has vendido ' . $shopping_item->price . ' Fúlbos por ' . formatCurrency($value);
                $redirect = redirect()->route('finances');
                break;
            default:
                Session::flash('flash_danger', 'Item inválido.');
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
            return response()->json([
                'credits'   => $user->credits
            ], 200);
        } else {
            Session::flash('flash_success', $success_message);
            return $redirect;
        }
    }
}
