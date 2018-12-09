@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/shopping.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script type="text/javascript">
    $('.shopping-item-wrapper').click(function() {
        $('#item-id').val($(this).data('id'));
        $('#modal-buy-confirmation').html(Lang.trans('messages.confirm_purchase_confirmation', { item_name: $(this).data('item'), item_price: $(this).data('price') }))
        $('#buy-item').text(Lang.trans('labels.buy_item', { 'item_name': $(this).data('item') }));
    });

    $('#buy-item').click(function() {
        $('#frm-buy').submit();
    });
</script>
@endsection

@section('content-inner')
<div>
    <div class="col-sm-12" style="margin-bottom:20px;">
        <h4 style="text-align: right;">@lang('labels.credits'): {{ $_user->credits }} <a href="{{ route('shopping.credits') }}" class="btn btn-xs btn-primary">@lang('labels.buy_credits')</a></h4>
    </div>
    @foreach($items as $item)
    <div class="col-sm-4">
        @if ($item->id == 3 && $_team->trainer >= \Carbon\Carbon::now())
        <div class="shopping-item-wrapper-disabled">
        @elseif ($_user->credits >= $item->price)
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-buy-confirm" data-item="{{ $item->name }}" data-price="{{ $item->price }}">
        @else
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-buy-denied" data-item="{{ $item->name }}" data-price="{{ $item->price }}">
        @endif
            <h3>@lang('shopping.item_name_' . $item->id)</h3>
            <span class="{{ $item->icon }}"></span>
            <p>@lang('shopping.item_description_' . $item->id)</p>
            <div class="shopping-item-price">{{ $item->price }} {{ str_plural('Fúlbo', $item->price) }}</div>
        </div>
    </div>
    @endforeach
    <div class="clear">
</div>
<div class="modal fade" id="modal-buy-denied" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('messages.not_enough_credits_title')</h4>
            </div>
            <div class="modal-body">
                <p>@lang('messages.not_enough_credits_body')</p>
                <p>@lang('messages.not_enough_credits_confirmation')</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-primary">@lang('labels.buy_credits')</a>
                <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-buy-confirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('messages.confirm_purchase_title')</h4>
            </div>
            <div class="modal-body">
                <p id="modal-buy-confirmation"></p>
            </div>
            <div class="modal-footer">
                <button id="buy-item" class="btn btn-primary"></button>
                <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
            </div>
        </div>
    </div>
</div>
<form id="frm-buy" method="post" action="{{ route('shopping.buy') }}">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="item-id">
</form>
@endsection
