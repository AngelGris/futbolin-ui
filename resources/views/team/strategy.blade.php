@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    var strategies = {};
    @foreach ($strategies as $str)
    strategies[{{ $str['id'] }}] = {
        'name':'{{ $str['name'] }}',
        @for ($i = 1; $i <= 11; $i++)
        {{ $i }}:{'left':{{ $str[$i]['left'] }}, 'top':{{ $str[$i]['top'] }}},
        @endfor
    };
    @endforeach

    var players = {};
    @foreach ($players as $player)
    players[{{ $player['id'] }}] = {
        'name': '{{ $player['first_name'] . ' ' . $player['last_name'] }}',
        'position': '{{ $player['position'] }}',
        'average': {{ $player['average'] }},
        'goalkeeping': {{ $player['goalkeeping'] }},
        'defending': {{ $player['defending'] }},
        'dribbling': {{ $player['dribbling'] }},
        'heading': {{ $player['heading'] }},
        'jumping': {{ $player['jumping'] }},
        'passing': {{ $player['passing'] }},
        'precision': {{ $player['precision'] }},
        'speed': {{ $player['speed'] }},
        'strength': {{ $player['strength'] }},
        'tackling': {{ $player['tackling'] }},
    }
    @endforeach

    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false
    });

    $('.change-formation').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('href');
        $('#strategy-name').text(strategies[id]['name']);

        for (var i = 1; i <= 11; i++) {
            if (i < 10) {
                div = '#player-0' + i;
            } else {
                div = '#player-' + i;
            }

            $(div).animate({
                left: strategies[id][i]['left'] + '%',
                top: strategies[id][i]['top'] + '%'
            }, 500);
        };
    });

    $('.rollover-player').mouseover(function() {
        id = $(this).attr('id');
        $('#player-header').text(players[id]['name']);
        $('#player-info .widgetcontent').html('<div class="col-md-6"><strong>POS: ' + players[id]['position'] + '</strong><br>ARQ: ' + players[id]['goalkeeping'] + '<br>GAM: ' + players[id]['dribbling'] + '<br>SAL: ' + players[id]['jumping'] + '<br>PRE: ' + players[id]['precision'] + '<br>FUE: ' + players[id]['strength'] + '</div><div class="col-md-6"><strong>PRO: ' + players[id]['average'] + '</strong><br>DEF: ' + players[id]['defending'] + '<br>CAB: ' + players[id]['heading'] + '<br>PAS: ' + players[id]['passing'] + '<br>VEL: ' + players[id]['speed'] + '<br>QUI: ' + players[id]['tackling'] + '</div>');
        $('#player-info').stop().show();
    }).mouseleave(function() {
        $('#player-info').fadeOut(500);
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-md-5">
    <div class="widgetbox">
        <div class="headtitle">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn dropdown-toggle"><span id="strategy-name">{{ $strategies[$strategy]['name'] }}</span> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @foreach ($strategies as $str)
                    <li><a href="{{ $str['id'] }}" class="change-formation">{{ $str['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <h4 class="widgettitle">Formación</h4>
        </div>
        <div class="widgetcontent">
            <div id="strategy-container">
                <img src="{{ asset('img/field-large-3d.png') }}">
                <div class="strategy-players-container">
                    @for ($i = 1; $i <= 11; $i++)
                    <div id="player-{{ ($i < 10) ? '0' . $i : $i }}" class="player-container {{ $strategies[$strategy][$i]['pos'] }}" style="left:{{ $strategies[$strategy][$i]['left'] }}%;top:{{ $strategies[$strategy][$i]['top'] }}%;">{{ $i }}</div>
                    @endfor
                </div>
            </div>
            <h3 style="font-weight:bold;margin-top:20px;">Suplentes</h3>
            @for ($i = 12; $i <= 18; $i++)
            <div id="player-{{ $i }}" class="player-container">{{ $i }}</div>
            @endfor
        </div>
    </div>
</div>
<div class="col-md-4">
    <table id="dyntable" class="table table-bordered responsive">
        <thead>
            <tr>
                <th>#</th>
                <th style="width:50%">Nombre</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Promedio">PRO</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($players as $player)
            <tr id="{{ $player['id'] }}" class="rollover-player {{ strtolower($player['position']) }}">
                <td align="right">{{ $player['number'] }}</td>
                <td>{{ $player['short_name'] }}</td>
                <td align="center">{{ $player['position'] }}</td>
                <td align="right"><strong>{{ $player['average'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="col-md-3">
    <div id="player-info" class="widgetbox col-md-12" style="display:none;">
        <div class="headtitle">
            <h4 id="player-header" class="widgettitle"></h4>
        </div>
        <div class="widgetcontent"></div>
    </div>
</div>
@endsection
