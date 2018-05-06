@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Compras</h3>
    {{ $payments->links() }}
    @foreach($payments as $payment)
    <div class="col-xs-12">
        <div class="col-xs-2">{{ $payment->created_at->format('d/m/Y H:i:s') }}</div>
        <div class="col-xs-3">{{ $payment->user->name }}</div>
        <div class="col-xs-3">{{ $payment->creditItem->name }}</div>
        <div class="col-xs-1">{{ $payment->method }}</div>
        <div class="col-xs-1">{{ $payment->status->name }}</div>
        <div class="col-xs-1">${{ number_format($payment->amount_total, 2) }}</div>
        <div class="col-xs-1">${{ number_format($payment->amount_earnings, 2) }}</div>
    </div>
    @endforeach
    <div class="clear"></div>
    {{ $payments->links() }}
</div>
@endsection
