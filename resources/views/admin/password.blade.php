@extends('layouts.admin')

@section('content-inner')
<form method="POST" action="{{ route('admin.password', ['domain' => $domain]) }}" class="form-horizontal" role="form">
    <input type="hidden" name="_method" value="PATCH">
    {{ csrf_field() }}
    <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
        <label for="old_password" class="col-md-2 control-label">Contraseña Actual</label>
        <div class="col-md-10">
            <input type="password" class="form-control input-default" name="old_password" id="old_password" value="{{ old('old_password') }}" required>
            @if ($errors->has('old_password'))
            <label class="error">
                <strong>{{ $errors->first('old_password') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
        <label for="new_password" class="col-md-2 control-label">Contraseña Nueva</label>
        <div class="col-md-10">
            <input type="password" class="form-control input-default" name="new_password" id="new_password" value="{{ old('new_password') }}" required>
            @if ($errors->has('new_password'))
            <label class="error">
                <strong>{{ $errors->first('new_password') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="new_password_confirmation" class="col-md-2 control-label">Confirmar Contraseña</label>
        <div class="col-md-10">
            <input type="password" class="form-control input-default" name="new_password_confirmation" id="new_password_confirmation" value="{{ old('new_password_confirmation') }}" required>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Cambiar</button>
        </div>
    </div>
</form>
@endsection
