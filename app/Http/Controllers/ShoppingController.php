<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Player;
use App\ShoppingItem;
use Carbon\Carbon;
use DB;
use Session;

class ShoppingController extends Controller
{
    /**
     * Show index page
     *
     * @return void
     */
    public function index()
    {
        $vars = [
            'icon'      => 'fa fa-shopping-cart',
            'title'     => 'Shopping',
            'subtitle'  => 'Hora de hacer compras',
            'items'     => ShoppingItem::where('in_shopping', TRUE)->get()
        ];

        return view('shopping.index', $vars);
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
        $this->validate($request, [
            'id'        => 'required|integer',
            'player_id' => 'sometimes|required|integer'
        ]);

        $user = Auth::user();
        $shopping_item = ShoppingItem::find($request->input('id'));

        // If user doesn't have enough credit for the transaction
        // redirect to shopping with arning message
        if ($user->credits < $shopping_item->price) {
            Session::flash('flash_danger', 'No tienes suficientes Fúlbos.');
            return redirect()->route('shopping');
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
                $user->team->trainer = Carbon::now()->addWeeks(1);
                $user->team->save();
                $user->team->train();
                $success_message = 'El entrenador ha sido contratado hasta el ' . $user->team->trainer->format('d/m/Y H:i:s') . '.';
                break;
            case 4:
                $player = Player::find($request->input('player_id'));
                $player->treat();
                $success_message = $player->short_name . ' fue tratado por su lesión.';
                $redirect = redirect()->route('player', $player->id);
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

        Session::flash('flash_success', $success_message);
        return $redirect;
    }
}
