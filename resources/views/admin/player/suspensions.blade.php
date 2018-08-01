@extends('layouts.admin')

@section('content-inner')
<div class="col-md-12 zebra">
    <h3>Suspendidos</h3>
    @foreach($players as $player)
    <div class="col-xs-12">
        <div class="col-xs-3">{{ $player->short_name }}</div>
        <div class="col-xs-2">{{ $player->position }}</div>
        <div class="col-xs-2">{{ $player->team->short_name }}</div>
        <div class="col-xs-4">{{ $player->suspension_type }}</div>
        <div class="col-xs-1">{{ $player->suspension }}</div>
    </div>
    @endforeach
</div>
@endsection
