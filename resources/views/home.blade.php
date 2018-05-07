@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    $('.load-match').click(function (event) {
        event.preventDefault();

        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('file'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            refreshResultModal(data);
        });
    });
});
</script>
@endsection

@section('content-inner')
@foreach($_team->injured_players as $player)
@if($player->treatable)
<div class="alert alert-warning" role="alert">{{ $player->short_name }} se ha lesionado y puede ser tratado para recuperarse más rápido. <div style="float:right;"><a class="btn btn-primary btn-xs" href="{{ route('player', $player->id) }}">Ver jugador</a></div></div>
@endif
@endforeach
@if(isset($tournament))
<h3>{{ $tournament['category']['name'] }} <a href="{{ route('tournaments') }}" class="btn btn-primary" style="margin-left:20px;padding: 2px 10px;">Ver todo</a></h3>
<div class="col-md-6 zebra" style="float:right;margin-bottom:40px;">
    @if (isset($tournament['next_match']))
    <div class="col-xs-12" id="home-next-match">
        <div class="col-xs-12" id="home-next-match-date"><h3>{{ $tournament['next_match']['date'] }}</h3></div>
        <div class="col-xs-12" id="home-next-match-stadium"><h4>{{ $tournament['next_match']['local']['stadium_name'] }}</h4></div>
        <div class="col-xs-6 teams"><a href="{{ route('team.show', $tournament['next_match']['local']['id']) }}">
            <img id="shield-local" class="svg" src="{{ $tournament['next_match']['local']['shieldFile'] }}" data-color-primary="{{ $tournament['next_match']['local']['primary_color'] }}" data-color-secondary="{{ $tournament['next_match']['local']['secondary_color'] }}" style="height:70px;" /><br />{{ $tournament['next_match']['local']['name'] }}</a>
        </div>
        <div class="col-xs-6 teams">
            <a href="{{ route('team.show', $tournament['next_match']['visit']['id']) }}"><img id="shield-visit" class="svg" src="{{ $tournament['next_match']['visit']['shieldFile'] }}" data-color-primary="{{ $tournament['next_match']['visit']['primary_color'] }}" data-color-secondary="{{ $tournament['next_match']['visit']['secondary_color'] }}" style="height:70px;" /><br />{{ $tournament['next_match']['visit']['name'] }}</a>
        </div>
    </div>
    <div class="clear"></div>
    @endif
    @if (!empty($tournament['last_matches']))
    <h4 style="margin-top:20px;">Últimos partidos</h4>
    @foreach ($tournament['last_matches'] as $match)
    <div class="col-xs-12">
        <div class="col-xs-2">{{ $match['date'] }}</div>
        <div class="col-xs-4" style="text-align:right;"><a href="{{ route('team.show', $match['local']['id']) }}">{{ $match['local']['short_name'] }}</a> {{ $match['local_goals'] }}</div>
        <div class="col-xs-4"><a href="{{ route('team.show', $match['visit']['id']) }}">{{ $match['visit']['short_name'] }}</a> {{ $match['visit_goals'] }}</div>
        <div class="col-xs-2" style="text-align:right;"><a href="#" class="load-match" data-file="{{ $match['logfile'] }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    @endif
</div>
<div class="col-md-6" style="float:left;margin-bottom:40px;">
    <h3 style="margin-bottom:10px;text-align:center">Posiciones</h3>
    <div class="col-md-12 zebra">
        <div class="col-xs-12">
            <div class="col-xs-2" style="font-weight:bold;text-align:center;">Pos</div>
            <div class="col-xs-7" style="font-weight:bold;text-align:center;">Equipo</div>
            <div class="col-xs-3" style="font-weight:bold;text-align:right;">Pts</div>
        </div>
        @for ($i = 0; $i < 10; $i++)
        <div class="col-xs-12" style="padding:0;{{ ($_team['id'] == $tournament['category']['positions'][$i]['team_id']) ? 'background-color:#f99;' : ''}}">
            <div class="col-xs-2" style="text-align:right;">{!! $tournament['category']['positions'][$i]['position_full'] !!}</div>
            <div class="col-xs-7"><a href="{{ route('team.show', $tournament['category']['positions'][$i]['team_id']) }}">{{ $tournament['category']['positions'][$i]['team']['name'] }}</a></div>
            <div class="col-xs-3" style="text-align:right;">{{ $tournament['category']['positions'][$i]['points'] }}</div>
        </div>
        @endfor
    </div>
</div>
<div class="clear"></div>
@endif
@include('modules.formation')
@include('modules.lastmatches')
@endsection
