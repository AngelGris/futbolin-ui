@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/market.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
@parent
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": []
    });

    $('#toggle-filters').click(function() {
        $('#filters').slideToggle();
    });

    $('#sel-position').change(function() {
        if ($(this).val() == 'all') {
            $('#chk-position').prop('checked', false);
        } else {
            $('#chk-position').prop('checked', true);
        }
    });

    $('#sel-attribute').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#sel-attribute-from').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#sel-attribute-to').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#txt-value-from').keyup(function() {
        $('#chk-value').prop('checked', true);
    });

    $('#txt-value-to').keyup(function() {
        $('#chk-value').prop('checked', true);
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-sm-8">
    @if ($offers)
    <a class="btn btn-sm btn-primary" href="{{ route('market') }}">Volver al mercado</a>
    @else
    <button id="toggle-filters" class="btn btn-sm btn-primary">Filtros</button>
    @endif
    <a class="btn btn-sm btn-primary" href="{{ route('market.offers') }}">Mis ofertas</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.following') }}">Siguiendo</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.transactions') }}">Transacciones finalizadas</a>
</div>
<div class="col-sm-4" style="text-align:right;">
    Oferta máxima: {{ formatCurrency($_team->calculateSpendingMargin()) }}
</div>
<div class="col-sm-12" id="filters" style="text-align: center;{{ empty($filters) ? 'display: none;' : '' }}">
    <form>
        <div class="row">
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_position" id="chk-position"{{ !empty($filters['filter_position']) ? ' checked="checked"' : '' }} />Posición</label><br />
                    <select name="pos" id="sel-position">
                        <option value="all">Todos</option>
                        @foreach(['arq' => 'Arqueros', 'def' => 'Defensores', 'med' => 'Mediocampistas', 'ata' => 'Atacantes'] as $k => $v)
                        @if (!empty($filters['pos']) && $k == $filters['pos'])
                        <option value="{{ $k }}" selected>{{ $v }}</option>
                        @else
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_attribute" id="chk-attribute"{{ !empty($filters['filter_attribute']) ? ' checked="checked"' : '' }} />Atributo</label><br />
                    <select name="attr" id="sel-attribute">
                        @foreach(['average' => 'Media', 'goalkeeping' => 'Arquero', 'defending' => 'Defensa', 'dribbling' => 'Gambeta', 'heading' => 'Cabeceo', 'jumping' => 'Salto', 'passing' => 'Pase', 'precision' => 'Precisión', 'speed' => 'Velocidad', 'strength' => 'Fuerza', 'tackling' => 'Quite'] as $k => $v)
                        @if (!empty($filters['attr']) && $k == $filters['attr'])
                        <option value="{{ $k }}" selected>{{ $v }}</option>
                        @else
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endif
                        @endforeach
                    </select><br />
                    Entre
                    <select name="attr_from" id="sel-attribute-from">
                        @for($i = 0; $i <= 100; $i += 5)
                        @if (!empty($filters['attr_from']) && $i == $filters['attr_from'])
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                        @else
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endif
                        @endfor
                    </select>
                    <select name="attr_to" id="sel-attribute-to">
                        @for($i = 0; $i <= 100; $i += 5)
                        @if ((!empty($filters['attr_to']) && $i == $filters['attr_to']) || (empty($filters['attr_to']) && $i == 100))
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                        @else
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endif
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_value" id="chk-value"{{ !empty($filters['filter_value']) ? ' checked="checked"' : '' }} />Valor</label><br />
                    Mayor a <input type="text" name="value_from" id="txt-value-from" value="{{ !empty($filters['value_from']) ? $filters['value_from'] : 100000 }}" /><br />
                    Menor a <input type="text" name="value_to" id="txt-value-to" value="{{ !empty($filters['value_to']) ? $filters['value_to'] : $_team->calculateSpendingMargin() }}" />
                </div>
            </div>
        </div>
        <div class="row">
            <button class="btn btn-sm btn-primary">Filtrar</button>
        </div>
    </form>
</div>
<div class="col-sm-12">
    {{ $transferables->links() }}
</div>
<div class="clear"></div>
<table id="dyntable" class="table table-bordered table-transferables responsive">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Equipo</th>
            <th>Edad</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
            <th>Valor</th>
            <th>Fecha límite</th>
            <th>Mejor Oferta</th>
            <th>Ofertar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transferables as $transferable)
        <tr class="{{ strtolower($transferable->player->position) }}">
            <td><a href="{{ route('player', $transferable->player->id) }}">{!! $transferable->player->name  !!}</a></td>
            <td>
                @if ($transferable->player->team)
                {{ $transferable->player->team->name }}
                @else
                <i>Jugador libre</i>
                @endif
            </td>
            <td align="center">{{ $transferable->player->age }}</td>
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $transferable->player->position_long }}">{{ $transferable->player->position }}</span></td>
            <td align="right"><strong>{{ $transferable->player->average }}</strong></td>
            <td>{{ formatCurrency($transferable->value) }}</td>
            <td>{{ $transferable->closes_at->format('d/m/Y H:i') }}</td>
            <td>
                @if ($transferable->best_offer_team)
                {{ formatCurrency($transferable->best_offer_value) }} ({{ $transferable->offeringTeam->name }})
                @else
                SIN OFERTAS
                @endif
            </td>
            <td align="center">
                @if (!$transferable->player->team || $transferable->player->team->id != $_team->id)
                <button class="btn btn-primary btn-offer" data-transfer="{{ $transferable->id }}" data-id="{{ $transferable->player->id }}" data-name="{{ $transferable->player->first_name . ' ' . $transferable->player->last_name }}" data-value="{{ $transferable->offer_value + 1 }}" data-offer="{{ (int)($transferable->offer_value * 1.05) }}">Ofertar</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="col-sm-12">
    {{ $transferables->links() }}
</div>
@include('modules.playerslegends')
@include('modules.modals.marketoffer')
@endsection
