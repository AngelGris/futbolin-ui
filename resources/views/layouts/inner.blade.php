@extends('layouts.app')

@section('headmeta')
@yield('headmeta-inner')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/lato.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/roboto.css') }}" type="text/css" />
@yield('styles-inner')
@endsection

@section('javascript')
<script src="{{ asset('js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/spectrum.js') }}"></script>
<script src="{{ asset('js/translations.js') }}"></script>
@yield('javascript-inner')
<script type="text/javascript">
    var trainable_remaining = {{ $_team['trainable_remaining'] }}
</script>
@if (Session::has('admin_message'))
<script type="text/javascript">
    loadAdminMessage({{ session('admin_message') }});
</script>
@endif
@if (App::environment('production'))
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-98862897-1', 'auto');
    ga('send', 'pageview');
</script>
@endif
@endsection

@section('content')
<div class="mainwrapper">
    <div class="header">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <ul class="headmenu headmenu-toggle">
                        @if (count($_notifications) or count($_messages))
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                @if ($_messagesCount > 0)
                                <span class="count unread-count count-alert">{{ $_messagesCount }}</span>
                                @else
                                <span class="count">{{ $_messagesCount }}</span>
                                @endif
                                <span class="head-icon head-message"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_notifications))
                                <li class="nav-header">@lang('labels.notifications')</li>
                                @foreach ($_notifications as $notification)
                                <li><a href="#" class="admin-messages open-notification{{ is_null($notification->read_on) ? ' unread' : '' }}" data-id="{{ $notification['id'] }}">{{ $notification['title'] }}<small class="muted"> - {{ $notification['published'] }}</small></a></li>
                                @endforeach
                                <li class="viewmore">
                                    <a href="{{ route('notifications') }}">@lang('labels.view_all_notifications')</a>
                                </li>
                                @endif
                                @if (count($_messages))
                                <li class="nav-header">@lang('labels.messages')</li>
                                @foreach ($_messages as $message)
                                <li><a href="#" class="admin-messages" onclick="loadAdminMessage({{ $message['id'] }});">{{ $message['title'] }}<small class="muted"> - {{ $message['published'] }}</small></a></li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if ($_playersAlertsCount > 0)
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                @if (count($_suspensions) > 0 || count($_injuries) > 0)
                                <span class="count count-alert">{{ $_playersAlertsCount }}</span>
                                @else
                                <span class="count">{{ $_playersAlertsCount }}</span>
                                @endif
                                <span class="head-icon head-users"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_transferables) > 0)
                                <li class="nav-header">@lang('labels.transferable_players')</li>
                                @foreach ($_transferables as $player)
                                <li>
                                    <a href="{{ route('player', $player->id) }}">
                                        <strong>{{ $player->number }} {{ $player->first_name }} {{ $player->last_name }}</strong>
                                        <small>{{ $player->position }}</small><br>
                                        @if ($player->best_offer_team)
                                        <span>@lang('labels.best_offer'): {{ formatCurrency($player->best_offer_value) }}</span>
                                        @else
                                        <span class="text-uppercase">@lang('labels.no_offers')</span>
                                        @endif
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_suspensions) > 0)
                                <li class="nav-header">@lang('labels.suspended')</li>
                                @foreach ($_suspensions as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['suspension_type'] }} - {{ $player['suspension'] }} @choice('countables.rounds', $player['suspension'])</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_injuries) > 0)
                                <li class="nav-header">@lang('labels.injured')</li>
                                @foreach ($_injuries as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['injury']['name'] }} - {{ $player['recovery'] }} @choice('countables.rounds', $player['recovery'])</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_upgraded) > 0)
                                <li class="nav-header">@lang('labels.improved')</li>
                                @foreach ($_upgraded as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_retiring) > 0)
                                <li class="nav-header">@lang('labels.retiring')</li>
                                @foreach ($_retiring as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($_team->trainer > \Carbon\Carbon::now())
                        <li class="trainning-disabled">
                            <span class="fa fa-user-circle"></span>
                            <div>{{ $_team->trainer_remaining }}</div>
                        </li>
                        @else
                        <li class="trainning"{!! ($_team['trainable'] ? '' : ' style="display:none;"') !!}>
                            <a href="#" class="trainning-button" data-token="{{ csrf_token() }}">
                                <img src="{{ asset('img/train.svg') }}" />
                            </a>
                        </li>
                        <li class="trainning-disabled"{!! ($_team['trainable'] ? ' style="display:none;"' : '') !!}>
                            <img src="{{ asset('img/train-disabled.svg') }}" />
                            <div class="remaining-timer">00:00:00</div>
                        </li>
                        @endif
                    </ul>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <div class="logo">
                        <a href="{{ route('home') }}">Futbolin</a>
                    </div>
                </div>

                <div class="headerinner">
                    <ul class="headmenu">
                        @if (count($_notifications) or count($_messages))
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                @if ($_messagesCount > 0)
                                <span class="count unread-count count-alert">{{ $_messagesCount }}</span>
                                @else
                                <span class="count">{{ $_messagesCount }}</span>
                                @endif
                                <span class="head-icon head-message"></span>
                                <span class="headmenu-label">@lang('labels.messages')</span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_notifications))
                                <li class="nav-header">@lang('labels.notifications')</li>
                                @foreach ($_notifications as $notification)
                                <li><a href="#" class="admin-messages open-notification{{ is_null($notification->read_on) ? ' unread' : '' }}" data-id="{{ $notification['id'] }}">{{ $notification['title'] }}<small class="muted"> - {{ $notification['published'] }}</small></a></li>
                                @endforeach
                                <li class="viewmore">
                                    <a href="{{ route('notifications') }}">@lang('labels.view_all_notifications')</a>
                                </li>
                                @endif
                                @if (count($_messages))
                                <li class="nav-header">@lang('labels.messages')</li>
                                @foreach ($_messages as $message)
                                <li><a href="#" class="admin-messages" onclick="loadAdminMessage({{ $message['id'] }});">{{ $message['title'] }}<small class="muted"> - {{ $message['published'] }}</small></a></li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if ($_playersAlertsCount > 0)
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                            @if (count($_suspensions) > 0 || count($_injuries) > 0)
                            <span class="count count-alert">{{ $_playersAlertsCount }}</span>
                            @else
                            <span class="count">{{ $_playersAlertsCount }}</span>
                            @endif
                            <span class="head-icon head-users"></span>
                            <span class="headmenu-label">@lang('labels.players')</span>
                            </a>
                            <ul class="dropdown-menu newusers">
                                @if (count($_transferables) > 0)
                                <li class="nav-header">@lang('labels.transferable_players')</li>
                                @foreach ($_transferables as $player)
                                <li>
                                    <a href="{{ route('player', $player->id) }}">
                                        <strong>{{ $player->number }} {{ $player->first_name }} {{ $player->last_name }}</strong>
                                        <small>{{ $player->position }}</small><br>
                                        @if ($player->best_offer_team)
                                        <span>@lang('labels.best_offer'): {{ formatCurrency($player->best_offer_value) }}</span>
                                        @else
                                        <span class="text-uppercase">@lang('labels.no_offers')</span>
                                        @endif
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_suspensions) > 0)
                                <li class="nav-header">@lang('labels.suspended_players')</li>
                                @foreach ($_suspensions as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['suspension_type'] }} - {{ $player['suspension'] }} @choice('countables.rounds', $player['suspension'])</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_injuries) > 0)
                                <li class="nav-header">@lang('labels.injured_players')</li>
                                @foreach ($_injuries as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['injury']['name'] }} - {{ $player['recovery'] }} @choice('countables.rounds', $player['recovery'])</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_upgraded) > 0)
                                <li class="nav-header">@lang('labels.improved_players')</li>
                                @foreach ($_upgraded as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_retiring) > 0)
                                <li class="nav-header">@lang('labels.players_retiring')</li>
                                @foreach ($_retiring as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </li>
                        @endif
                        <li class="right">
                            <div class="userloggedinfo">
                                @if($_team->trainer > \Carbon\Carbon::now())
                                <div class="trainning-button-disabled">
                                    <span class="fa fa-user-circle"></span>
                                    <p>{{ $_team->trainer_remaining }}</p>
                                </div>
                                @else
                                <button class="trainning-button"{!! ($_team->trainable ? '' : ' style="display:none;"') !!} data-token="{{ csrf_token() }}">
                                    <img src="{{ asset('img/train.svg') }}" />
                                    <p>@lang('labels.train')</p>
                                </button>
                                <button class="trainning-button-disabled"{!! ($_team->trainable ? ' style="display:none;"' : '') !!} disabled>
                                    <img src="{{ asset('img/train-disabled.svg') }}" />
                                    <p class="remaining-timer">00:00:00</p>
                                </button>
                                @endif
                                <div class="userinfo">
                                    <h5>
                                        {{ $_user['first_name'] }} {{ $_user['last_name'] }} <br>
                                        <small>{{ $_user['email'] }}</small>
                                    </h5>
                                    <ul>
                                        <li><a href="{{ route('profile.edit') }}">@lang('labels.edit_profile')</a></li>
                                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">@lang('labels.exit')</a></li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="leftpanel">
        <nav class="leftmenu collapse" id="bs-navbar-collapse">
            <ul class="nav nav-tabs nav-stacked">
                <li class="nav-header">@lang('labels.navigation')</li>
                <li class="nav-extras{{ (Request::path() == 'perfil/editar') ? ' active' : '' }}"><a href="{{ route('profile.edit') }}"><span class="fa fa-user"></span> @lang('labels.edit_profile')</a></li>
                @foreach ($_navigation as $link)
                <li{!! (Request::path() == $link['url']) ? ' class="active"' : '' !!}><a href="{{ url('/' . $link['url']) }}"><span class="{{ $link['icon'] }}"></span> {{ $link['name'] }}</a></li>
                @endforeach
                <li class="nav-extras"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="fa fa-sign-out"></span> @lang('labels.exit')</a></li>
            </ul>
        </nav>
    </div>
    <div class="rightpanel">
        <div class="pageheader">
            <div class="teamname">
                @if (empty($header_team) || $header_team->id == $_team->id)
                <img class="svg shield" src="{{ asset($_team['shield_file']) }}" data-color-primary="{{ $_team['primary_color'] }}" data-color-secondary="{{ $_team['secondary_color'] }}">
                <div>
                    <h2>{{ $_team->name }}</h2>
                    <h4>{!! $_team->formatted_funds !!}</h4>
                </div>
                @else
                <img class="svg shield" src="{{ asset($header_team['shield_file']) }}" data-color-primary="{{ $header_team['primary_color'] }}" data-color-secondary="{{ $header_team['secondary_color'] }}">
                <h2 class="no-credits">{{ $header_team['name'] }}</h2>
                @endif
            </div>
            <div class="pageicon"><span class="{{ $icon }}"></span></div>
            <div class="pagetitle">
                <h5>{{ $subtitle }}</h5>
                <h1>{{ $title }}</h1>
            </div>
        </div>
        <div class="maincontent">
            <div class="maincontentinner">
                @if(Session::has('flash_success'))
                <div class="alert alert-success" role="alert">{!! session('flash_success') !!}</div>
                @endif
                @if(Session::has('flash_info'))
                <div class="alert alert-info" role="alert">{!! session('flash_info') !!}</div>
                @endif
                @if(Session::has('flash_warning'))
                <div class="alert alert-warning" role="alert">{!! session('flash_warning') !!}</div>
                @endif
                @if(Session::has('flash_danger'))
                <div class="alert alert-danger" role="alert">{!! session('flash_danger') !!}</div>
                @endif
                <div class="row" style="margin:0;width:100%;">
                    @yield('content-inner')
                </div>
                <div class="footer">
                    <div class="footer-left">
                        <span style="font-weight:bold;">Futbolin</span><br />
                        <a href="{{ route('user-guide') }}">@lang('labels.users_guide')</a><br />
                        <a href="{{ route('contact') }}" target="_blank">@lang('labels.contact_form')</a><br />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-admin-message">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 id="modal-admin-message-title" class="modal-title"></h4>
            </div>
            <div id="modal-admin-message-body" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
            </div>
        </div>
    </div>
</div>
@endsection