@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    updateSalary();

    $('#txtValue').keyup(function() {
        updateSalary();
    });
});

function updateSalary() {
    var value = parseInt($('#txtValue').val() * {{ \Config::get('constants.PLAYERS_SALARY') }});
    $('#player-salary').html(formatCurrency(value));
}
</script>
@endsection

@section('content-inner')
@if ($player->team && $player->treatable && $player->team->id == $_team->id)
<h3>{{ $player->number }} - {!! $player->name !!} <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-treatment">Tratar jugador</button></h3>
@else
<h3>{{ $player->number }} - {!! $player->name !!}</h3>
@endif
<h4>{{ $player->position }} - {{ $player->age }} años</h4>
<h4>
    Valor: {!! formatCurrency($player->value) !!}
@if ($player->team && $player->team->id == $_team->id && empty($player->selling->id))
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-value">Mejorar contrato</button>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-selling">Declarar transferible</button>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-free">Dejar libre</button>
@elseif (!empty($player->selling->id))
    <br>
    @if ($player->selling->best_offer_team)
    Mejor oferta: {{ formatCurrency($player->selling->best_offer_value) }} ({{ $player->selling->offeringTeam->name }})
    @else
    SIN OFERTAS
    @endif
    @if (!$player->team || $player->team->id != $_team->id)
    <button class="btn btn-primary btn-sm btn-offer" data-toggle="modal" data-target="#modal-make-offer">Ofertar</button>
    @endif
    <br>Transferible hasta el {{ $player->selling->closes_at->format('d/m/Y H:i') }}
@endif
</h4>
<div class="col-xs-12 col-sm-6 col-md-3 zebra">
    @foreach (['Media' => 'average', 'Arquero' => 'goalkeeping', 'Defensa' => 'defending', 'Gambeta' => 'dribbling', 'Cabeceo' => 'heading', 'Salto' => 'jumping', 'Pase' => 'passing', 'Precisión' => 'precision', 'Velocidad' => 'speed', 'Fuerza' => 'strength', 'Quite' => 'tackling'] as $k => $v)
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">{{ $k }}</label>
        <div class="col-xs-3">
            @if ($player->upgraded && !empty($player->last_upgrade->{$v}))
            <span style="color:#009900;">{{ $player->{$v} }}<sub>+{{ $player->last_upgrade->{$v} }}</sub></span>
            @else
            {{ $player->{$v} }}
            @endif
        </div>
    </div>
    @endforeach
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">Energía</label>
        <div class="col-xs-3">{{ $player->stamina }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">Experiencia</label>
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
                    <h4 class="modal-title">Mejorar contrato de {{ $player->first_name }} {{ $player->last_name }}</h4>
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
                    <h4 class="modal-title">Declarar a {{ $player->first_name }} {{ $player->last_name }} como transferible</h4>
                </div>
                <div class="modal-body">
                    <p>¿Quieres poner a <strong>{{ $player->shortName }}</strong> como transferible hasta el <strong>{{ \Carbon\Carbon::now()->addWeek()->format('d/m/Y') }}</strong>?</p>
                    <p>El jugador será vendido al mejor postor con un valor inicial de <strong>{!! formatCurrency($player->value) !!}</strong>.</p>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
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
                <h4 class="modal-title">Liberar a {{ $player->first_name }} {{ $player->last_name }}</h4>
            </div>
            <div class="modal-body">
                <p>Para rescindir el contrato de {{ $player->short_name }} debe pagar una cláusula de {{ formatCurrency($player->freeValue) }}, o puedes hacerlo utilizando 1 Fúlbo.</p>
                <p>¿Quieres liberar a <strong>{{ $player->shortName }}</strong>?</p>
                <p>Fondos: {{ formatCurrency($_team->funds) }}</p>
                <p>Fúlbos: {{ $_user->credits }}</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('player.free', ['player' => $player->id]) }}" style="display: inline;">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Pagar {{ formatCurrency($player->freeValue) }}</button>
                </form>
                <form method="post" action="{{ route('shopping.buy') }}" style="display: inline;">
                    <input type="hidden" name="id" value="5">
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Utilizar 1 Fúlbo</button>
                </form>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
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
                <h4 class="modal-title">Tratar jugador</h4>
            </div>
            @if($_user->credits > 0)
            <div class="modal-body">
                <p>Si tratas a <b>{{ $player->first_name }} {{ $player->last_name }}</b> ahora recuperará <b>{{ $player->treatmentImprovement }} {{ str_plural('fecha', $player->treatmentImprovement) }}</b></p>
                <p>Cada jugador puede ser tratado una sóla vez por cada lesión.</p>
                <p>¿Quieres tratar a {{ $player->short_name }} por 1 Fúlbo?</p>
                <p>Fúlbos: {{ $_user->credits }}</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('shopping.buy') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="4">
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    <button id="buy-item" class="btn btn-primary">Tratar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </form>
            </div>
            @else
            <div class="modal-body">
                <p>No tienes suficientes Fúlbos para tratar a {{ $player->shot_name }}.</p>
                <p>¿Quieres comprar más Fúlbos?</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-primary">Comprar Fúlbos</a>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endif
@if (!empty($player->selling->id) && (!$player->team || $player->team->id != $_team->id))
<div class="modal fade" id="modal-make-offer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('player.offer') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Hacer oferta</h4>
                </div>
                <div class="modal-body">
                    <p>Hacer una oferta por <strong>{{ $player->first_name }} {{ $player->last_name }}</strong></p>
                    <p>La oferta tiene que ser superior a {{ formatCurrency($player->selling->offer_value) }}</p>
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <label for="modal-make-offer-input">Valor de la oferta</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="offer" id="txtValue" value="{{ (int)($player->selling->offer_value * 1.05) }}" />
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
                    <input type="hidden" name="player_id" id="modal-make-offer-player-id" value="{{ $player->id }}">
                    <button id="buy-item" class="btn btn-primary">Ofertar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
