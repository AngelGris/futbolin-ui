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
