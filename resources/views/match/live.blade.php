@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/livematch.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css" />
@endsection

@section('javascript')
<script src="{{ asset('js/jquery.jplayer.min.js') }}"></script>
<script src="{{ asset('js/livematch.js') }}"></script>
<script type="text/javascript">
var match_log = '{{ $match->logfile }}';
var match_others = {!! $match_others !!};
var match_duration = {{ config('constants.LIVE_MATCH_DURATION') * 60 }};
var positions = {!! $positions !!};
var rivals = { {{ $match->local->id }} : {{ $match->visit->id }}, {{ $match->visit->id }} : {{ $match->local->id }} };
var text_half_time = '@lang('labels.half_time')';
var text_full_time = '@lang('labels.full_time')';
</script>
@endsection

@section('content')
<div id="jplayer-final-whistle"></div>
<div id="jplayer-goal"></div>
<div id="jplayer-whistle"></div>
<div class="col-sm-8 main-window">
    <div class="col-xs-12">
        <h1>
            {{ $match->category->name }}<br>
            @lang('labels.round_number', ['number' => $match->round->number])
        </h1>
        <h4 class="assistance">@lang('labels.number_spectators', ['number' => number_format($match->assistance)]) - @lang('labels.collection_value', ['value' => formatCurrency($match->incomes)])</h4>
    </div>
    <div class="col-xs-4 col-sm-5 title-local">
        <span class="title-local-name">{{ $match->local->name }}</span>
        <span class="title-local-shortname">{{ $match->local->short_name }}</span>
        <img class="svg" id="shield-local-res" src="{{ $match->local->shield_file }}"  data-color-primary="{{ $match->local->primary_color }}" data-color-secondary="{{ $match->local->secondary_color }}" style="height:50px;">
    </div>
    <div class="col-xs-4 col-sm-2 title-goals"><span class="local-goals">0</span> : <span class="visit-goals">0</span></div>
    <div class="col-xs-4 col-sm-5 title-visit">
        <img class="svg" id="shield-visit-res" src="{{ $match->visit->shield_file }}"  data-color-primary="{{ $match->visit->primary_color }}" data-color-secondary="{{ $match->visit->secondary_color }}" style="height:50px;">
        <span class="title-visit-name">{{ $match->visit->name }}</span>
        <span class="title-visit-shortname">{{ $match->visit->short_name }}</span>
    </div>
    <div class="col-xs-12 time" id="match-timer">00:00</div>
    <div class="col-xs-12 timeline-wrapper">
        <div id="timeline-marker">
            <span class="fa fa-caret-down"></span>
            <span class="fa fa-caret-up"></span>
        </div>
        <div id="timeline-markers">
        </div>
        <div id="timeline">
            <div id="timeline-progress"></div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="broadcast-wrapper" id="broadcast-wrapper">
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="stats-wrapper">
            <div class="col-xs-12">
                <div class="col-xs-4 stats-team">{{ $match->local->short_name }}</div>
                <div class="col-xs-4"></div>
                <div class="col-xs-4 stats-team">{{ $match->visit->short_name }}</div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4 local-goals goals" id="goals-{{ $match->local->id }}">0</div>
                <div class="col-xs-4">@lang('labels.goals')</div>
                <div class="col-xs-4 visit-goals goals" id="goals-{{ $match->visit->id }}">0</div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4"><span class="local-shots">0</span> (<span class="local-shots-on-goal">0</span>)</div>
                <div class="col-xs-4">@lang('labels.shots')</div>
                <div class="col-xs-4"><span class="visit-shots">0</span> (<span class="visit-shots-on-goal">0</span>)</div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4"><span class="local-fouls">0</span></div>
                <div class="col-xs-4">@lang('labels.fouls_made')</div>
                <div class="col-xs-4"><span class="visit-fouls">0</span></div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4"><span class="local-yellow-cards">0</span> / <span class="local-red-cards">0</span></div>
                <div class="col-xs-4">@lang('labels.cards')</div>
                <div class="col-xs-4"><span class="visit-yellow-cards">0</span> / <span class="visit-red-cards">0</span></div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4"><span class="local-substitutions">0</span></div>
                <div class="col-xs-4">@lang('labels.substitutions')</div>
                <div class="col-xs-4"><span class="visit-substitutions">0</span></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="col-sm-0 col-md-2"></div>
    <div class="col-sm-12 col-md-8">
        <h2>@lang('labels.line_ups')</h2>
        <div class="col-xs-12 modal-match-result-formations">
            <div class="col-sm-3 col-xs-6">
                <ul id="formation-local">
                </ul>
            </div>
            <div class="col-sm-3 col-xs-6">
                <ul id="formation-visit">
                </ul>
            </div>
            <div class="col-sm-6 col-xs-12" id="formation-field">
                <img src="{{ asset('img/field-large.png') }}">
            </div>
        </div>
    </div>
</div>
<div class="col-sm-4 match-other-wrapper">
    <div id="match-other-tab">TAB</div>
    <div id="match-other-scroll">
        <div id="match-other-wrapper">
        </div>
        <div id="live-positions" class="zebra">
            <div class="col-xs-12">
                <div class="col-xs-3 col-sm-2" style="font-weight:bold;text-align:center;">@lang('labels.position_short')</div>
                <div class="col-xs-5 col-sm-6" style="font-weight:bold;text-align:center;">@lang('labels.team')</div>
                <div class="col-xs-2" style="font-weight:bold;text-align:right;">{{ strtoupper(trans('labels.points_short')) }}</div>
                <div class="col-xs-2" style="font-weight:bold;text-align:right;">@lang('labels.goals_difference_short')</div>
                <div class="clear"></div>
            </div>
            <div id="live-positions-teams"></div>
        </div>
    </div>
</div>
<div id="live-loading">
    <img src="{{ asset('img/connecting.gif') }}" style="width:60px;"><br>
    @lang('labels.starting_transmission')
</div>
@endsection