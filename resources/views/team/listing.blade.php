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

@if ($playable)
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
@endif
</script>
@endsection

@section('content-inner')
@if (!$playable)
<div class="alert alert-danger" role="alert">Para poder jugar partidos necesita completar su formación en la página de <a href="{{ route('strategy') }}">Estratégia</a></div>
@endif
<h3>Sparrings</h3>
<table id="dynsparrings" class="table table-bordered responsive">
    <thead>
        <tr>
            <th style="width:100%">Equipo</th>
            <th>Formacion</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
            <th>Jugar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sparrings as $t)
        <tr>
            <td>{{ $t['name'] }}</td>
            <td align="center">{{ $t['strategy']['name'] }}</td>
            <td align="center">{{ $t['average'] }}</td>
            <td align="center">
                @if ($playable)
                <a href="#" class="play" data-id="{{ $t['id'] }}"><span class="fa fa-futbol-o" title="Entrenamiento"></span></a>
                @endif
            </td>
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
                @if ($playable && $t['id'] != $team['id'])
                <a href="#" class="play" data-id="{{ $t['id'] }}"><span class="fa fa-handshake-o" title="Amistoso"></span></a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
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
