@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/spectrum.css') }}" type="text/css" />
@endsection

@section('javascript')
<script src="{{ asset('js/spectrum.js') }}"></script>
@endsection

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>Crear Equipo</h1>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('team.store') }}">
            {{ csrf_field() }}
            <div class="inputwrapper{{ $errors->has('name') ? ' has-error' : '' }}">
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nombre del equipo" class="form-control" required autofocus />

                @if ($errors->has('name'))
                <label class="error">
                    <strong>{{ $errors->first('name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('short_name') ? ' has-error' : '' }}">
                <input id="short_name" type="text" name="short_name" value="{{ old('short_name') }}" maxlength="5" placeholder="Nombre corto del equipo (5 caractéres)" class="form-control" required />

                @if ($errors->has('short_name'))
                <label class="error">
                    <strong>{{ $errors->first('short_name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('stadium_name') ? ' has-error' : '' }}">
                <input id="stadium_name" type="text" name="stadium_name" value="{{ old('stadium_name') }}" placeholder="Nombre del estadio" class="form-control" required />

                @if ($errors->has('last_name'))
                <label class="error">
                    <strong>{{ $errors->first('stadium_name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <p style="color:#fff;">Colores del equipo</p>
                <input type="text" class="colorpicker" name="primary_color" id="primary_color_picker" value="#ffffff" />
                <input type="text" class="colorpicker" name="secondary_color" id="secondary_color_picker" value="#000000" />
                @if ($errors->has('primary_color'))
                <label class="error">
                    <strong>{{ $errors->first('primary_color') }}</strong>
                </label>
                @endif
                @if ($errors->has('secondary_color'))
                <label class="error">
                    <strong>{{ $errors->first('secondary_color') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <p style="color:#fff;">Escudo</p>
                <input id="shield-value" type="hidden" name="shield" value="1" />
                <div style="background-color:#fff;padding:10px;">
                    <a href="#" id="shield-select"><img class="svg" id="shield-svg" src="{{ asset('/img/shield/shield-01.svg') }}" style="width:70px;" /></a>
                </div>
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
@include('modules.modals.shieldselect')
@endsection
