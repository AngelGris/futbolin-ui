@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/userguide.js') }}"></script>
@endsection

@section('content-inner')
<div class="accordion accordion-primary">
    <h3><span class="fa fa-home" style="margin-right:10px;"></span><a href="#">@lang('labels.locker_room')</a></h3>
    <div>
        @lang('users_guide.locker_room')
    </div>
    <h3><span class="fa fa-shield" style="margin-right:10px;"></span><a href="#">@lang('labels.team')</a></h3>
    <div>
        @lang('users_guide.team')
    </div>
    <h3><span class="fa fa-money" style="margin-right:10px;"></span><a href="#">@lang('labels.finances')</a></h3>
    <div>
        @lang('users_guide.finances', ['salary_ratio' => number_format(config('constants.PLAYERS_SALARY') * 100, 2)])
    </div>
    <h3><span class="fa fa-group" style="margin-right:10px;"></span><a href="#">@lang('labels.players')</a></h3>
    <div>
        @lang('users_guide.players', ['max_players' => config('constants.MAX_PLAYERS_REPLACE')])
        @include('modules.playerslegends')
    </div>
    <h3><span class="fa fa-retweet" style="margin-right:10px;"></span><a href="#">@lang('labels.transfers_market')</a></h3>
    <div>
        @lang('users_guide.transfers_market', ['transferable_period' => config('constants.PLAYERS_TRANSFERABLE_PERIOD'), 'salary_rate' => number_format(config('constants.PLAYERS_SALARY') * 100, 2)])
    </div>
    <h3><span class="fa fa-gears" style="margin-right:10px;"></span><a href="#">@lang('labels.strategy')</a></h3>
    <div>
        @lang('users_guide.strategy')
    </div>
    <h3><span class="fa fa-handshake-o" style="margin-right:10px;"></span><a href="#">@lang('labels.friendlies')</a></h3>
    <div>
        @lang('users_guide.friendlies')
    </div>
    <h3><span class="fa fa-trophy" style="margin-right:10px;"></span><a href="#">@lang('labels.tournaments')</a></h3>
    <div>
        @lang('users_guide.tournaments')
    </div>
    <h3><span class="fa fa-shopping-cart" style="margin-right:10px;"></span><a href="#">@lang('labels.shopping')</a></h3>
    <div>
        @lang('users_guide.shopping')
    </div>
    <h3><span class="fa fa-star" style="margin-right:10px;"></span><a href="#">@lang('labels.train')</a></h3>
    <div>
        @lang('users_guide.train')
    </div>
    <h3><span class="fa fa-user" style="margin-right:10px;"></span><a href="#">@lang('labels.player')</a></h3>
    <div>
        @lang('users_guide.player', ['salary_rate' => number_format(config('constants.PLAYERS_SALARY') * 100, 2), 'transferable_period' => config('constants.PLAYERS_TRANSFERABLE_PERIOD')])
    </div>
    <h3><span class="fa fa-money" style="margin-right:10px;"></span><a href="#">@lang('labels.financial_fair_play')</a></h3>
    <div>
        @lang('users_guide.financial_fair_play', ['min_team_players' => config('constants.MIN_TEAM_PLAYERS'), 'max_team_players' => config('constants.MAX_TEAM_PLAYERS'), 'max_team_funds' => formatCurrency(config('constants.MAX_TEAM_FUNDS')), 'max_team_value' => formatCurrency(config('constants.MAX_TEAM_VALUE')), 'max_player_value' => formatCurrency(config('constants.MAX_PLAYER_VALUE')), 'max_players_replace' => config('constants.MAX_PLAYERS_REPLACE')])
    </div>
</div>
@endsection