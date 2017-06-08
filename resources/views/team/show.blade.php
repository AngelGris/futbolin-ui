@extends('layouts.inner')

@section('styles-inner')
<style>
#other-shield .shield-primary-color {
    fill: {{ $team['primary_color'] }};
}

#other-shield .shield-secondary-color {
    fill: {{ $team['secondary_color'] }};
}
</style>
@endsection

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
<div class="col-xs-12" style="margin-bottom:20px;text-align:center;">
    <img id="other-shield" class="svg" src="{{ $team['shieldFile'] }}" style="width:70px;" />
    <h2 style="text-align:center;width:auto;">{{ $team['name'] }}</h2>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">Nombre corto</label>
    <div class="col-xs-8">{{ $team['short_name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">Entrenador</label>
    <div class="col-xs-8">{{ $team['user']['name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">Estadio</label>
    <div class="col-xs-8">{{ $team['stadium_name'] }}</div>
</div>
<div class="clear"></div>
<div class="col-md-12">
@include('modules.formation')
</div>
<div class="col-md-6">
    <h2>Estadísticas</h2>
    @include('modules.statsmatches')
</div>
@include('modules.lastmatches')
<div class="clear"></div>
<div class="col-md-6">
    <h2>Contra {{ $_team['name'] }}</h2>
    @include('modules.statsmatches', ['matches' => $matches_versus, 'goals' => $goals_versus])
</div>
@include('modules.lastmatches', ['last_matches' => $last_matches_versus])
@endsection
