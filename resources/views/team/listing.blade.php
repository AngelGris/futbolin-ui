@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function(){
    $('#dynteams').dataTable({
        "paging": false,
        "searching": false,
        "info": false
    });

    $('.sparring-toggle').click(function(event) {
        event.preventDefault();

        $('#dynsparrings-container').slideToggle(800, function() {
            if($(this).is(':visible')) {
                $('.sparring-toggle>span').removeClass('fa-plus-circle');
                $('.sparring-toggle>span').addClass('fa-minus-circle');
            } else {
                $('.sparring-toggle>span').removeClass('fa-minus-circle');
                $('.sparring-toggle>span').addClass('fa-plus-circle');
            }
        });
    });

    $('.teams-toggle').click(function(event) {
        event.preventDefault();

        $('#dynteams-container').slideToggle(800, function() {
            if($(this).is(':visible')) {
                $('.teams-toggle>span').removeClass('fa-plus-circle');
                $('.teams-toggle>span').addClass('fa-minus-circle');
            } else {
                $('.teams-toggle>span').removeClass('fa-minus-circle');
                $('.teams-toggle>span').addClass('fa-plus-circle');
            }
        });
    });

    $('.stats').click(function(event) {
        event.preventDefault();

        $('#modal-stats-versus-content').html('Cargando las estadísticas');
        $('#modal-stats-versus-loading').show();
        $('#modal-stats-versus').modal('show');

        $.ajax({
            'method' : 'GET',
            'url' : '/equipo/estadisticas/' + $(this).data('id'),
        }).done(function(data){
            $('#modal-stats-versus-loading').hide();
            $('#modal-stats-versus-content').html(data);
        });
    });

@if ($playable)
    $('.play').click(function(event) {
        event.preventDefault();

        $('#modal-playing-message').text('Se está disputando el encuentro, no dejen de alentar!');
        $('#modal-playing').modal({
            'backdrop' : 'static',
            'keyboard' : false
        });

        $.ajax({
            'method' : 'POST',
            'url' : '{{ route('match.play') }}',
            'dataType' : 'json',
            'data' : {rival : $(this).data('id'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            $('#modal-playing-message').text('Cargando el resultado...');
            loadResult(data.file);
        });
    });
@endif
});

function loadResult(fileName) {
    $.ajax({
        'method' : 'GET',
        'url' : '{{ route('match.load') }}',
        'data' : {file : fileName, _token : '{{ csrf_token() }}'},
    }).done(function(data){
        $('#modal-playing').modal('hide');
        $('#modal-match-result-content').html(data);
        $('#modal-match-result').modal('show');
    });
}
</script>
@endsection

@section('content-inner')
@if (!$playable)
<div class="alert alert-danger" role="alert">Para poder jugar partidos necesita completar su formación en la página de <a href="{{ route('strategy') }}">Estratégia</a></div>
@endif
<h3>Sparrings <a href="#" class="sparring-toggle"><span class="fa fa-plus-circle"></span></a></h3>
<p style="margin-bottom:10px;">Con los sparrings pueden jugar todos los partidos de entrenamiento que quieras, para probar las diferentes estrategias de tu equipo</p>
<div id="dynsparrings-container" style="display:none;">
    <table id="dynsparrings" class="table table-bordered responsive">
        <thead>
            <tr>
                <th style="width:100%">Equipo</th>
                <th>Formacion</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
                <th>Estad.</th>
                <th>Jugar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sparrings as $t)
            <tr>
                <td>{{ $t['name'] }}</td>
                <td align="center">{{ $t['strategy']['name'] }}</td>
                <td align="center">{{ $t['average'] }}</td>
                <td align="center"><a href="#" class="stats" data-id="{{ $t['id'] }}"><span class="fa fa-bar-chart" title="Estadísticas"></span></a></td>
                <td align="center">
                    @if ($playable)
                    <a href="#" class="play" data-id="{{ $t['id'] }}"><span class="fa fa-futbol-o" title="Entrenamiento"></span></a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<h3>Amistosos <a href="#" class="teams-toggle"><span class="fa fa-minus-circle"></span></a></h3>
<p style="margin-bottom:10px;">Sólo podrás jugar un amistoso con cada equipo cada 24 horas.</p>
<div id="dynteams-container">
    <table id="dynteams" class="table table-bordered responsive">
        <thead>
            <tr>
                <th style="width:50%">Equipo</th>
                <th style="width:50%">Entrenador</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
                <th>Estad.</th>
                <th>Jugar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teams as $t)
            <tr>
                <td>{{ $t['name'] }}</td>
                <td>{{ $t['user']['name'] }}</td>
                <td align="center">{{ $t['average'] }}</td>
                <td align="center">
                    @if ($t['id'] != $team['id'])
                    <a href="#" class="stats" data-id="{{ $t['id'] }}"><span class="fa fa-bar-chart" title="Estadísticas"></a>
                    @endif
                </td>
                <td align="center">
                    @if ($playable && $t['playable'] && $t['id'] != $team['id'])
                    <a href="#" class="play" data-id="{{ $t['id'] }}"><span class="fa fa-handshake-o" title="Amistoso"></span></a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="modal-stats-versus">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Estadísticas de enfrentamientos</h4>
            </div>
            <div class="modal-body">
                <p id="modal-stats-versus-content">Cargando las estadísticas</p>
                <div id="modal-stats-versus-loading" style="margin-top:20px;"><img src="{{ asset('img/loader.gif') }}" /></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-playing">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hora de partido</h4>
            </div>
            <div class="modal-body">
                <p id="modal-playing-message">Se está disputando el encuentro, no dejen de alentar!</p>
            </div>
            <div class="modal-footer">
                <img src="{{ asset('img/loader.gif') }}" />
            </div>
        </div>
    </div>
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
