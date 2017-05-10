@extends('layouts.inner')

@section('content-inner')
<div class="form-group">
    <label class="col-md-2 control-label">Nombre del equipo</label>
    <div class="col-md-10">{{ $team['name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Nombre corto del equipo</label>
    <div class="col-md-10">{{ $team['short_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Nombre del estadio</label>
    <div class="col-md-10">{{ $team['stadium_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">Colores</label>
    <div class="col-md-10">
        <div class="col-md-6 teamprimarycolor" style="background-color:{{ $team['primary_color'] }};border-color:{{ ($team['primary_color'] == '#ffffff') ? $team['secondary_color'] : $team['primary_color'] }}"></div>
        <div class="col-md-6 teamsecondarycolor" style="background-color:{{ $team['secondary_color'] }};border-color:{{ ($team['secondary_color'] == '#ffffff') ? $team['primary_color'] : $team['secondary_color'] }}"></div>
    </div>
</div>
<div class="form-group">
    <a href="{{ route('team.edit') }}" class="btn btn-default">Editar equipo</a>
</div>
@endsection
