@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    @if (!empty($last_round))
    movePager({{ $last_round }});

    @endif
    $('.pagination-slider-left').click(function(event) {
        event.preventDefault();

        var pos = $('.pagination-slider').position();
        $('.pagination-slider').animate({
            left : Math.min(0, pos.left + $('.pagination-slider-container').width()),
        });
    });

    $('.pagination-slider-right').click(function(event) {
        event.preventDefault();

        var pos = $('.pagination-slider').position();
        $('.pagination-slider').animate({
            left : Math.max($('.pagination-slider-container').width() - $('.pagination-slider').width(), pos.left - $('.pagination-slider-container').width()),
        });
    });

    $('.pagination-round').click(function(event) {
        event.preventDefault();

        $('.pagination-round').removeClass('active');
        $(this).addClass('active');

        movePager($(this).data('id'));

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

function movePager(id) {
    var btn = $('#pag-' + id).position();
    var pos = $('.pagination-slider').position();

    console.log($('.pagination-slider-container').width() - $('.pagination-slider').width());
    console.log(($('.pagination-slider-container').width() / 2) - (id * 45) + 25);
    console.log(Math.min(0, Math.max($('.pagination-slider-container').width() - $('.pagination-slider').width(), ($('.pagination-slider-container').width() / 2) - (id * 45) + 25)));
    $('.pagination-slider').animate({
        left : Math.min(0, Math.max($('.pagination-slider-container').width() - $('.pagination-slider').width(), ($('.pagination-slider-container').width() / 2) - (id * 45) + 25)),
    })
}
</script>
@endsection

@section('content-inner')
@if (empty($tournament))
<h3>No hay torneos para mostrar (todavía!!!)</h3>
@else
<h3 style="margin-bottom:30px;">{{ $category['name'] }}</h3>
<div class="col-xs-12 col-md-6" style="float:right">
    <div class="col-md-12">
        <ul class="pagination" style="float:left;margin:0;">
            <li><a href="#" class="pagination-slider-left">«</a></li>
        </ul>
        <div class="pagination-slider-container">
            <ul class="pagination pagination-slider">
                @for ($i = 1; $i <= 38; $i++)
                <li class="pagination-round{{ $i == $last_round ? ' active' : '' }}" data-id="{{ $i }}"><a href="#" style="text-align:center;width:45px;">{{ $i }}</a></li>
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
                <tr{!! ($match['local']['id'] == $team['id'] || $match['visit']['id'] == $team['id']) ? ' style="background-color:#ddd;"' : '' !!}>
                    <td style="padding-right:5px;text-align:right;"><a href="{{ route('team.show', $match['local']['id']) }}">{{ $match['local']['name'] }}</a></td>
                    <td style="text-align:center;">{{ $match['local_goals'] }}</td>
                    <td style="text-align:center;">{{ $match['visit_goals'] }}</td>
                    <td style="padding-left:5px;"><a href="{{ route('team.show', $match['visit']['id']) }}">{{ $match['visit']['name'] }}</a></td>
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
            <tr{!! ($position['team']['id'] == $team['id']) ? ' style="background-color:#ddd;"' : '' !!}>
                <td style="padding-right:5px;text-align:right;">{{ $position['position'] }}</td>
                <td><a href="{{ route('team.show', $position['team']['id']) }}">{{ $position['team']['name'] }}</a></td>
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
