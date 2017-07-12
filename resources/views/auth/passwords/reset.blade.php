@extends('layouts.app')

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>Recuperar Contraseña</h1>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('password.request') }}" method="POST" role="form">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="inputwrapper">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Dirección de e-mail" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña" required>
                @if ($errors->has('password_confirmation'))
                <label class="error">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">Contáctenos</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection
