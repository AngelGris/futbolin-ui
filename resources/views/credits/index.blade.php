@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/shopping.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script type="text/javascript">
    $(function() {
        $('.shopping-item-wrapper').click(function() {
            $('.shopping-item-wrapper-selected').removeClass('shopping-item-wrapper-selected');
            $('#payment-id').val($(this).data('id'));
            $(this).addClass('shopping-item-wrapper-selected');
            $('.btn-payment').attr('disabled', false);
        });

        $('.btn-payment').click(function() {
            if ($('#payment-id').val() == '') {
                alert('@lang('errors.must_select_credits_package')');
            } else {
                $('#payment-method').val($(this).data('method'));
                $('#payment-form').submit();
            }
        });
    });
</script>
@endsection

@section('content-inner')
<div>
    <div class="col-sm-12" style="margin-bottom:20px;">
        <h4 style="text-align: right;">@lang('labels.credits'): {{ $_user->credits }}</h4>
    </div>
    @foreach($items as $item)
    <div class="col-sm-4">
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}">
            <h3>@lang('credits.item_name_' . $item->id)</h3>
            <h4>{{ $item->quantity }} @choice('countables.credits', $item->quantity)</h4>
            <span class="fa fa-soccer-ball-o"></span>
            <p>@lang('credits.item_description_' . $item->id)</p>
            <div class="shopping-item-price">{{ config('constants.CURRENCY') . ' ' . number_format($item->price, 2) }}</div>
        </div>
    </div>
    @endforeach
    <div class="clear">
</div>
<div style="margin-top: 20px;">
    <div class="col-xs-12" style="text-align: center;">
        <btn class="btn btn-primary btn-lg btn-payment" data-method="PP" disabled>@lang('labels.pay_using_paypal')</btn>
    </div>
    <form method="POST" id="payment-form" action="{{ route('payment.checkout') }}" >
        {{ csrf_field() }}
        <input type="hidden" id="payment-id" name="id" value="" />
        <input type="hidden" id="payment-method" name="method" value="">
    </form>
    <div class="clear">
</div>
@endsection
