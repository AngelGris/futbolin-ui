@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/lato.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/roboto.css') }}" type="text/css" />
@yield('styles-inner')
@endsection

@section('javascript')
<script src="{{ asset('js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@yield('javascript-inner')
@endsection

@section('content')
<div id="admin-wrapper" class="mainwrapper">
    <div class="header">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="headerinner">
                    <ul class="headmenu">
                        <li class="right">
                            <div class="userloggedinfo">
                                <img src="{{ asset('img/thumb1.png') }}" alt="" />
                                <div class="userinfo">
                                    <ul>
                                        <li><a href="{{ route('admin.password', ['domain' => $_domain]) }}">Cambiar Contraseña</a></li>
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
                        <span>Developed by: <a href="https://github.com/AngelGris" target="_blank">Angel Gris</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection