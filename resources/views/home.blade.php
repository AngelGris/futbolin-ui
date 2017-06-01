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
@if(isset($tournament))
<h3>{{ $tournament['category']['name'] }}</h3>
<div class="col-md-6 zebra" style="float:right;margin-bottom:40px;">
    <div class="col-xs-12" id="home-next-match">
        <div class="col-xs-12" id="home-next-match-date"><h3>{{ $tournament['next_match']['date'] }}</h3></div>
        <div class="col-xs-12" id="home-next-match-stadium"><h4>{{ $tournament['next_match']['local']['stadium_name'] }}</h4></div>
        <div class="col-xs-6 teams"><a href="{{ route('team.show', $tournament['next_match']['local']['id']) }}">
            <img id="shield-local" class="svg" src="{{ $tournament['next_match']['local']['shieldFile'] }}" style="height:70px;" /><br />{{ $tournament['next_match']['local']['name'] }}</a>
            <input type="hidden" id="local_primary_color" value="{{ $tournament['next_match']['local']['primary_color'] }}">
            <input type="hidden" id="local_secondary_color" value="{{ $tournament['next_match']['local']['secondary_color'] }}">
        </div>
        <div class="col-xs-6 teams">
            <a href="{{ route('team.show', $tournament['next_match']['visit']['id']) }}"><img id="shield-visit" class="svg" src="{{ $tournament['next_match']['visit']['shieldFile'] }}" style="height:70px;" /><br />{{ $tournament['next_match']['visit']['name'] }}</a>
            <input type="hidden" id="visit_primary_color" value="{{ $tournament['next_match']['visit']['primary_color'] }}">
            <input type="hidden" id="visit_secondary_color" value="{{ $tournament['next_match']['visit']['secondary_color'] }}">
        </div>
    </div>
    <div class="clear"></div>
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
    <div class="col-md-6 zebra">
        <div class="clear"></div>
        @for ($i = 0; $i < 10; $i++)
        <div class="col-xs-12" style="padding:0;">
            <div class="col-xs-3" style="text-align:right;">{{ $tournament['category']['positions'][$i]['position'] }}</div>
            <div class="col-xs-6"><a href="{{ route('team.show', $tournament['category']['positions'][$i]['team_id']) }}">{{ $tournament['category']['positions'][$i]['team']['short_name'] }}</a></div>
            <div class="col-xs-3" style="text-align:right;">{{ $tournament['category']['positions'][$i]['points'] }}</div>
        </div>
        @endfor
    </div>
    <div class="col-md-6 zebra">
        <div class="clear"></div>
        @for ($i = 10; $i < 20; $i++)
        <div class="col-xs-12" style="padding:0;">
            <div class="col-xs-3" style="text-align:right;">{{ $tournament['category']['positions'][$i]['position'] }}</div>
            <div class="col-xs-6"><a href="{{ route('team.show', $tournament['category']['positions'][$i]['team_id']) }}">{{ $tournament['category']['positions'][$i]['team']['short_name'] }}</a></div>
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
