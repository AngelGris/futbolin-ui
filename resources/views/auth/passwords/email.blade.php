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
        <form action="{{ route('password.email') }}" method="POST" role="form">
            {{ csrf_field() }}
            <div class="inputwrapper">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Driección de e-mail" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">Enviar e-mail</button>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">Contáctenos</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection
