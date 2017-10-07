@extends('layouts.app')

@section('headmeta')
@yield('headmeta-inner')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/lato.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/roboto.css') }}" type="text/css" />
<style type="text/css">
.shield-primary-color {
    @if (empty($header_team))
    fill: {{ $_team['primary_color'] }};
    @else
    fill: {{ $header_team['primary_color'] }};
    @endif
}

.shield-secondary-color {
    @if (empty($header_team))
    fill: {{ $_team['secondary_color'] }};
    @else
    fill: {{ $header_team['secondary_color'] }};
    @endif
}
</style>
@yield('styles-inner')
@endsection

@section('javascript')
<script src="{{ asset('js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/spectrum.js') }}"></script>
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
                                <span class="count">{{ $_messagesCount }}</span>
                                <span class="head-icon head-message"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_notifications))
                                <li class="nav-header">Notificaciones</li>
                                @foreach ($_notifications as $notification)
                                <li><a href="#" class="admin-messages" onclick="loadNotification({{ $notification['id'] }});">{{ $notification['title'] }}<small class="muted"> - {{ $notification['published'] }}</small></a></li>
                                @endforeach
                                <li class="viewmore">
                                    <a href="{{ route('notifications') }}">Ver todas las notificaciones</a>
                                </li>
                                @endif
                                @if (count($_messages))
                                <li class="nav-header">Mensajes</li>
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
                                @if (count($_injuries) > 0)
                                <span class="count count-alert">{{ $_playersAlertsCount }}</span>
                                @else
                                <span class="count">{{ $_playersAlertsCount }}</span>
                                @endif
                                <span class="head-icon head-users"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_injuries) > 0)
                                <li class="nav-header">Lesionados</li>
                                @foreach ($_injuries as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['injury']['name'] }} - {{ $player['recovery'] }} fechas</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_upgraded) > 0)
                                <li class="nav-header">Mejorados</li>
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
                                <li class="nav-header">Por retirarse</li>
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
                        <li class="trainning"{!! ($_team['trainable'] ? '' : ' style="display:none;"') !!}>
                            <a href="#" class="trainning-button" data-token="{{ csrf_token() }}">
                                <span class="fa fa-star"></span>
                            </a>
                        </li>
                        <li class="trainning-disabled"{!! ($_team['trainable'] ? ' style="display:none;"' : '') !!}>
                            <span class="fa fa-star-o"></span>
                            <div class="remaining-timer">00:00:00</div>
                        </li>
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
                                <span class="count">{{ $_messagesCount }}</span>
                                <span class="head-icon head-message"></span>
                                <span class="headmenu-label">Mensajes</span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (count($_notifications))
                                <li class="nav-header">Notificaciones</li>
                                @foreach ($_notifications as $notification)
                                <li><a href="#" class="admin-messages" onclick="loadNotification({{ $notification['id'] }});">{{ $notification['title'] }}<small class="muted"> - {{ $notification['published'] }}</small></a></li>
                                @endforeach
                                <li class="viewmore">
                                    <a href="{{ route('notifications') }}">Ver todas las notificaciones</a>
                                </li>
                                @endif
                                @if (count($_messages))
                                <li class="nav-header">Mensajes</li>
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
                            @if (count($_injuries) > 0)
                            <span class="count count-alert">{{ $_playersAlertsCount }}</span>
                            @else
                            <span class="count">{{ $_playersAlertsCount }}</span>
                            @endif
                            <span class="head-icon head-users"></span>
                            <span class="headmenu-label">Jugadores</span>
                            </a>
                            <ul class="dropdown-menu newusers">
                                @if (count($_injuries) > 0)
                                <li class="nav-header">Jugadores lesionados</li>
                                @foreach ($_injuries as $player)
                                <li>
                                    <a href="{{ route('player', $player['id']) }}">
                                        <strong>{{ $player['number'] }} {{ $player['first_name'] }} {{ $player['last_name'] }}</strong>
                                        <small>{{ $player['position'] }}</small><br>
                                        <span style="color:#f00;">{{ $player['injury']['name'] }} - {{ $player['recovery'] }} fechas</span>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                                @if (count($_upgraded) > 0)
                                <li class="nav-header">Jugadores mejorados</li>
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
                                <li class="nav-header">Jugadores por retirarse</li>
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
                                <button class="trainning-button"{!! ($_team['trainable'] ? '' : ' style="display:none;"') !!} data-token="{{ csrf_token() }}">
                                    <span class="fa fa-star"></span>
                                    <p>Entrenar</p>
                                </button>
                                <div class="trainning-button-disabled"{!! ($_team['trainable'] ? ' style="display:none;"' : '') !!}>
                                    <span class="fa fa-star-o"></span>
                                    <p class="remaining-timer">00:00:00</p>
                                </div>
                                <div class="userinfo">
                                    <h5>
                                        {{ $_user['first_name'] }} {{ $_user['last_name'] }} <br>
                                        <small>{{ $_user['email'] }}</small>
                                    </h5>
                                    <ul>
                                        <li><a href="{{ route('profile.edit') }}">Editar Perfil</a></li>
                                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Salir</a></li>
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
                <li class="nav-header">Navegación</li>
                <li class="nav-extras{{ (Request::path() == 'perfil/editar') ? ' active' : '' }}"><a href="{{ route('profile.edit') }}"><span class="fa fa-user"></span> Editar perfil</a></li>
                @foreach ($_navigation as $link)
                <li{!! (Request::path() == $link['url']) ? ' class="active"' : '' !!}><a href="{{ url('/' . $link['url']) }}"><span class="{{ $link['icon'] }}"></span> {{ $link['name'] }}</a></li>
                @endforeach
                <li class="nav-extras"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="fa fa-sign-out"></span> Salir</a></li>
            </ul>
        </nav>
    </div>
    <div class="rightpanel">
        <div class="pageheader">
            <div class="teamname">
                @if (empty($header_team))
                <img class="svg shield" src="{{ asset($_team['shield_file']) }}">
                <h2>{{ $_team['name'] }}</h2>
                @else
                <img class="svg shield" src="{{ asset($header_team['shield_file']) }}">
                <h2>{{ $header_team['name'] }}</h2>
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
                <div class="alert alert-info" role="alert">{!! session('flash_success') !!}</div>
                @endif
                @if(Session::has('flash_warning'))
                <div class="alert alert-warning" role="alert">{!! session('flash_success') !!}</div>
                @endif
                @if(Session::has('flash_danger'))
                <div class="alert alert-danger" role="alert">{!! session('flash_success') !!}</div>
                @endif
                <div class="row" style="margin:0;width:100%;">
                    @yield('content-inner')
                </div>
                <div class="footer">
                    <div class="footer-left">
                        <span style="font-weight:bold;">Futbolin</span><br />
                        <a href="{{ route('user-guide') }}">Guía de Usuario</a><br />
                        <a href="{{ route('contact') }}" target="_blank">Formulario de Contacto</a><br />
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection