@extends('layouts.admin')

@section('javascript-inner')
<script type="text/javascript">
$(function(){
    $('.load-match').click(function() {
        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('file'), show_remaining : true, _token : '{{ csrf_token() }}'},
        }).done(function(data){
            refreshResultModal(data);
        });
    })
});
</script>
@endsection

@section('content-inner')
<div id="home-last-active-users" class="col-md-6 zebra">
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
<div id="home-last-active-users" class="col-md-6 zebra">
    <h3>Usuarios activos</h3>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_users_stats['day'] }}</div>
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
<div id="home-last-active-users" class="col-md-6 zebra">
    <h3>Últimos entrenamientos</h3>
    @foreach($last_trainnings as $team)
    <div class="col-xs-12">
        <div class="col-xs-6">{{ $team['name'] }}</div>
        <div class="col-xs-1">{{ $team['trainning_count'] }}</div>
        <div class="col-xs-4">{{ date('d/m/Y H:i:s', strtotime($team['last_trainning'])) }}</div>
        <div class="col-xs-1"><a href="{{ route('admin.team', ['domain' => getDomain(), 'id' => $team['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
    <a href="{{ route('admin.teams', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div id="home-last-active-users" class="col-md-6 zebra">
    <h3>Equipos entrenados</h3>
    <div class="col-xs-12">
        <div class="col-xs-10">Últimas 24 horas</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['day'] }}</div>
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
        <div class="col-xs-10">Total de usuarios</div>
        <div class="col-xs-2" style="text-align:right;">{{ $last_trainnings_stats['total'] }}</div>
    </div>
    <a href="{{ route('admin.users', getDomain()) }}" class="btn btn-primary" style="float:right;margin-top:10px;">Ver todos</a>
</div>
<div class="clear"></div>
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
