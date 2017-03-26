@extends('layouts.inner')

@section('content-inner')
<table id="dyntable" class="table table-bordered responsive">
    <thead>
        <tr>
            <th class="head0" align="right">#</th>
            <th class="head1" style="width:50%">Nombre</th>
            <th class="head0">POS</th>
            <th class="head1">ARQ</th>
            <th class="head0">DEF</th>
            <th class="head1">GAM</th>
            <th class="head0">CAB</th>
            <th class="head1">SAL</th>
            <th class="head0">PAS</th>
            <th class="head1">PRE</th>
            <th class="head0">VEL</th>
            <th class="head1">FUE</th>
            <th class="head0">QUI</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($players as $player)
        <tr>
            <td align="right">{{ $player['number'] }}</td>
            <td>{{ $player['first_name'] . ' ' . $player['last_name'] }}</td>
            <td align="center">{{ $player['position'] }}</td>
            <td align="right">{{ $player['goalkeeping'] }}</td>
            <td align="right">{{ $player['defending'] }}</td>
            <td align="right">{{ $player['dribbling'] }}</td>
            <td align="right">{{ $player['heading'] }}</td>
            <td align="right">{{ $player['jumping'] }}</td>
            <td align="right">{{ $player['passing'] }}</td>
            <td align="right">{{ $player['precision'] }}</td>
            <td align="right">{{ $player['speed'] }}</td>
            <td align="right">{{ $player['strength'] }}</td>
            <td align="right">{{ $player['tackling'] }}</td>
        @endforeach
    </tbody>
</table>
@endsection
