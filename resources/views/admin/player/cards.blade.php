@extends('layouts.admin')

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>Lesionados</h3>
    @foreach($players as $player)
    <div class="col-xs-12">
        <div class="col-xs-6">{{ $player->short_name }}</div>
        <div class="col-xs-4">{{ $player->team->short_name }}</div>
        <div class="col-xs-2">{{ $player->cards->cards }}</div>
    </div>
    @endforeach
</div>
@endsection
