@extends('layouts.inner')

@section('content-inner')
<div class="form-group">
    <label class="col-md-2 control-label">Nombre del equipo</label>
    <div class="col-md-10">{{ $_team['name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Nombre corto del equipo</label>
    <div class="col-md-10">{{ $_team['short_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Nombre del estadio</label>
    <div class="col-md-10">{{ $_team['stadium_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Colores</label>
    <div class="col-md-10">
        <div class="col-md-2 teamprimarycolor" style="background-color:{{ $_team['primary_color'] }};border-color:{{ ($_team['primary_color'] == '#ffffff') ? $_team['secondary_color'] : $_team['primary_color'] }}"></div>
        <div class="col-md-2 teamsecondarycolor" style="background-color:{{ $_team['secondary_color'] }};border-color:{{ ($_team['secondary_color'] == '#ffffff') ? $_team['primary_color'] : $_team['secondary_color'] }}"></div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Escudo</label>
    <div class="col-md-10"><img class="svg" src="{{ $_team['shieldFile'] }}"  data-color-primary="{{ $_team['primary_color'] }}" data-color-secondary="{{ $_team['secondary_color'] }}" style="width:70px;"></div>
</div>
<div class="form-group">
    <a href="{{ route('team.edit') }}" class="btn btn-default">Editar equipo</a>
</div>
@endsection
