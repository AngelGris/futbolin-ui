@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
    $(function() {
        $('#modal-sell-credits-credits').change(function() {
            var value = $(this).val() * {{ config('constants.CREDITS_SELL_VALUE') }}
            $('#modal-sell-credits-value').text(formatCurrency(value));
        });
    });
</script>
@endsection

@section('content-inner')
<div class="col-sm-12" style="margin-bottom:20px;">
    <h4 style="text-align: right;">Fúlbos: {{ $_user->credits }} <a href="#" data-toggle="modal" data-target="#modal-sell-credits" class="btn btn-xs btn-primary">Vender Fúlbos</a></h4>
</div>
@if (count($movements) == 0)
<h3>No hay movimientos</h3>
@else
{{ $movements->links() }}
<table id="dyntable" class="table table-bordered table-players responsive">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($movements as $movement)
        <tr>
            <td style="text-align: right;">{{ $movement->created_at->format('d/m/Y') }}</td>
            <td>{{ $movement->description }}</td>
            <td style="text-align: right;">{!! formatCurrency($movement->amount) !!}</td>
            <td style="text-align: right;">{!! formatCurrency($movement->balance) !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $movements->links() }}
@endif
<div class="modal fade" id="modal-sell-credits" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Vender Fúlbos</h4>
            </div>
            @if ($_user->credits == 0)
            <div class="modal-body">
                <p>No tienes Fúlbos para vender</p>
                <p>¿Quieres comprar Fúlbos?</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-xs btn-primary">Comprar Fúlbos</a>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
            @else
            <form method="post" action="{{ route('shopping.buy') }}">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="6">
                <div class="modal-body">
                    <p>¿Cuántos Fúlbos quieres vender?</p>
                    <select name="credits" id="modal-sell-credits-credits">
                        @for($i = 1; $i <= $_user->credits; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    Fúlbos = <span id="modal-sell-credits-value">{{ formatCurrency(config('constants.CREDITS_SELL_VALUE')) }}</span>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Vender Fúlbos" />
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection