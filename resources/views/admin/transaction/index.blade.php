@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Transacciones</h3>
    {{ $transactions->links() }}
    @foreach($transactions as $transaction)
    <div class="col-xs-12">
        <div class="col-xs-3">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
        <div class="col-xs-4">{{ $transaction->user->name }}</div>
        <div class="col-xs-4">{{ $transaction->shoppingItem->name }}</div>
        <div class="col-xs-1">{{ $transaction->credits }}</div>
    </div>
    @endforeach
    <div class="clear"></div>
    {{ $transactions->links() }}
</div>
@endsection
