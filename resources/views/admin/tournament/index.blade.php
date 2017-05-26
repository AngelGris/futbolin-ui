@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3 style="float:left;">Torneos</h3>
    <a href="{{ route('admin.tournament.create', getDomain()) }}" class="btn btn-danger" style="float:right;">Crear Torneo</a>
    <div class="clear"></div>
    @if(count($tournaments) == 0)
    <h4>No hay torneos para mostrar</h4>
    @else
    <div class="col-xs-12">
        <div class="col-xs-2">ID</div>
        <div class="col-xs-4" style="text-align:center">Nombre</div>
        <div class="col-xs-2" style="text-align:center">Cat.</div>
        <div class="col-xs-2" style="text-align:center">Zonas</div>
        <div class="col-xs-2" style="text-align:center">Opciones</div>
    </div>
    @foreach($tournaments as $tournament)
    <div class="col-xs-12">
        <div class="col-xs-2">{{ $tournament['id'] }}</div>
        <div class="col-xs-4">{{ $tournament['name'] }}</div>
        <div class="col-xs-2" style="text-align:center">{{ $tournament['categories'] }}</div>
        <div class="col-xs-2" style="text-align:center">{{ $tournament['zones'] }}</div>
        <div class="col-xs-2" style="text-align:center"><a href="{{ route('admin.tournament', ['domain' => getDomain(), 'id' => $tournament['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    @endif
</div>
@endsection