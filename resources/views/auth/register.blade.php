@extends('layouts.app')

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>Registrarse</h1>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}
            <div class="inputwrapper{{ $errors->has('first_name') ? ' has-error' : '' }}">
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Nombre" required autofocus>

                @if ($errors->has('first_name'))
                <label class="error">
                    <strong>{{ $errors->first('first_name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('last_name') ? ' has-error' : '' }}">
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Apellido" required>

                @if ($errors->has('last_name'))
                <label class="error">
                    <strong>{{ $errors->first('last_name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('email') ? ' has-error' : '' }}">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="E-mail" required>

                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" name="password" placeholder="Contraseña" required>

                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>
        </form>
    </div>
</div>
@endsection
