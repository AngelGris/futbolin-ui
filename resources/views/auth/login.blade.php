@extends('layouts.app')

@section('headmeta')
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="{{ date('D, d M Y H:i:s e') }}" />
<meta http-equiv="pragma" content="no-cache" />
@endsection

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>Ingresar</h1>
        <form id="login" action="{{ route('login') }}" method="POST" role="form">
            {{ csrf_field() }}
            <div class="inputwrapper">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Dirección de e-mail" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" required>
                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
            <div class="inputwrapper">
                <label class="pull-left"><input type="checkbox" class="remember" name="remember" {{ old('remember') | true ? 'checked' : '' }} /> Recordarme</label>
            </div>
            <div class="inputwrapper col-xs-12">
                <div class="pull-right">¿No es miembro? <a href="{{ route('register') }}" class="btn btn-danger">Afiliarse</a></div>
            </div>
            <div class="inputwrapper col-xs-12">
                <div class="pull-right">¿Olvidó su contraseña? <a href="{{ route('password.request') }}">Recuperarla</a></div>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">Contáctenos</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection