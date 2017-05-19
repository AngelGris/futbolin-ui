@extends('layouts.admin')

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>{{ $user['name'] }}</h3>
    <div class="col-xs-12">
        <div class="col-xs-6">Equipos</div>
        <div class="col-xs-6"><a href="{{ route('admin.team', [getDomain(), $user['team']['id']]) }}">{{ $user['team']['name'] }}</a></div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">E-mail</div>
        <div class="col-xs-6">{{ $user['email'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Creado</div>
        <div class="col-xs-6">{{ $user['created_at'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Actualizado</div>
        <div class="col-xs-6">{{ $user['updated_at'] }}</div>
    </div>
</div>
@endsection
