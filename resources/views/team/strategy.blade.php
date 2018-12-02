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
                content: '@lang('walkthrough.popup_1', ['name' => $_user['first_name']])',
                type: 'modal',
           },
        }, {
            popup: {
                content: '@lang('walkthrough.popup_2')',
                type: 'modal'
            }
        }, {
            wrapper: '#btn-formation',
            popup: {
                content: '@lang('walkthrough.popup_3')',
                type: 'tooltip',
                position: 'bottom',
                offsetHorizontal: -100,
                offsetArrowHorizontal: 50,
            }
        }, {
            wrapper: '#dyntable',
            popup: {
                content: '@lang('walkthrough.popup_4')',
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
            wrapper: $('a[href="{{ route('finances') }}"]').is(':visible') ? 'a[href="{{ route('finances') }}"]' : 'button.navbar-toggle',
            popup: {
                content: '@lang('walkthrough.popup_5')',
                type: 'tooltip',
                position: $('a[href="{{ route('finances') }}"]').is(':visible') ? 'right' : 'bottom',
                offsetHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : -150,
                offsetArrowHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : 100,
            },
            onEnter: function() {
                $('html, body').animate({
                    scrollTop: 0,
                }, 2000);
            }
        }, {
            wrapper: $('a[href="{{ route('market') }}"]').is(':visible') ? 'a[href="{{ route('market') }}"]' : 'button.navbar-toggle',
            popup: {
                content: '@lang('walkthrough.popup_6')',
                type: 'tooltip',
                position: $('a[href="{{ route('market') }}"]').is(':visible') ? 'right' : 'bottom',
                offsetHorizontal: $('a[href="{{ route('market') }}"]').is(':visible') ? 0 : -150,
                offsetArrowHorizontal: $('a[href="{{ route('market') }}"]').is(':visible') ? 0 : 100,
            }
        }, {
            wrapper: $('a[href="{{ route('teams') }}"]').is(':visible') ? 'a[href="{{ route('teams') }}"]' : 'button.navbar-toggle',
            popup: {
                content: '@lang('walkthrough.popup_7')',
                type: 'tooltip',
                position: $('a[href="{{ route('teams') }}"]').is(':visible') ? 'right' : 'bottom',
                offsetHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : -150,
                offsetArrowHorizontal: $('a[href="{{ route('teams') }}"]').is(':visible') ? 0 : 100,
            }
        }, {
            wrapper: $('a[href="{{ route('shopping') }}"]').is(':visible') ? 'a[href="{{ route('shopping') }}"]' : 'button.navbar-toggle',
            popup: {
                content: '@lang('walkthrough.popup_8')',
                type: 'tooltip',
                position: $('a[href="{{ route('shopping') }}"]').is(':visible') ? 'right' : 'bottom',
                offsetHorizontal: $('a[href="{{ route('shopping') }}"]').is(':visible') ? 0 : -150,
                offsetArrowHorizontal: $('a[href="{{ route('shopping') }}"]').is(':visible') ? 0 : 100,
            }
        }, {
            popup: {
                content: '@lang('walkthrough.popup_9')',
                type: 'modal'
            }
        }, {
            wrapper: $('button.trainning-button').is(':visible') ? 'button.trainning-button' : 'li.trainning',
            popup: {
                content: '@lang('walkthrough.popup_10')',
                type: 'tooltip',
                position: 'bottom',
                offsetHorizontal: $('button.trainning-button').is(':visible') ? -50 : 100,
                offsetArrowHorizontal: $('button.trainning-button').is(':visible') ? 0 : -150,
            }
        }, {
            popup: {
                content: '@lang('walkthrough.popup_11')',
                type: 'modal'
            }
        }],
        buttons: {
            jpwNext: {
                i18n: '@lang('labels.next') &rarr;',
            },
            jpwPrevious: {
                i18n: '&larr; @lang('labels.previous')',
            },
            jpwFinish: {
                i18n: '@lang('labels.finish') &#10004;',
            },
            jpwClose: {
                i18n: '@lang('labels.close')',
            }
        }
    });

    $('body').pagewalkthrough('show');
    @endif

    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false,
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
        $('#player-info .widgetcontent').html('<div class="col-xs-6"><strong>@lang('attributes.age_short'): ' + players[id]['age'] + '</strong><br>@lang('attributes.goalkeeping_short'): ' + players[id]['goalkeeping'] + '<br>@lang('attributes.dribbling_short'): ' + players[id]['dribbling'] + '<br>@lang('attributes.jumping_short'): ' + players[id]['jumping'] + '<br>@lang('attributes.precision_short'): ' + players[id]['precision'] + '<br>@lang('attributes.strength_short'): ' + players[id]['strength'] + '<br>@lang('attributes.experience_short'): ' + players[id]['experience'] + '</div><div class="col-xs-6"><strong>@lang('attributes.average_short'): ' + players[id]['average'] + '</strong><br>@lang('attributes.defending_short'): ' + players[id]['defending'] + '<br>@lang('attributes.heading_short'): ' + players[id]['heading'] + '<br>@lang('attributes.passing_short'): ' + players[id]['passing'] + '<br>@lang('attributes.speed_short'): ' + players[id]['speed'] + '<br>@lang('attributes.tackling_short'): ' + players[id]['tackling'] + '<br>@lang('attributes.stamina_short'): ' + players[id]['stamina'] + '</div>');
        $('#player-info').stop().fadeTo(0, 1).css({'display': 'contents'});
    }).mouseleave(function() {
        //$('#player-info').fadeOut(500);
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
            content = playerHandler(id);
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
                    var content = playerHandler(old_id);
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

    function playerHandler(index) {
        var content = players[index]['number'] + '<div class="status">';
        var count = 0;
        if (players[index]['retiring'] && count < 3) {
            content += '<span class="fa fa-user-times" style="color:#f00;"></span>';
            count++;
        }
        if (players[index]['cards_count'] >= {{ config('constants.YELLOW_CARDS_SUSPENSION') - 1}} && count < 3) {
            content += '<span class="fa fa-square" style="color:#ff0;"></span>';
            count++;
        }
        if (players[index]['suspended'] && count < 3) {
            content += '<span class="fa fa-square" style="color:#f00;"></span>';
            count++;
        }
        if (players[index]['recovery'] && count < 3) {
            content += '<span class="fa fa-medkit" style="color:#f00;"></span>';
            count++;
        }
        if (players[index]['healed'] && count < 3) {
            content += '<span class="fa fa-plus-circle" style="color:#0a0;"></span>';
            count++;
        }
        if (players[index]['upgraded'] && count < 3) {
            content += '<span class="fa fa-arrow-circle-up" style="color:#0a0;"></span>';
            count++;
        }
        if (players[index]['stamina'] < 50 && count < 3) {
            content += '<span class="fa fa-arrow-down" style="color:#f00;"></span>';
            count++;
        }
        if (players[index]['transferable'] && count < 3) {
            content += '<span class="fa fa-share-square-o" style="color:#0a0;"></span>';
            count++;
        }
        content += '<div>';

        return content;
    }
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
            <h4 class="widgettitle">@lang('labels.formation')</h4>
        </div>
        <div class="widgetcontent" style="padding-top:60px;">
            <div id="strategy-container">
                <img src="{{ asset('img/field-large-3d.png') }}">
                <div class="strategy-players-container">
                    @for ($i = 1; $i <= 11; $i++)
                        @if (empty($players[$formation[$i - 1]]))
                        <div id="player-{{ sprintf('%02d', $i) }}" data-player-id="0" class="player-container player-draggable player-droppable rollover-player" style="left:{{ $strategies[$strategy][$i]['left'] }}%;top:{{ $strategies[$strategy][$i]['top'] }}%;"></div>
                        @else
                        <div id="player-{{ sprintf('%02d', $i) }}" data-player-id="{{ isset($formation[$i - 1]) ? $formation[$i - 1] : 0 }}" class="player-container player-draggable player-droppable rollover-player {{ strtolower($players[$formation[$i - 1]]['position']) }}" style="left:{{ $strategies[$strategy][$i]['left'] }}%;top:{{ $strategies[$strategy][$i]['top'] }}%;">
                            {{ $players[$formation[$i - 1]]['number'] }}
                            {!! $players[$formation[$i - 1]]->bladeHandlerIcons !!}
                        </div>
                        @endif
                    @endfor
                </div>
            </div>
            <h3 style="font-weight:bold;margin-top:20px;">Suplentes</h3>
            @for ($i = 12; $i <= 18; $i++)
                @if (empty($players[$formation[$i - 1]]))
                <div id="player-{{ $i }}" data-player-id="0" class="player-container player-draggable player-droppable rollover-player"></div>
                @else
                <div id="player-{{ $i }}" data-player-id="{{ $formation[$i - 1] }}" class="player-container player-draggable player-droppable rollover-player {{ strtolower($players[$formation[$i - 1]]['position']) }}">
                    {{ $players[$formation[$i - 1]]['number'] }}
                    {!! $players[$formation[$i - 1]]->bladeHandlerIcons !!}
                </div>
                @endif
            @endfor
            <button class="btn btn-sm btn-primary" style="margin-top: 10px;" data-toggle="modal" data-target="#modal-numbers-confirm">@lang('labels.update_numbers')</button>
        </div>
    </div>
</div>
<div class="col-md-4">
    <table id="dyntable" class="table table-bordered responsive">
        <thead>
            <tr>
                <th>#</th>
                <th style="width:50%">@lang('labels.name')</th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.position')">@lang('attributes.position_short')</span></th>
                <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.average')">@lang('attributes.average_short')</span></th>
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
                    @if ($player->cards != NULL && $player->cards->cards >= config('constants.YELLOW_CARDS_SUSPENSION') - 1)
                    <span class="fa fa-square" style="color:#ff0;"></span>
                    @endif
                    @if ($player->cards != NULL && $player->cards->suspension)
                    <span class="fa fa-square" style="color:#f00;"></span>
                    @endif
                    @if ($player->recovery)
                    <span class="fa fa-medkit" style="color:#f00;"></span>
                    @endif
                    @if ($player->healed)
                    <span class="fa fa-plus-circle" style="color:#0a0;"></span>
                    @endif
                    @if ($player->upgraded)
                    <span class="fa fa-arrow-circle-up" style="color:#0a0;"></span>
                    @endif
                    @if ($player->stamina < 50)
                    <span class="fa fa-arrow-down" style="color:#f00;"></span>
                    @endif
                    @if ($player->transferable)
                    <span class="fa fa-share-square-o" style="color:#0a0;"></span>
                    @endif
                </td>
                <td align="center" class="player-position"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $player['position_long'] }}">{{ $player['position'] }}</span></td>
                <td align="right" class="player-average"><strong>{{ $player['average'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @include('modules.playerslegends')
</div>
<div class="col-md-3">
    <div id="player-info" class="widgetbox col-md-12" style="display:none;">
        <div class="headtitle">
            <h4 id="player-header" class="widgettitle"></h4>
        </div>
        <div class="widgetcontent text-uppercase"></div>
    </div>
</div>
<div class="modal fade" id="modal-numbers-confirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.confirm_action')</h4>
            </div>
            <div class="modal-body">
                <p>@lang('messages.confirm_update_numbers')</p>
            </div>
            <div class="modal-footer">
                <form id="form-numbers-update" method="POST" action="{{ route('team.numbers.update') }}">
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn-primary" value="@lang('labels.update')" />
                    <button type="reset" data-dismiss="modal" class="btn">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection