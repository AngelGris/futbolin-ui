@extends('layouts.inner')

@section('content-inner')
<form method="POST" action="{{ route('profile') }}" class="form-horizontal" role="form">
    <input type="hidden" name="_method" value="PATCH">
    {{ csrf_field() }}
    <div class="form-group">
        <label class="col-md-2 control-label">Email</label>
        <div class="col-md-10">{{ $_user['email'] }}</div>
    </div>
    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
        <label for="first_name" class="col-md-2 control-label">Nombre</label>
        <div class="col-md-10">
            <input type="text" class="form-control input-default" name="first_name" value="{{ old('first_name', $_user['first_name']) }}" required>
            @if ($errors->has('first_name'))
            <label class="error">
                <strong>{{ $errors->first('first_name') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
        <label for="last_name" class="col-md-2 control-label">Apellido</label>
        <div class="col-md-10">
            <input type="text" class="form-control input-default" name="last_name" value="{{ old('last_name', $_user['last_name']) }}" required>
            @if ($errors->has('last_name'))
            <label class="error">
                <strong>{{ $errors->first('last_name') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">Contraseña</label>
        <div class="col-md-10">
            <a href="{{ route('profile.password') }}">Cambiar contraseña</a>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Guardar</button>
        </div>
    </div>
</form>
@endsection
