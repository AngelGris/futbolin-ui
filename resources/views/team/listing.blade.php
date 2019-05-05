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

        $('#modal-stats-versus-content').html('@lang('lables.loading_statistics')');
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

        $('#modal-playing-message').text('@lang('messages.playing_match')');
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
            $('#modal-playing-message').text('@lang('labels.loading_result')');
            $('#btn-play-' + data.id).hide();
            $('#span-play-' + data.id).show();
            loadResult(data.file);
        });
    });
    @endif
});

function loadResult(fileName) {
    $('#modal-match-loading').modal('show');

    $.ajax({
        'method' : 'GET',
        'url' : '{{ route('match.load') }}',
        'data' : {file : fileName, show_remaining : true, _token : '{{ csrf_token() }}'},
    }).done(function(data){
        $('#modal-match-loading').on('hidden.bs.modal', function () {
            refreshResultModal(data);
        }).modal('hide');
    });
}
</script>
@endsection

@section('content-inner')
@if (!$playable)
<div class="alert alert-danger" role="alert">@lang('messages.complete_lineup', ['url' => route('strategy')])</div>
@endif
<h3>@lang('labels.sparrings') <a href="#" class="sparring-toggle"><span class="fa fa-plus-circle"></span></a></h3>
<p style="margin-bottom:10px;">@lang('messages.sparrings_description')</p>
<div id="dynsparrings-container" style="display:none;">
    <table id="dynsparrings" class="table table-bordered responsive">
        <thead>
            <tr>
                <th style="width:100%">@lang('labels.team')</th>
                <th>@lang('labels.formation')</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.average')">@lang('attributes.average_short')</span></th>
                <th>@lang('labels.statistics_short')</th>
                <th>@lang('labels.play')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sparrings as $team)
            <tr>
                <td>{{ $team['name'] }}</td>
                <td align="center">{{ $team['strategy']['name'] }}</td>
                <td align="center">{{ $team['average'] }}</td>
                <td align="center"><a href="#" class="stats" data-id="{{ $team['id'] }}"><span class="fa fa-bar-chart" title="@lang('labels.statistics')"></span></a></td>
                <td align="center">
                    @if ($playable)
                    <a href="#" class="play" data-id="{{ $team['id'] }}"><span class="fa fa-futbol-o" title="@lang('labels.training')"></span></a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<h3>@lang('labels.friendlies') <a href="#" class="teams-toggle"><span class="fa fa-minus-circle"></span></a></h3>
<p style="margin-bottom:10px;">@lang('messages.friendlies_description')</p>
<div id="dynteams-container">
    <table id="dynteams" class="table table-bordered responsive">
        <thead>
            <tr>
                <th style="width:50%">@lang('labels.team')</th>
                <th style="width:50%">@lang('labels.trainer')</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.average')">@lang('attributes.average_short')</span></th>
                <th>@lang('labels.statistics_short')</th>
                <th>@lang('labels.play')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teams as $team)
            <tr>
                <td><a href="{{ route('team.show', [$team['id']]) }}">{{ $team['name'] }}</a></td>
                <td>{{ $team['user']['name'] }}</td>
                <td align="center">{{ $team['average'] }}</td>
                <td align="center">
                    @if ($team['id'] != $_team['id'])
                    <a href="#" class="stats" data-id="{{ $team['id'] }}"><span class="fa fa-bar-chart" title="@lang('labels.statistics')"></a>
                    @endif
                </td>
                <td align="center">
                    @if ($playable && $team['playable'] && $team['id'] != $_team['id'])
                    <a href="#" id="btn-play-{{ $team['id'] }}" class="play" data-id="{{ $team['id'] }}"{!! !empty($team->played) ? ' style="display:none;"' : '' !!}><span class="fa fa-handshake-o" title="@lang('labels.friendlies')"></span></a>
                    <span id="span-play-{{ $team['id'] }}" data-id="{{ $team['id'] }}" {!! empty($team->played) ? ' style="display:none;"' : '' !!}>{{ !empty($team->played) ? $team->played : '24 h' }}</span>
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
                <h4 class="modal-title">@lang('labels.match_statistics')</h4>
            </div>
            <div class="modal-body">
                <p id="modal-stats-versus-content">@lang('labels.loading_statistics')</p>
                <div id="modal-stats-versus-loading" style="margin-top:20px;"><img src="{{ asset('img/loader.gif') }}" /></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
            </div>
        </div>
    </div>
</div>
@if ($playable)
@include('modules.modals.playfriendly')
@endif
@endsection
