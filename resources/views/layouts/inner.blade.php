@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/lato.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/roboto.css') }}" type="text/css" />
@yield('styles-inner')
@endsection

@section('javascript')
<script src="{{ asset('js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@yield('javascript-inner')
@endsection

@section('content')
<div class="mainwrapper">
    <div class="header">
        <div class="logo">
            <a href="{{ route('home') }}">Futbolin</a>
        </div>
        <div class="headerinner">
            <ul class="headmenu">
                <li class="odd">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="count">4</span>
                        <span class="head-icon head-message"></span>
                        <span class="headmenu-label">Mensajes</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Messages</li>
                        <li><a href=""><span class="glyphicon glyphicon-envelope"></span> New message from <strong>Jack</strong> <small class="muted"> - 19 hours ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-envelope"></span> New message from <strong>Daniel</strong> <small class="muted"> - 2 days ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-envelope"></span> New message from <strong>Jane</strong> <small class="muted"> - 3 days ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-envelope"></span> New message from <strong>Tanya</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-envelope"></span> New message from <strong>Lee</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li class="viewmore"><a href="messages.html">View More Messages</a></li>
                    </ul>
                </li>
                <li>
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <span class="count">10</span>
                    <span class="head-icon head-users"></span>
                    <span class="headmenu-label">Jugadores</span>
                    </a>
                    <ul class="dropdown-menu newusers">
                        <li class="nav-header">New Users</li>
                        <li>
                            <a href="">
                                <img src="images/photos/thumb1.png" alt="" class="userthumb" />
                                <strong>Draniem Daamul</strong>
                                <small>April 20, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="images/photos/thumb2.png" alt="" class="userthumb" />
                                <strong>Shamcey Sindilmaca</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="images/photos/thumb3.png" alt="" class="userthumb" />
                                <strong>Nusja Paul Nawancali</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="images/photos/thumb4.png" alt="" class="userthumb" />
                                <strong>Rose Cerona</strong>
                                <small>April 18, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="images/photos/thumb5.png" alt="" class="userthumb" />
                                <strong>John Doe</strong>
                                <small>April 16, 2013</small>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="odd">
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <span class="count">1</span>
                    <span class="head-icon head-bar"></span>
                    <span class="headmenu-label">Estadista</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Statistics</li>
                        <li><a href=""><span class="glyphicon glyphicon-align-left"></span> New Reports from <strong>Products</strong> <small class="muted"> - 19 hours ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-align-left"></span> New Statistics from <strong>Users</strong> <small class="muted"> - 2 days ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-align-left"></span> New Statistics from <strong>Comments</strong> <small class="muted"> - 3 days ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-align-left"></span> Most Popular in <strong>Products</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li><a href=""><span class="glyphicon glyphicon-align-left"></span> Most Viewed in <strong>Blog</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li class="viewmore"><a href="charts.html">View More Statistics</a></li>
                    </ul>
                </li>
                <li class="right">
                    <div class="userloggedinfo">
                        <img src="{{ asset('img/thumb1.png') }}" alt="" />
                        <div class="userinfo">
                            <h5>
                                {{ $user['first_name'] }} {{ $user['last_name'] }} <br>
                                <small>{{ $user['email'] }}</small>
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
    <div class="leftpanel">
        <div class="leftmenu">
            <ul class="nav nav-tabs nav-stacked">
                <li class="nav-header">Navegación</li>
                @foreach ($navigation as $link)
                <li{{ (Request::path() == $link['url']) ? ' class=active' : '' }}><a href="{{ url('/' . $link['url']) }}"><span class="{{ $link['icon'] }}"></span> {{ $link['name'] }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="rightpanel">
        <div class="pageheader">
            <div class="teamname">
                {{ $team['name'] }}
                <div class="primarycolor" style="background-color:{{ $team['primary_color'] }};border-color:{{ ($team['primary_color'] == '#ffffff') ? $team['secondary_color'] : $team['primary_color'] }}"></div>
                <div class="secondarycolor" style="background-color:{{ $team['secondary_color'] }};border-color:{{ ($team['secondary_color'] == '#ffffff') ? $team['primary_color'] : $team['secondary_color'] }}"></div>
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
                        <span>Futbolin</span>
                    </div>
                    <div class="footer-right">
                        <span>Developed by: <a href="https://github.com/AngelGris" target="_blank">Angel Gris</a> - Shamcey template by: <a href="http://themepixels.com/" target="_blank">ThemePixels</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection