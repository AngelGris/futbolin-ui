<h4>Estadísticas contra {{ $rival['name'] }}</h4>
<table class="table table-bordered responsive">
    <thead>
        <tr>
            <th></th>
            <th>Jug</th>
            <th>Gan</th>
            <th>Emp</th>
            <th>Per</th>
            <th>GF</th>
            <th>GC</th>
            <th>DG</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Local</strong></td>
            <td align="right">{{ $matches[0][0] + $matches[0][1] + $matches[0][2] }}</td>
            <td align="right">{{ $matches[0][1] }}</td>
            <td align="right">{{ $matches[0][0] }}</td>
            <td align="right">{{ $matches[0][2] }}</td>
            <td align="right">{{ $goals[0][0] }}</td>
            <td align="right">{{ $goals[0][1] }}</td>
            <td align="right">{{ $goals[0][0] - $goals[0][1] }}</td>
        </th>
        <tr>
            <td><strong>Visitante</strong></td>
            <td align="right">{{ $matches[1][0] + $matches[1][1] + $matches[1][2] }}</td>
            <td align="right">{{ $matches[1][2] }}</td>
            <td align="right">{{ $matches[1][0] }}</td>
            <td align="right">{{ $matches[1][1] }}</td>
            <td align="right">{{ $goals[1][1] }}</td>
            <td align="right">{{ $goals[1][0] }}</td>
            <td align="right">{{ $goals[1][1] - $goals[1][0] }}</td>
        </th>
        <tr>
            <td><strong>Total</strong></td>
            <td align="right"><strong>{{ $matches[0][0] + $matches[0][1] + $matches[0][2] + $matches[1][0] + $matches[1][1] + $matches[1][2] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][1] + $matches[1][2] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][0] + $matches[1][0] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][2] + $matches[1][1] }}</strong></td>
            <td align="right"><strong>{{ $goals[0][0] + $goals[1][1] }}</strong></td>
            <td align="right"><strong>{{ $goals[0][1] + $goals[1][0] }}</strong></td>
            <td align="right"><strong>{{ ($goals[0][0] + $goals[1][1]) - ($goals[0][1] + $goals[1][0]) }}</strong></td>
        </th>
    </tbody>
</table>
@if (!empty($last_matches))
<h4>Últimos partidos</h4>
<table class="table table-bordered responsive">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Condición</th>
            <th>Resultado</th>
            <th>Detalles</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($last_matches as $match)
        <tr>
            <td>{{ $match['date'] }}</td>
            <td>{{ $match['condition'] }}</td>
            <td align="center">{!! $match['score'] !!}</td>
            <td align="center"><a href="#" class="view-match" onClick="loadResult('{{ $match['logfile'] }}'); return false;"><span class="fa fa-search"></span></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<div class="clear"></div>