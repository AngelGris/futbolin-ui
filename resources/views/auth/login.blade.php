@extends('layouts.app')

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>Ingresar</h1>
        <form id="login" action="{{ route('login') }}" method="POST" role="form">
            {{ csrf_field() }}
            <div class="inputwrapper">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your e-mail" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="password" type="password" class="form-control" name="password" placeholder="Enter your password" required>
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
                <div class="pull-right">¿No es miembro? <a href="{{ route('register') }}">Afiliarse</a></div>
                <label><input type="checkbox" class="remember" name="remember" {{ old('remember') | true ? 'checked' : '' }} /> Recordarme</label>
            </div>
            <div class="inputwrapper">
                <div class="pull-right">¿Olvidó su contraseña? <a href="{{ route('password.request') }}">Recuperarla</a></div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
</div>
@endsection