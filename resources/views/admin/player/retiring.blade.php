@extends('layouts.admin')

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>Lesionados</h3>
    {{ $players->links() }}
    @foreach($players as $player)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ $player->shortName }}</div>
        <div class="col-xs-2">{{ $player->position }}</div>
        <div class="col-xs-6">{{ $player->team['name'] }}</div>
    </div>
    @endforeach
    <div class="clear"></div>
    {{ $players->links() }}
</div>
@endsection
