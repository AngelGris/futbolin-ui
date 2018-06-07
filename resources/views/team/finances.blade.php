@extends('layouts.inner')

@section('content-inner')
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
@endsection
