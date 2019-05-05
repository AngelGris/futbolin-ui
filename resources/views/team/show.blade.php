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

    @if ($playable)
    $('.play').click(function(event) {
        event.preventDefault();

        $('#modal-playing-message').text('@lang('messages.playing_match')');
        $('#modal-playing').modal({
            'backdrop' : 'static',
            'keyboard' : false
        });

        $.ajax({
            'method' : 'POST',
            'url' : '{{ route('match.play') }}',
            'dataType' : 'json',
            'data' : {rival : $(this).data('id'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            $('#modal-playing-message').text('@lang('labels.loading_result')');
            loadResult(data.file);
        });
    });
    @endif
});

function loadResult(fileName) {
    $('#modal-match-loading').modal('show');

    $.ajax({
        'method' : 'GET',
        'url' : '{{ route('match.load') }}',
        'data' : {file : fileName, show_remaining : true, _token : '{{ csrf_token() }}'},
    }).done(function(data){
        $('#modal-match-loading').on('hidden.bs.modal', function () {
            refreshResultModal(data, true);
        }).modal('hide');
    });
}
</script>
@endsection

@section('content-inner')
<div class="col-xs-12" style="margin-bottom:20px;text-align:center;">
    <img id="other-shield" class="svg" src="{{ $team['shieldFile'] }}"  data-color-primary="{{ $team['primary_color'] }}" data-color-secondary="{{ $team['secondary_color'] }}" style="width:70px;" />
    <h2 style="text-align:center;width:auto;">{{ $team['name'] }}</h2>
    @if ($playable)
        @if (empty($next_friendly))
            <button class="btn btn-primary play" data-id="{{ $team->id }}">@lang('labels.play_friendly_match')</button>
        @else
            <button class="btn btn-default disabled">@lang('labels.play_friendly_match') ({{ $next_friendly }})</button>
        @endif
    @endif
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">@lang('labels.short_name')</label>
    <div class="col-xs-8">{{ $team['short_name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">@lang('labels.trainer')</label>
    <div class="col-xs-8">{{ $team['user']['name'] }}</div>
</div>
<div class="col-md-12" style="height:30px;">
    <label class="col-xs-4 control-label">@lang('labels.stadium')</label>
    <div class="col-xs-8">{{ $team['stadium_name'] }}</div>
</div>
<div class="clear"></div>
@if(!empty($team->trophies))
<div class="col-sm-12">
    <h2>@lang('labels.trophy_cabinet')</h2>
    @foreach($team->trophies as $trophy)
    <div class="col-xs-6 col-sm-3 col-md-2" style="position:relative;text-align:center;">
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
        <a href="{{ route('tournament', $trophy->category->id )}}">{!! $trophy->category->name_br !!}</a>
    </div>
    @endforeach
</div>
<div class="clear"></div>
@endif
<div class="col-md-12">
    <h2>@lang('labels.formation')</h2>
    @include('modules.formation')
</div>
<div class="col-md-6">
    <h2>@lang('labels.statistics')</h2>
    @include('modules.statsmatches')
</div>
@include('modules.lastmatches')
<div class="clear"></div>
@if ($_team->id != $team->id)
<div class="col-md-6">
    <h2>@lang('labels.against_team', ['team' => $_team->name])</h2>
    @include('modules.statsmatches', ['matches' => $matches_versus, 'goals' => $goals_versus])
</div>
@include('modules.lastmatches', ['last_matches' => $last_matches_versus])
@endif
@if ($playable)
@include('modules.modals.playfriendly')
@endif
@endsection
