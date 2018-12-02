@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/market.css') }}" type="text/css" />
@endsection

@section('content-inner')
<div class="col-sm-12">
    <a class="btn btn-sm btn-primary" href="{{ route('market') }}">@lang('labels.back_to_market')</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.offers') }}">@lang('labels.my_offers')</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.following') }}">@lang('labels.following')</a>
</div>
<div class="col-sm-12">
    {{ $transactions->links() }}
</div>
<div class="clear"></div>
<table class="table table-bordered table-transferables responsive">
    <thead>
        <tr>
            <th>@lang('labels.date')</th>
            <th>@lang('labels.name')</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.position')">@lang('attributes.position_short')</span></th>
            <th>@lang('labels.seller')</th>
            <th>@lang('labels.buyer')</th>
            <th>@lang('labels.value')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
        <tr class="{{ strtolower($transaction->player->position) }}">
            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            @if (is_null($transaction->player->deleted_at))
            <td><a href="{{ route('player', $transaction->player->id) }}">{{ $transaction->player->first_name . ' ' . $transaction->player->last_name  }}</a></td>
            @else
            <td>{!! $transaction->player->name !!}</td>
            @endif
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $transaction->player->position_long }}">{{ $transaction->player->position }}</span></td>
            <td>
                @if ($transaction->seller)
                <a href="{{ route('team.show', $transaction->seller->id) }}">{{ $transaction->seller->name }}</a>
                @else
                <i>@lang('labels.free_player')</i>
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
