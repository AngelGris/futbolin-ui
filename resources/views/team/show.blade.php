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
    <img id="other-shield" class="svg" src="{{ $team['shieldFile'] }}"  data-color-primary="{{ $team['primary_color'] }}" data-color-secondary="{{ $team['secondary_color'] }}" style="width:70px;" />
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
@if(!empty($team->trophies))
<div class="col-md-6">
    <h2>Vitrina de trofeos</h2>
    @foreach($team->trophies as $trophy)
    <div class="col-md-3" style="position:relative;text-align:center;">
        @if($trophy->position <= 3)
        <p>
            @if($trophy->position == 1)
            <span class="fa fa-trophy" style="color:#ffd700;font-size:70px;"></span>
            @elseif($trophy->position == 2)
            <span class="fa fa-trophy" style="color:#c0c0c0;font-size:70px;"></span>
            @else
            <span class="fa fa-trophy" style="color:#cd7f32;font-size:70px;"></span>
            @endif
        </p>
        <div style="font-size:25px;left:0;position:absolute;top:15px;width:100%;">{{ $trophy->position }}</div>
        @else
        <div style="font-size:25px;height:70px;left:0;line-height:70px;top:15px;width:100%;">{{ $trophy->position }}°</div>
        @endif
        <a href="{{ route('tournament', $trophy->category->id )}}">{{ $trophy->category->name }}</a>
    </div>
    @endforeach
</div>
@endif
<div class="col-md-12">
    <h2>Formación</h2>
    @include('modules.formation')
</div>
<div class="col-md-6">
    <h2>Estadísticas</h2>
    @include('modules.statsmatches')
</div>
@include('modules.lastmatches')
<div class="clear"></div>
@if ($_team->id != $team->id)
<div class="col-md-6">
    <h2>Contra {{ $_team['name'] }}</h2>
    @include('modules.statsmatches', ['matches' => $matches_versus, 'goals' => $goals_versus])
</div>
@include('modules.lastmatches', ['last_matches' => $last_matches_versus])
@endif
@endsection
