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
    public function free(Player $player)
    {
        $user = Auth::user();
        if ($player->team->id != $user->team->id) {
            Session::flash('flash_warning', 'No puedes liberar a un jugador de otro equipo.');
            return redirect()->back();
        }

        if ($player->freeValue > $user->team->funds) {
            Session::flash('flash_warning', 'Faltan fondos para liberar a ' . $player->short_name . '.');
            return redirect()->back();
        }

        $sellable_count = $player->team->sellabel_count;
        if ($sellable_count < 1) {
            Session::flash('flash_warning', 'Ha alcanzado el mínimo de jugadores en su equipo.');
            return redirect()->back();
        }

        $user->team->moneyMovement(-$player->freeValue, 'Rescisión de contrato de ' . $player->first_name . ' ' . $player->last_name);
        $player->setFree();
        Session::flash('flash_success', $player->short_name . ' ha sido liberado.');
        return redirect()->back();
    }

    /**
     * Show player info
     */
    public function index(Player $player, Request $request)
    {
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
            Session::flash('flash_warning', 'Valor de la oferta inválido.');
            return redirect()->back();
        }

        $player = Player::find($request->input('player_id'));
        if (!$player->selling) {
            Session::flash('flash_warning', $player->short_name . ' no es transferible.');
            return redirect()->back();
        }

        if ($request->input('offer') <= $player->selling->offer_value) {
            Session::flash('flash_warning', 'La oferta por ' . $player->short_name . ' tiene que ser superior a '  . formatCurrency($player->selling->offer_value) . '.');
            return redirect()->back();
        }

        $user = Auth::user();
        if ($request->input('offer') > $user->team->calculateSpendingMargin()) {
            Session::flash('flash_warning', 'No puedes hacer una oferta mayor a ' . formatCurrency($user->team->calculateSpendingMargin()) . '.');
            return redirect()->back();
        }

        if (!$user->team->canHire) {
            Session::flash('flash_warning', 'Ya alcansaste el máximo de jugadores en un equipo, no puedes comprar más jugadores.');
            return redirect()->back();
        }

        $selling = PlayerSelling::where('player_id', $player->id)->first();

        if ($selling->best_offer_team) {
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

        Session::flash('flash_success', 'Oferta realizada.');
        return redirect()->back();
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
    public function startSelling(Player $player)
    {
        $selling = $player->selling;
        if (is_null($selling)) {
            $sellable_count = $player->team->sellabel_count;
            if ($sellable_count > 0) {
                $selling = $player->startSelling();
                Session::flash('flash_success', $player->shortName . ' es transferible hasta el ' . $selling->closes_at->format('d/m/Y H:i') . ' con un valor inicial de ' . formatCurrency($selling->value));
            } else {
                Session::flash('flash_warning', 'Ha alcanzado el mínimo de jugadores en su equipo, no puede vender mas jugadores hasta no aumentar la plantilla');
            }
        } else {
            Session::flash('flash_warning', $player->shortName . ' ya es transferible hasta el ' . $selling->closes_at->format('d/m/Y H:i'));
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
            Session::flash('flash_warning', 'Nuevo valor de mercado inválido.');
            return redirect()->back();
        }

        if (!is_null($player->selling)) {
            Session::flash('flash_warning', 'No se puede modificar el valor de mercado de un jugador transferible');
            return redirect()->back();
        }

        $player->value = $request->input('value');
        $player->save();

        Session::flash('flash_success', 'Valor actualizado.');
        return redirect()->back();
    }
}
