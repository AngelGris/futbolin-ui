@extends('layouts.admin')

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>Lesionados</h3>
    ARQ: {{ $stats['ARQ'] }} ({{ number_format($stats['ARQ'] * 100 / $stats['total'], 2) }}%) - DEF: {{ $stats['DEF'] }} ({{ number_format($stats['DEF'] * 100 / $stats['total'], 2) }}%) - MED: {{ $stats['MED'] }} ({{ number_format($stats['MED'] * 100 / $stats['total'], 2) }}%) - ATA: {{ $stats['ATA'] }} ({{ number_format($stats['ATA'] * 100 / $stats['total'], 2) }}%) - TOT: {{ $stats['total'] }}
    @foreach($players as $player)
    <div class="col-xs-12">
        <div class="col-xs-3">{{ $player->short_name }}</div>
        <div class="col-xs-1">{{ $player->position }}</div>
        <div class="col-xs-2">{{ $player->team->short_name }}</div>
        <div class="col-xs-5">{{ $player->injury->name }}</div>
        <div class="col-xs-1"{!! $player->healed ? ' style="color:#0b0;"' : ' style="color:#f00;"' !!}>{{ $player->recovery }}</div>
    </div>
    @endforeach
</div>
@endsection
