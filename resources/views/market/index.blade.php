@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/market.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    updateSalary();

    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": []
    });

    $('#players-filtering').change(function() {
        if ($(this).val() != '') {
            document.location = '?pos=' + $(this).val();
        } else {
            document.location = '?pos=all';
        }
    });

    $('#modal-make-offer-input').keyup(function() {
        updateSalary();
    });

    $('.btn-offer').click(function() {
        $('#modal-make-offer-player-id').val($(this).data('id'));
        $('#modal-make-offer-player-name').text($(this).data('name'));
        $('#modal-make-offer-value').text($(this).data('value'));
        $('#modal-make-offer-input').val($(this).data('offer'));
        updateSalary();
        $('#modal-make-offer').modal('show');
    });
});

function updateSalary() {
    var value = parseInt($('#modal-make-offer-input').val() * {{ \Config::get('constants.PLAYERS_SALARY') }});
    $('#player-salary').html(value);
}
</script>
@endsection

@section('content-inner')
<div class="col-sm-4">
    Mostrar:
    <select id="players-filtering">
        <option value="">Todos</option>
        @foreach(['arq' => 'Arqueros', 'def' => 'Defensores', 'med' => 'Mediocampistas', 'ata' => 'Atacantes'] as $k => $v)
        @if ($k == $filter)
        <option value="{{ $k }}" selected>{{ $v }}</option>
        @else
        <option value="{{ $k }}">{{ $v }}</option>
        @endif
        @endforeach
    </select>
</div>
<div class="col-sm-4" style="text-align:center;">
    <a class="btn btn-sm btn-primary" href="{{ route('market.transactions') }}">Transacciones finalizadas</a>
</div>
<div class="col-sm-4" style="text-align:right;">
    Oferta máxima: {{ formatCurrency($_team->calculateSpendingMargin()) }}
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
                @if ($transferable->best_offer_value)
                {{ formatCurrency($transferable->best_offer_value) }} ({{ $transferable->offeringTeam->name }})
                @else
                SIN OFERTAS
                @endif
            </td>
            <td align="center">
                @if (!$transferable->player->team || $transferable->player->team->id != $_team->id)
                <button class="btn btn-primary btn-offer" data-id="{{ $transferable->player->id }}" data-name="{{ $transferable->player->first_name . ' ' . $transferable->player->last_name }}" data-value="{{ formatCurrency($transferable->offer_value) }}" data-offer="{{ (int)($transferable->offer_value * 1.05) }}">Ofertar</button>
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
<div class="modal fade" id="modal-make-offer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('player.offer') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Hacer oferta</h4>
                </div>
                <div class="modal-body">
                    <p>Hacer una oferta por <span id="modal-make-offer-player-name" style="font-weight: bold;"></span></p>
                    <p>La oferta tiene que ser superior a <span id="modal-make-offer-value"></span></p>
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <label for="modal-make-offer-input">Valor de la oferta</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="offer" id="modal-make-offer-input" value="" />
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
                    <input type="hidden" name="player_id" id="modal-make-offer-player-id" value="">
                    <button id="buy-item" class="btn btn-primary">Ofertar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
