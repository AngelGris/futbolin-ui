@extends('layouts.inner')

@section('styles-inner')
@if (Session::has('show_walkthrough'))
<link rel="stylesheet" href="{{ asset('css/jquery.walkthrough.min.css') }}" type="text/css" />
@endif
@endsection

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
@if (Session::has('show_walkthrough'))
<script src="{{ asset('js/jquery.walkthrough.min.js') }}"></script>
@endif
<script type="text/javascript">
$(function() {
    var fieldTimeout;
    var strategies = {!! json_encode($strategies) !!};
    var players = {!! json_encode($players) !!};

    @if (Session::has('show_walkthrough'))
    // Set up tour
    $('body').pagewalkthrough({
        name: 'welcome',
        steps: [{
           popup: {
                content: '<p>¡Bienvenido/a {{ $_user['first_name'] }}!</p><br /><p>Voy a enseñarte rápidamente cómo funciona el juego para que puedas llevar a tu equipo a lo más alto.</p><p>¿Estamos listos?</p>',
                type: 'modal',
           },
        }, {
            popup: {
                content: '<p>Esta es la página de estrategias y aquí puedes decidir cómo parar a tu equipo en la cancha y cuáles jugadores serán titulares, y cuáles suplentes.',
                type: 'modal'
            }
        }, {
            wrapper: '#btn-formation',
            popup: {
                content: '<p>Presionando en la formación puedes ver las diferentes opciones de formación que tienes y elegir la que creas mejor para tu equipo.</p>',
                type: 'tooltip',
                position: 'bottom',
                offsetHorizontal: -100,
                offsetArrowHorizontal: 50,
            }
        }, {
            wrapper: '#dyntable',
            popup: {
                content: '<p>Elige los jugadores que quieres que jueguen y arrástralos hasta la posición que quieres que ocupen, o hasta el banco de suplentes.</p>',
                type: 'tooltip',
                position: 'top',
                onEnter: $.noop,
            },
            onEnter: function() {
                $('html, body').animate({
                    scrollTop: $("#dyntable").offset().top - 500,
                }, 2000);
            },
        }, {
            wrapper: $('a[href="{{ route('teams') }}"]').is(':visible') ? 'a[href="{{ route('teams') }}"]' : 'button.navbar-toggle',
            popup: {
                content: '<p>Cuando tu equipo esté listo puedes ir a la página de amistosos para jugar contra sparrings y otros equipos para ver si tu estrategia es buena.</p>',
                type: 'tooltip',
                position: $('a[href="{{ route('teams') }}"]').is(':visible') ? 'right' : 'bottom',
                offsetHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : -150,
                offsetArrowHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : 100,
            },
            onEnter: function() {
                $('html, body').animate({
                    scrollTop: 0,
                }, 2000);
            }
        }, {
            popup: {
                content: '<p>En cada partido de campeonato que juegues tus jugadores ganaran experiencia, y al llegar a 100 puntos de experiencia mejoraran sus atributos. (partidos con sparring y amistosos no suman experiencia)</p>',
                type: 'modal'
            }
        }, {
            wrapper: $('button.trainning-button').is(':visible') ? 'button.trainning-button' : 'li.trainning',
            popup: {
                content: '<p>También puedes ayudar a tus jugadores a mejorar haciéndolos entrenar todos los días presionando en el botón "Entrenar"</p>',
                type: 'tooltip',
                position: 'bottom',
                offsetHorizontal: $('button.trainning-button').is(':visible') ? -50 : 100,
                offsetArrowHorizontal: $('button.trainning-button').is(':visible') ? 0 : -150,
            }
        }, {
            popup: {
                content: '<p>Ahora sí puedes ponerte a trabajar en tu equipo.</p><p>¡Buena suerte!</p>',
                type: 'modal'
            }
        }],
        buttons: {
            jpwNext: {
                i18n: 'Próximo &rarr;',
            },
            jpwPrevious: {
                i18n: '&larr; Anterior',
            },
            jpwFinish: {
                i18n: 'Finalizar &#10004;',
            },
            jpwClose: {
                i18n: 'Cerrar',
            }
        }
    });

    $('body').pagewalkthrough('show');
    @endif

    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "sScrollY": "405px"
    });

    $('.change-formation').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('href');
        $('#strategy-name').text(strategies[id]['name']);

        $.ajax({
            'method' : 'POST',
            'url' : '{{ route('team.strategy') }}',
            'data' : {strategy : id, _token : '{{ csrf_token() }}'},
        });

        for (var i = 1; i <= 11; i++) {
            if (i < 10) {
                div = '#player-0' + i;
            } else {
                div = '#player-' + i;
            }

            $(div).animate({
                left: strategies[id][i]['left'] + '%',
                top: strategies[id][i]['top'] + '%'
            }, 500);
        };
    });

    $('.rollover-player').mouseover(function() {
        var id = parseInt($(this).data('player-id'));
        $('#player-header').html(players[id]['number'] + '. ' + players[id]['name']);
        $('#player-info .widgetcontent').html('<div class="col-md-6"><strong>EDAD: ' + players[id]['age'] + '</strong><br>ARQ: ' + players[id]['goalkeeping'] + '<br>GAM: ' + players[id]['dribbling'] + '<br>SAL: ' + players[id]['jumping'] + '<br>PRE: ' + players[id]['precision'] + '<br>FUE: ' + players[id]['strength'] + '<br>EXP: ' + players[id]['experience'] + '</div><div class="col-md-6"><strong>MED: ' + players[id]['average'] + '</strong><br>DEF: ' + players[id]['defending'] + '<br>CAB: ' + players[id]['heading'] + '<br>PAS: ' + players[id]['passing'] + '<br>VEL: ' + players[id]['speed'] + '<br>QUI: ' + players[id]['tackling'] + '<br>ENE: ' + players[id]['stamina'] + '</div>');
        $('#player-info').stop().fadeTo(0, 1);
    }).mouseleave(function() {
        $('#player-info').fadeOut(500);
    });

    $('#strategy-container').mouseover(function() {
        clearTimeout(fieldTimeout);
        $('.strategy-players-container').css('transform','none');
        $('.strategy-players-container').css('-webkit-transform','none');
    }).mouseleave(function() {
        fieldTimeout = window.setTimeout(function() {
            $('.strategy-players-container').css('transform','matrix3d(1,0,0.00,0,0.00,1.12,0.98,-0.0009,0,-0.98,0.17,0,0,0,0,1)');
            $('.strategy-players-container').css('-webkit-transform','matrix3d(1,0,0.00,0,0.00,1.12,0.98,-0.0009,0,-0.98,0.17,0,0,0,0,1)');
        }, 5000);
    });

    $('.player-draggable').draggable({
        cursorAt: { left: 18, top: 18 },
        appendTo: 'body',
        helper: function() {
            var id = parseInt($(this).data('player-id'));
            return '<div class="player-helper ' + players[id]['position'].toLowerCase() + (($(this).parent().hasClass('strategy-players-container')) ? ' player-helper-div' : '') + '">' + players[id]['number'] + '</div>';
        },
        start: function() {
            clearTimeout(fieldTimeout);
            $('.strategy-players-container').css('transform','none');
            $('.strategy-players-container').css('-webkit-transform','none');
        },
        stop: function() {
            fieldTimeout = window.setTimeout(function() {
                $('.strategy-players-container').css('transform','matrix3d(1,0,0.00,0,0.00,1.12,0.98,-0.0009,0,-0.98,0.17,0,0,0,0,1)');
                $('.strategy-players-container').css('-webkit-transform','matrix3d(1,0,0.00,0,0.00,1.12,0.98,-0.0009,0,-0.98,0.17,0,0,0,0,1)');
            }, 5000);
        }
    });

    $('.player-droppable').droppable({
        drop: function(event, ui) {
            var id = parseInt(ui.draggable.data('player-id'));
            var old_id = parseInt($(this).data('player-id'));

            $(this).data('player-id', id);
            var content = players[id]['number'] + '<div class="status">';
            if (players[id]['retiring']) {
                content += '<span class="fa fa-user-times" style="color:#f00;"></span>';
            }
            if (players[id]['stamina'] < 50) {
                content += '<span class="fa fa-arrow-down" style="color:#f00;"></span>';
            }
            content += '<div>';
            $(this).html(content);
            $(this).removeClass('arq');
            $(this).removeClass('def');
            $(this).removeClass('med');
            $(this).removeClass('ata');
            $(this).addClass(players[id]['position'].toLowerCase());
            if (ui.draggable[0].tagName == 'DIV') {
                ui.draggable.data('player-id', old_id);
                ui.draggable.removeClass('arq');
                ui.draggable.removeClass('def');
                ui.draggable.removeClass('med');
                ui.draggable.removeClass('ata');
                if (old_id > 0) {
                    var content = players[old_id]['number'] + '<div class="status">';
                    if (players[old_id]['retiring']) {
                        content += '<span class="fa fa-user-times" style="color:#f00;"></span>'
                    }
                    if (players[id]['stamina'] < 50) {
                        content += '<span class="fa fa-arrow-down" style="color:#f00;"></span>';
                    }
                    content += '</div>';
                    ui.draggable.html(content);
                    ui.draggable.addClass(players[old_id]['position'].toLowerCase());
                } else {
                    ui.draggable.text('');
                }
            } else {
                $('tr.player-draggable#subs-' + (id < 10 ? '0' : '') + id).hide();
                $('tr.player-draggable#subs-' + (old_id < 10 ? '0' : '') + old_id).show();
            }

            var formation = {};
            for (i = 1; i <= 18; i++) {
                formation[i - 1] = $('#player-' + (i < 10 ? '0' : '') + i).data('player-id');
            }

            $.ajax({
                'method' : 'POST',
                'url' : '{{ route('team.formation') }}',
                'data' : {formation : formation, _token : '{{ csrf_token() }}'},
            });
        }
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-md-5">
    <div class="widgetbox">
        <div class="headtitle">
            <div class="btn-group">
                <button id="btn-formation" data-toggle="dropdown" class="btn dropdown-toggle" data-target="#">
                    <span id="strategy-name">{{ $strategies[$strategy]['name'] }}</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @foreach ($strategies as $str)
                    <li><a href="{{ $str['id'] }}" class="change-formation">{{ $str['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <h4 class="widgettitle">Formación</h4>
        </div>
        <div class="widgetcontent" style="padding-top:60px;">
            <div id="strategy-container">
                <img src="{{ asset('img/field-large-3d.png') }}">
                <div class="strategy-players-container">
                    @for ($i = 1; $i <= 11; $i++)
                        @if (empty($formation[$i - 1]))
                        <div id="player-{{ sprintf('%02d', $i) }}" data-player-id="0" class="player-container player-draggable player-droppable rollover-player" style="left:{{ $strategies[$strategy][$i]['left'] }}%;top:{{ $strategies[$strategy][$i]['top'] }}%;"></div>
                        @else
                        <div id="player-{{ sprintf('%02d', $i) }}" data-player-id="{{ isset($formation[$i - 1]) ? $formation[$i - 1] : 0 }}" class="player-container player-draggable player-droppable rollover-player {{ strtolower($players[$formation[$i - 1]]['position']) }}" style="left:{{ $strategies[$strategy][$i]['left'] }}%;top:{{ $strategies[$strategy][$i]['top'] }}%;">
                            {{ $players[$formation[$i - 1]]['number'] }}
                            <div class="status">
                                @if ($players[$formation[$i - 1]]['retiring'])
                                <span class="fa fa-user-times" style="color:#f00;"></span>
                                @endif
                                @if ($players[$formation[$i - 1]]['stamina'] < 50)
                                <span class="fa fa-arrow-down" style="color:#f00;"></span>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endfor
                </div>
            </div>
            <h3 style="font-weight:bold;margin-top:20px;">Suplentes</h3>
            @for ($i = 12; $i <= 18; $i++)
                @if (empty($formation[$i - 1]))
                <div id="player-{{ $i }}" data-player-id="0" class="player-container player-draggable player-droppable rollover-player"></div>
                @else
                <div id="player-{{ $i }}" data-player-id="{{ $formation[$i - 1] }}" class="player-container player-draggable player-droppable rollover-player {{ strtolower($players[$formation[$i - 1]]['position']) }}">
                    {{ $players[$formation[$i - 1]]['number'] }}
                    <div class="status">
                        @if ($players[$formation[$i - 1]]['retiring'])
                        <span class="fa fa-user-times" style="color:#f00;"></span>
                        @endif
                        @if ($players[$formation[$i - 1]]['stamina'] < 50)
                        <span class="fa fa-arrow-down" style="color:#f00;"></span>
                        @endif
                    </div>
                </div>
                @endif
            @endfor
        </div>
    </div>
</div>
<div class="col-md-4">
    <table id="dyntable" class="table table-bordered responsive">
        <thead>
            <tr>
                <th>#</th>
                <th style="width:50%">Nombre</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($players as $player)
            <tr id="subs-{{ sprintf('%02d', $player['id']) }}" data-player-id="{{ $player['id'] }}" class="rollover-player player-draggable {{ strtolower($player['position']) }}" {!! !empty($formation) ? (in_array($player['id'], $formation) ? 'style="display:none;"' : '') : '' !!}>
                <td align="right" class="player-number">{{ $player['number'] }}</td>
                <td class="player-name">
                    {{ $player['short_name'] }}
                    @if ($player->retiring)
                    <span class="fa fa-user-times" style="color:#f00;"></span>
                    @endif
                </td>
                <td align="center" class="player-position"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $player['position_long'] }}">{{ $player['position'] }}</span></td>
                <td align="right" class="player-average"><strong>{{ $player['average'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="col-md-3">
    <div id="player-info" class="widgetbox col-md-12" style="display:none;">
        <div class="headtitle">
            <h4 id="player-header" class="widgettitle"></h4>
        </div>
        <div class="widgetcontent"></div>
    </div>
</div>
@endsection
