@extends('layouts.inner')

@section('styles-inner')
<style>
#other-shield .shield-primary-color {
    fill: {{ $t['primary_color'] }};
}

#other-shield .shield-secondary-color {
    fill: {{ $t['secondary_color'] }};
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
    <img id="other-shield" class="svg" src="{{ $t['shieldFile'] }}" style="width:70px;" />
    <h2 style="text-align:center;width:auto;">{{ $t['name'] }}</h2>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-2 control-label">Nombre corto</label>
    <div class="col-xs-10">{{ $t['short_name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-2 control-label">Entrenador</label>
    <div class="col-xs-10">{{ $t['user']['name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-2 control-label">Nombre del estadio</label>
    <div class="col-xs-10">{{ $t['stadium_name'] }}</div>
</div>
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
    <h2>Contra {{ $team['name'] }}</h2>
    @include('modules.statsmatches', ['matches' => $matches_versus, 'goals' => $goals_versus])
</div>
@include('modules.lastmatches', ['last_matches' => $last_matches_versus])
@endsection
