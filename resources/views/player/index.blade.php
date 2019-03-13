@extends('layouts.inner')

@section('content-inner')
@if ($player->team && $player->treatable && $player->team->id == $_team->id)
<h3>{{ $player->number }} - {!! $player->name !!} <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-treatment">@lang('labels.treat_player')</button></h3>
@else
<h3>{{ $player->number }} - {!! $player->name !!}</h3>
@endif
<h4>@lang('positions.' . strtolower($player->position) . '_short') - @lang('labels.number_years', ['number' => $player->age])</h4>
<h4>
    @lang('labels.value'): {!! formatCurrency($player->value) !!}
@if ($player->team && $player->team->id == $_team->id && empty($player->selling->id))
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-value">@lang('labels.improve_contract')</button>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-selling">@lang('labels.declare_transferable')</button>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-free">@lang('labels.set_free')</button>
@elseif (!empty($player->selling->id))
    <br>
    @if ($player->selling->best_offer_team)
    @lang('labels.best_offer'): {{ formatCurrency($player->selling->best_offer_value) }} ({{ $player->selling->offeringTeam->name }})
    @else
    {{ strtoupper(trans('labels.no_offers')) }}
    @endif
    @if (!$player->team || $player->team->id != $_team->id)
    <button class="btn btn-primary btn-sm btn-offer" data-transfer="{{ $player->selling->id }}" data-id="{{ $player->id }}" data-name="{{ $player->full_name }}" data-value="{{ $player->selling->offer_value + 1 }}" data-offer="{{ (int)($player->selling->offer_value * 1.05) }}">@lang('labels.offer')</button>
    @endif
    <br>@lang('labels.transferable_until_date', ['date' => $player->selling->closes_at->format('d/m/Y H:i')])
@endif
</h4>
<div class="col-xs-12 col-sm-6 col-md-3 zebra">
    @foreach (['average', 'goalkeeping', 'defending', 'dribbling', 'heading', 'jumping', 'passing', 'precision', 'speed', 'strength', 'tackling'] as $attribute)
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">@lang('attributes.' . $attribute)</label>
        <div class="col-xs-3">
            @if ($player->upgraded && !empty($player->last_upgrade->{$attribute}))
            <span style="color:#009900;">{{ $player->{$attribute} }}<sub>+{{ $player->last_upgrade->{$attribute} }}</sub></span>
            @else
            {{ $player->{$attribute} }}
            @endif
        </div>
    </div>
    @endforeach
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">@lang('attributes.stamina')</label>
        <div class="col-xs-3">{{ $player->stamina }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">@lang('attributes.experience')</label>
        <div class="col-xs-3">{{ $player->experience }}</div>
    </div>
</div>
@if ($player->team && $player->team->id == $_team->id)
@if (empty($player->selling->id))
<div class="modal fade" id="modal-value" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('player.value', ['player' => $player->id]) }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Mejorar contrato de {{ $player->full_name }}</h4>
                </div>
                <div class="modal-body">
                    <p>Puedes incrementar el valor de venta de {{ $player->shortName }} hasta un valor de <strong>{!! formatCurrency($_team->calculateSpendingMargin($player->value, FALSE)) !!}</strong>.</p>
                    <p>Pero recuerda que el sueldo que deberás pagarle será el <strong>{{ \Config::get('constants.PLAYERS_SALARY') * 100 }}%</strong> de ése valor.</p>
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <label for="txtValue">Valor del jugador</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="value" id="txtValue" value="{{ $player->value }}" />
                        </div>
                        <div class="col-sm-5">
                            <label>Salario</label>
                        </div>
                        <div class="col-sm-7"><span id="player-salary"></span></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-selling" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('player.selling', ['player' => $player->id]) }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">@lang('labels.declare_player_transferable', ['player' => $player->full_name])</h4>
                </div>
                <div class="modal-body">
                    <p>@lang('messages.do_you_want_to_make_player_transferable_until_date', ['player' => $player->short_name, 'date' => \Carbon\Carbon::now()->addDays(config('constants.PLAYERS_TRANSFERABLE_PERIOD'))->format('d/m/Y')])</p>
                    <p>@lang('messages.make_transferable_selling_conditions', ['value' => formatCurrency($player->value)])</p>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('labels.accept')</button>
                    <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-free" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.free_player_action', ['player' => $player->full_name])</h4>
            </div>
            <div class="modal-body">
                <p>@lang('messages.how_to_terminate_contract', ['player' => $player->short_name, 'clause_value' => formatCurrency($player->freeValue)])</p>
                <p>@lang('messages.do_you_want_to_let_player_free', ['player' => $player->shortName])</p>
                <p>@lang('labels.funds_with_value', ['value' => formatCurrency($_team->funds)])</p>
                <p>@lang('labels.credits_with_value', ['value' => $_user->credits])</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('player.free', ['player' => $player->id]) }}" style="display: inline;">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('labels.pay_value', ['value' => formatCurrency($player->freeValue)])</button>
                </form>
                <form method="post" action="{{ route('shopping.buy') }}" style="display: inline;">
                    <input type="hidden" name="id" value="5">
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('labels.use_1_credit')</button>
                </form>
                <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
            </div>
        </div>
    </div>
</div>
@endif
@if ($player->team && $player->treatable && $player->team->id == $_team->id)
<div class="modal fade" id="modal-treatment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.treat_player')</h4>
            </div>
            @if($_user->credits > 0)
            <div class="modal-body">
                <p>@lang('messages.treatment_result', ['player' => $player->full_name, 'recovery' => $player->treatmentImprovement . ' ' . trans_choice('countables.rounds', $player->treatmentImprovement)])</p>
                <p>@lang('messages.each_player_can_be_treated_once')</p>
                <p>@lang('messages.do_you_want_to_treat_player', ['player' => $player->short_name])</p>
                <p>@lang('labels.credits_with_value', ['value' => $_user->credits])</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('shopping.buy') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="4">
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    <button id="buy-item" class="btn btn-primary">@lang('labels.treat')</button>
                    <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
                </form>
            </div>
            @else
            <div class="modal-body">
                <p>@lang('messages.not_enough_credits_for_treatment', ['player' => $player->short_name])</p>
                <p>@lang('messages.not_enough_credits_confirmation')</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-primary">@lang('labels.buy_credits')</a>
                <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endif
@if (!empty($player->selling->id) && (!$player->team || $player->team->id != $_team->id))
@include('modules.modals.marketoffer')
@endif
@endsection
