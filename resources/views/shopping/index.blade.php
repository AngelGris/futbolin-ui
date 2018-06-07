@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/shopping.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script type="text/javascript">
    $('.shopping-item-wrapper').click(function() {
        $('#item-id').val($(this).data('id'));
        $('.modal-buy-item').text($(this).data('item'));
        $('#modal-buy-price').text($(this).data('price'));
    });

    $('#buy-item').click(function() {
        $('#frm-buy').submit();
    });
</script>
@endsection

@section('content-inner')
<div>
    <div class="col-sm-12" style="margin-bottom:20px;">
        <h4 style="text-align: right;">Fúlbos: {{ $_user->credits }} <a href="{{ route('shopping.credits') }}" class="btn btn-xs btn-primary">Comprar Fúlbos</a></h4>
    </div>
    @foreach($items as $item)
    <div class="col-sm-4">
        @if ($item->id == 3 && $_team->trainer >= \Carbon\Carbon::now())
        <div class="shopping-item-wrapper-disabled">
        @elseif ($_user->credits >= $item->price)
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-buy-confirm" data-id="{{ $item->id }}" data-item="{{ $item->name }}" data-price="{{ $item->price }}">
        @else
        <div class="shopping-item-wrapper" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-buy-denied" data-id="{{ $item->id }}" data-item="{{ $item->name }}" data-price="{{ $item->price }}">
        @endif
            <h3>{{ $item->name }}</h3>
            <span class="{{ $item->icon }}"></span>
            <p>{{ $item->description }}</p>
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
                <h4 class="modal-title">No alcanzan los Fúlbos</h4>
            </div>
            <div class="modal-body">
                <p>No tienes suficientes Fúlbos para realizar la compra.</p>
                <p>¿Quieres comprar más Fúlbos?</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-primary">Comprar Fúlbos</a>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-buy-confirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmar compra</h4>
            </div>
            <div class="modal-body">
                <p>¿Quieres comprar <span class="modal-buy-item"></span> por <span id="modal-buy-price"></span>?</p>
            </div>
            <div class="modal-footer">
                <button id="buy-item" class="btn btn-primary">Comprar paquete "<span class="modal-buy-item"></span>"</button>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<form id="frm-buy" method="post" action="{{ route('shopping.buy') }}">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="item-id">
</form>
@endsection
