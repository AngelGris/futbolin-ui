@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/market.css') }}" type="text/css" />
@endsection

@section('content-inner')
<div class="col-sm-12">
    {{ $transactions->links() }}
</div>
<div class="clear"></div>
<table class="table table-bordered table-transferables responsive">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Nombre</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
            <th>Vendedor</th>
            <th>Comprador</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
        <tr class="{{ strtolower($transaction->player->position) }}">
            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            <td><a href="{{ route('player', $transaction->player->id) }}">{{ $transaction->player->first_name . ' ' . $transaction->player->last_name  }}</a></td>
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $transaction->player->position_long }}">{{ $transaction->player->position }}</span></td>
            <td>
                @if ($transaction->seller)
                <a href="{{ route('team.show', $transaction->seller->id) }}">{{ $transaction->seller->name }}</a>
                @else
                <i>Jugador libre</i>
                @endif
            </td>
            <td><a href="{{ route('team.show', $transaction->buyer->id) }}">{{ $transaction->buyer->name }}</a></td>
            <td>{{ formatCurrency($transaction->value) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="col-sm-12">
    {{ $transactions->links() }}
</div>
@endsection
