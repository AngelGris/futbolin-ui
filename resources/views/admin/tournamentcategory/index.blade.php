<div class="col-xs-12">
    <ul class="nav nav-tabs">
        <li id="modal-category-tab-positions" class="active" onClick="changeTab(1);"><a href="#">Posiciones</a></li>
        <li id="modal-category-tab-matches" onClick="changeTab(2);"><a href="#">Partidos</a></li>
    </ul>
</div>
<div id="modal-category-positions" class="col-xs-12">
    <h4>Posiciones</h4>
    <table class="table table-bordered responsive">
        <thead>
            <tr>
                <th>Pos</th>
                <th>Equipo</th>
                <th>PTS</th>
                <th>PJ</th>
                <th>PG</th>
                <th>PE</th>
                <th>PP</th>
                <th>GF</th>
                <th>GC</th>
                <th>DG</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($category['positions'] as $position)
            <tr>
                <td style="padding-right:5px;text-align:right;">{{ $position['position'] }}</td>
                <td>{{ $position['team']['name'] }}</td>
                <td style="text-align:right;">{{ $position['points'] }}</td>
                <td style="text-align:right;">{{ $position['played'] }}</td>
                <td style="text-align:right;">{{ $position['won'] }}</td>
                <td style="text-align:right;">{{ $position['tied'] }}</td>
                <td style="text-align:right;">{{ $position['lost'] }}</td>
                <td style="text-align:right;">{{ $position['goals_favor'] }}</td>
                <td style="text-align:right;">{{ $position['goals_against'] }}</td>
                <td style="text-align:right;">{{ $position['goals_difference'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="modal-category-matches" class="col-xs-12" style="display:none;">
    <h4>Partidos</h4>
    Fecha: <select onChange="changeRound(this);">
    @for ($i = 1; $i <= count($category['rounds']); $i++)
        <option value="{{ $i }}">{{ $i }}</option>
    @endfor
    </select>
    @foreach($category['rounds'] as $round)
    <div class="col-xs-12 modal-category-rounds" id="modal-category-round-{{ $round['number'] }}" {!! ($round['number'] > 1) ? ' style="display:none;"' : '' !!}>
        <h5>Fecha {{ $round['number'] }}</h5>
        <table class="table table-bordered responsive">
            <thead>
                <tr>
                    <th colspan="2">Local</th>
                    <th colspan="2">Visitante</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($round['matches'] as $match)
                <tr>
                    <td style="padding-right:5px;text-align:right;">{{ $match['local']['name'] }}</td>
                    <td style="text-align:center;">{{ $match['local_goals'] }}</td>
                    <td style="text-align:center;">{{ $match['visit_goals'] }}</td>
                    <td style="padding-left:5px;">{{ $match['visit']['name'] }}</td>
                    <td style="text-align:center;">
                        @if ($match['match_id'])
                        <a href="#" onClick="loadResult('{{ $match['logfile'] }}'); return false;"><span class="fa fa-search"></span></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>
<div class="clear"></div>