@extends('layouts.admin')

@section('javascript-inner')
<script src="{{ asset('js/jquery.flot.min.js') }}"></script>
<script type="text/javascript">
$(function(){
    $.plot($("#graph-players-energy"), [ {{ $players_energy }} ], {
        series: {
            stack: false,
            lines: { show: false, fill: false, steps: false },
            bars: { show: true, barWidth: 4.5 }
        },
        grid: { hoverable: true, clickable: true, borderColor: '#666', borderWidth: 2, labelMargin: 10 },
        colors: ["#666"]
    });

    $.plot($("#graph-teams-energy"), [ {{ $teams_energy }} ], {
        series: {
            stack: false,
            lines: { show: false, fill: false, steps: false },
            bars: { show: true, barWidth: 4.5 }
        },
        grid: { hoverable: true, clickable: true, borderColor: '#666', borderWidth: 2, labelMargin: 10 },
        colors: ["#666"]
    });

    $('.load-match').click(function() {
        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('file'), show_remaining : true, _token : '{{ csrf_token() }}'},
        }).done(function(data){
            refreshResultModal(data);
        });
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>Compras</h3>
    <div class="col-xs-6 no-zebra">Este mes: ${{ number_format($payments['this_month']->earnings, 2) }}</div>
    <div class="col-xs-6 no-zebra">Mes pasado: ${{ number_format($payments['last_month']->earnings, 2) }}</div>
    <div class="col-xs-12">
        <div class="col-xs-6">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['day']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['day']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['day']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Últimas 48 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['days']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['days']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['days']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Últimos 7 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['week']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['week']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['week']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Últimos 30 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['month']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['month']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['month']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Últimos 6 meses</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['semester']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['semester']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['semester']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Último año</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['year']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['year']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['year']->earnings, 2) }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Total de compras</div>
        <div class="col-xs-2" style="text-align:right;">{{ $payments['total']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['total']->total, 2) }}</div>
        <div class="col-xs-2" style="text-align:right;">${{ number_format($payments['total']->earnings, 2) }}</div>
    </div>
    <a href="{{ route('admin.payments', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Transacciones</h3>
    <div class="col-xs-6 no-zebra">Este mes: {{ $transactions['this_month']->count }} ({{ $transactions['this_month']->total or 0 }}) | Mes pasado: {{ $transactions['last_month']->count }} ({{ $transactions['last_month']->total or 0 }})</div>
    <div class="col-xs-6 no-zebra">Créditos en el sistema: {{ $total_credits }}</div>
    <div class="col-xs-12">
        <div class="col-xs-8">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['day']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['day']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Últimas 48 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['days']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['days']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Últimos 7 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['week']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['week']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Últimos 30 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['month']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['month']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Últimos 6 meses</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['semester']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['semester']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Último año</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['year']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['year']->total or 0 }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-8">Total transacciones</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['total']->count }}</div>
        <div class="col-xs-2" style="text-align:right;">{{ $transactions['total']->total or 0 }}</div>
    </div>
    <a href="{{ route('admin.transactions', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Últimos usuarios activos</h3>
    @foreach($last_users as $user)
    <div class="col-xs-12">
        <div class="col-xs-7">{{ $user['name'] }}</div>
        <div class="col-xs-4">{{ date('d/m/Y H:i:s', strtotime($user['last_activity'])) }}</div>
        <div class="col-xs-1"><a href="{{ route('admin.user', ['domain' => getDomain(), 'id' => $user['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    <a href="{{ route('admin.users', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Usuarios activos</h3>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['day'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 48 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['days'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 7 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['week'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 30 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['month'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 6 meses</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['semester'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Último año</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['year'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Total de usuarios</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['total'] }}</div>
    </div>
    <a href="{{ route('admin.users', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="clear"></div>
<div class="col-md-6 zebra">
    <h3>Últimos entrenamientos</h3>
    @foreach($last_trainnings as $team)
    <div class="col-xs-12">
        <div class="col-xs-6">{{ $team['name'] }}</div>
        <div class="col-xs-1">{{ $team['trainning_count'] }}</div>
        <div class="col-xs-4"{!! !$team['inTrainningSpam'] ? ' style="color:#f00;"' : ($team['trainable'] ? ' style="color:#0b0;"' : '') !!}>{{ date('d/m/Y H:i:s', strtotime($team['last_trainning'])) }}</div>
        <div class="col-xs-1"><a href="{{ route('admin.team', ['domain' => getDomain(), 'id' => $team['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    <a href="{{ route('admin.teams', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Equipos entrenados</h3>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['day'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 48 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['days'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 7 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['week'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 30 días</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['month'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimos 6 meses</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['semester'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Último año</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['year'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-10">Total de equipos</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['total'] }}</div>
    </div>
    <a href="{{ route('admin.teams', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="clear"></div>
<div class="col-md-6 zebra">
    <h3>Tarjetas Amarillas</h3>
    @foreach($cards_count as $card)
    <div class="col-xs-12">
        <div class="col-xs-11">{{ $card->cards }}</div>
        <div class="col-xs-1">{{ $card->cards_count }}</div>
    </div>
    @endforeach
    <a href="{{ route('admin.cards', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Suspensiones</h3>
    @foreach($suspensions as $suspension)
    <div class="col-xs-12">
        <div class="col-xs-11">{{ $suspension->name }}</div>
        <div class="col-xs-1">{{ $suspension->suspensions_count }}</div>
    </div>
    @endforeach
    <a href="{{ route('admin.suspensions', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="clear"></div>
<div class="col-md-6 zebra">
    <h3>Jugadores Lesionados</h3>
    @foreach($injured_players as $player)
    <div class="col-xs-12">
        <div class="col-xs-3">{{ $player->short_name }}</div>
        <div class="col-xs-2">{{ $player->team->short_name }}</div>
        <div class="col-xs-6">{{ $player->injury->name }}</div>
        <div class="col-xs-1"{!! $player->healed ? ' style="color:#0b0;"' : ' style="color:#f00;"' !!}>{{ $player->recovery }}</div>
    </div>
    @endforeach
    <a href="{{ route('admin.injuries', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="col-md-6 zebra">
    <h3>Tipos de lesiones</h3>
    @foreach($injury_types as $injury)
    <div class="col-xs-12">
        <div class="col-xs-11">{{ $injury->name }}</div>
        <div class="col-xs-1">{{ $injury->injuries_count }}</div>
    </div>
    @endforeach
</div>
<div class="clear"></div>
<div class="col-md-6">
    <h3>Energía de los jugadores</h3>
    <div class="widgetcontent">
        <div id="graph-players-energy" style="height:300px;"></div>
    </div>
</div>
<div class="col-md-6">
    <h3>Energía de los equipos</h3>
    <div class="widgetcontent">
        <div id="graph-teams-energy" style="height:300px;"></div>
    </div>
</div>
<div id="home-last-teams" class="col-md-6 zebra">
    <h3>Últimos equipos</h3>
    @foreach($last_teams as $team)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ date('d/m/Y H:i:s', strtotime($team['created_at'])) }}</div>
        <div class="col-xs-7">{{ $team['name'] }}</div>
        <div class="col-xs-1"><a href="{{ route('admin.team', ['domain' => getDomain(), 'id' => $team['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    <a href="{{ route('admin.teams', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div id="home-last-matches" class="col-md-6 zebra">
    <h3>Últimos partidos</h3>
    @foreach($last_matches as $match)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ date('d/m/Y H:i:s', strtotime($match['created_at'])) }}</div>
        <div class="col-xs-7">{{ $match['local']['name'] }} {{ $match['local_goals'] }} - {{ $match['visit_goals'] }} {{ $match['visit']['name'] }}</div>
        <div class="col-xs-1"><span class="fa fa-search load-match" data-file="{{ $match['logfile'] }}"></span></div>
    </div>
    @endforeach
    <a href="{{ route('admin.matches', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="modal fade" id="modal-match-result">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Resumen del partido</h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-match-result-content">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
