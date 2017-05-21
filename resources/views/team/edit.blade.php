@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/colorpicker.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script src="{{ asset('js/colorpicker.js') }}"></script>
<script>
    $(function() {
        loadTeamColorsPickers('{{ old('primary_color', $team['primary_color']) }}', '{{ old('secondary_color', $team['secondary_color']) }}');
    });
</script>
@endsection

@section('content-inner')
<form method="POST" action="{{ route('team') }}" class="form-horizontal" role="form">
    <input type="hidden" name="_method" value="PATCH">
    {{ csrf_field() }}
    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <label for="name" class="col-md-2 control-label">Nombre del equipo</label>
        <div class="col-md-10">
            <input type="text" class="form-control input-default" name="name" value="{{ old('name', $team['name']) }}" required />
            @if ($errors->has('name'))
            <label class="error">
                <strong>{{ $errors->first('name') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('short_name') ? ' has-error' : '' }}">
        <label for="short_name" class="col-md-2 control-label">Nombre corto del equipo</label>
        <div class="col-md-10">
            <input type="text" class="form-control input-default" name="short_name" maxlength="5" value="{{ old('short_name', $team['short_name']) }}" required />
            @if ($errors->has('short_name'))
            <label class="error">
                <strong>{{ $errors->first('short_name') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group{{ $errors->has('stadium_name') ? ' has-error' : '' }}">
        <label class="col-md-2 control-label">Nombre del estadio</label>
        <div class="col-md-10">
            <input type="text" class="form-control input-default" name="stadium_name" value="{{ old('stadium_name', $team['stadium_name']) }}" required />
            @if ($errors->has('stadium_name'))
            <label class="error">
                <strong>{{ $errors->first('stadium_name') }}</strong>
            </label>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">Colores</label>
        <div class="col-md-10">
            <input type="hidden" name="primary_color" id="primary_color_picker" value="{{ old('primary_color', $team['primary_color']) }}" class="form-control input-sm" />
            <span id="primary_color_selector" class="colorselector"><span style="background-color:{{ old('primary_color', $team['primary_color']) }}"></span></span>
            <input type="hidden" name="secondary_color" id="secondary_color_picker" value="{{ old('secondary_color', $team['secondary_color']) }}" class="form-control input-sm" />
            <span id="secondary_color_selector" class="colorselector"><span style="background-color:{{ old('secondary_color', $team['secondary_color'])}}"></span></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">Escudo</label>
        <div class="col-md-10">
            <input id="shield-value" type="hidden" name="shield" value="{{ $team['shield'] }}" />
            <a href="#" id="shield-select"><img class="svg" id="shield-svg" src="{{ $team['shieldFile'] }}" style="width:70px;" /></a>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Guardar</button>
        </div>
    </div>
</form>
@include('modules.modals.shieldselect')
@endsection
