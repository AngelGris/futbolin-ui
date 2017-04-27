@extends('layouts.inner')

@section('content-inner')
<h3>Sparrings</h3>
<table id="dynsparrings" class="table table-bordered responsive">
    <thead>
        <tr>
            <th style="width:100%">Equipo</th>
            <th>Formacion</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sparrings as $team)
        <tr>
            <td>{{ $team['name'] }}</td>
            <td align="center">{{ $team['strategy']['name'] }}</td>
            <td align="center">{{ $team['average'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3>Amistosos</h3>
<table id="dynteams" class="table table-bordered responsive">
    <thead>
        <tr>
            <th style="width:50%">Equipo</th>
            <th style="width:50%">Entrenador</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($teams as $team)
        <tr>
            <td>{{ $team['name'] }}</td>
            <td>{{ $team['user']['name'] }}</td>
            <td align="center">{{ $team['average'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
