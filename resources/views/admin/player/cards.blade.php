@extends('layouts.admin')

@section('content-inner')
<div class="col-md-12 zebra">
    <h3>Tarjetas</h3>
    @foreach($players as $player)
    <div class="col-xs-12">
        <div class="col-xs-5">{{ $player->short_name }}</div>
        <div class="col-xs-2">{{ $player->position }}</div>
        <div class="col-xs-3">{{ $player->team->short_name }}</div>
        <div class="col-xs-2">{{ $player->cards->cards }}</div>
    </div>
    @endforeach
</div>
@endsection
