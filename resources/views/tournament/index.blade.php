@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    $('.pagination-slider-left').click(function(event) {
        event.preventDefault();

        var pos = $('.pagination-slider').position();
        $('.pagination-slider').animate({
            left : Math.min(0, pos.left + $('.pagination-slider-container').width()),
        })
    });

    $('.pagination-slider-right').click(function(event) {
        event.preventDefault();

        var pos = $('.pagination-slider').position();
        $('.pagination-slider').animate({
            left : Math.max($('.pagination-slider-container').width() - $('.pagination-slider').width(), pos.left - $('.pagination-slider-container').width()),
        })
    });

    $('.pagination-round').click(function(event) {
        event.preventDefault();

        $('.pagination-round').removeClass('active');
        $(this).addClass('active');

        $('.rounds').hide();
        $('#round-' + $(this).data('id')).show();
    });

    $('.load-match').click(function(event) {
        event.preventDefault();

        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('filename'), show_remaining : false, _token : '{{ csrf_token() }}'},
        }).done(function(data){
            refreshResultModal(data);
        });
    });
});
</script>
@endsection

@section('content-inner')
@if (empty($tournament))
<h3>No hay torneos para mostrar (todavía!!!)</h3>
@else
<h3 style="margin-bottom:30px;">{{ $tournament['name'] }} ({{ 'Zona ' . $category['zone_name'] . ' Cat. ' . $category['category_name'] }})</h3>
<div class="col-xs-12 col-md-6" style="float:right">
    <div class="col-md-12">
        <ul class="pagination" style="float:left;margin:0;">
            <li><a href="#" class="pagination-slider-left">«</a></li>
        </ul>
        <div class="pagination-slider-container">
            <ul class="pagination pagination-slider">
                @for ($i = 1; $i <= 38; $i++)
                <li class="pagination-round{{ $i == $last_round ? ' active' : '' }}" data-id="{{ $i }}"><a href="#">{{ $i }}</a></li>
                @endfor
            </ul>
        </div>
        <ul class="pagination" style="float:right;margin:0;">
            <li><a href="#" class="pagination-slider-right">»</a></li>
        </ul>
    </div>
    @foreach($category['rounds'] as $round)
    <div class="col-xs-12 rounds" id="round-{{ $round['number'] }}" {!! ($round['number'] != $last_round) ? ' style="display:none;"' : '' !!}>
        <h3>Fecha {{ $round['number'] }}</h3>
        <h4>{{ date('d/m/Y H:i', $round['datetime']) }}</h4>
        <table class="table table-bordered responsive">
            <thead>
                <tr>
                    <th colspan="2" width="50%">Local</th>
                    <th colspan="2" width="50%">Visitante</th>
                    @if ($round['datetime'] < $_SERVER['REQUEST_TIME'])
                    <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($round['matches'] as $match)
                <tr>
                    <td style="padding-right:5px;text-align:right;">{{ $match['local']['name'] }}</td>
                    <td style="text-align:center;">{{ $match['local_goals'] }}</td>
                    <td style="text-align:center;">{{ $match['visit_goals'] }}</td>
                    <td style="padding-left:5px;">{{ $match['visit']['name'] }}</td>
                    @if ($round['datetime'] < $_SERVER['REQUEST_TIME'])
                    <td style="text-align:center;">
                        @if ($match['match_id'])
                        <a href="#" class="load-match" data-filename="{{ $match['logfile'] }}"><span class="fa fa-search"></span></a>
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>
<div class="col-xs-12 col-md-6" style="float:left">
    <h3>Posiciones</h3>
    <table id="dyntable" class="table table-bordered responsive">
        <thead>
            <tr>
                <th>Pos</th>
                <th>Equipo</th>
                <th>PTS</th>
                <th>PJ</th>
                <th>PG</th>
                <th>PE</th>
                <th>PP</th>
                <th>GF</th>
                <th>GC</th>
                <th>DG</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($category['positions'] as $position)
            <tr>
                <td style="padding-right:5px;text-align:right;">{{ $position['position'] }}</td>
                <td>{{ $position['team']['name'] }}</td>
                <td style="text-align:right;">{{ $position['points'] }}</td>
                <td style="text-align:right;">{{ $position['played'] }}</td>
                <td style="text-align:right;">{{ $position['won'] }}</td>
                <td style="text-align:right;">{{ $position['tied'] }}</td>
                <td style="text-align:right;">{{ $position['lost'] }}</td>
                <td style="text-align:right;">{{ $position['goals_favor'] }}</td>
                <td style="text-align:right;">{{ $position['goals_against'] }}</td>
                <td style="text-align:right;">{{ $position['goals_difference'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="modal-match-result">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Resumen del partido</h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-match-result-content">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
