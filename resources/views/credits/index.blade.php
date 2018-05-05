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
                alert('Debe seleccionar el paquete que quiere comprar');
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
    @foreach($items as $item)
    <div class="col-sm-4">
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}">
            <h3>{{ $item->name }}</h3>
            <h4>{{ $item->quantity }} Fúlbos</h4>
            <span class="fa fa-soccer-ball-o"></span>
            <p>{{ $item->description }}</p>
            <div class="shopping-item-price">{{ config('constants.CURRENCY') . ' ' . number_format($item->price, 2) }}</div>
        </div>
    </div>
    @endforeach
    <div class="clear">
</div>
<div style="margin-top: 20px;">
    <div class="col-xs-12" style="text-align: center;">
        <btn class="btn btn-primary btn-lg btn-payment" data-method="PP" disabled>Pagar con PayPal</btn>
    </div>
    <form method="POST" id="payment-form" action="{{ route('payment.checkout') }}" >
        {{ csrf_field() }}
        <input type="hidden" id="payment-id" name="id" value="" />
        <input type="hidden" id="payment-method" name="method" value="">
    </form>
    <div class="clear">
</div>
@endsection
