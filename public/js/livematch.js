$(function() {
    var time_interval;
    var time_current = 0;
    var time_total = 5400;
    var time_step = time_total / match_duration;
    var time_half = 1;
    var halftime = 15;
    var log_plays = [];
    var plays_last = 0;
    var plays_next = 0;
    var plays_index = 1;
    var team_local;
    var team_visit;
    var other_goals = [];

    $('#jplayer-final-whistle').jPlayer({
        ready : function() {
            $(this).jPlayer('setMedia', {
                mp3: '/audio/final_whistle.mp3'
            });
        },
        swfPath : '/swf/jquery.jplayer.swf'
    });

    $('#jplayer-goal').jPlayer({
        ready : function() {
            $(this).jPlayer('setMedia', {
                mp3: '/audio/goal.mp3'
            });
        },
        swfPath : '/swf/jquery.jplayer.swf'
    });

    $('#jplayer-whistle').jPlayer({
        ready : function() {
            $(this).jPlayer('setMedia', {
                mp3: '/audio/whistle.mp3'
            });
        },
        swfPath : '/swf/jquery.jplayer.swf'
    });

    $.ajax({
        'method': 'GET',
        'url': '/vivo/relato/' + match_log,
        'dataType': 'json'
    }).done(function(data) {
        time_current = data.time;
        team_local = data.local;
        team_visit = data.visit;

        data.local.formation.forEach(function(item, index) {
            if (index < 11) {
                player = '<li>' + item['short_name'] + '<span>(' + item['number'] + ')</span></li>';
                $('#formation-field').append('<div class="player-container player-container-local" style="left:' + item['left'] + '%;top:' + item['top'] + '%;background-color:rgba(' + data.local.rgb_primary + ', 0.5);border-color:' + data.local.rbg_secondary + ';color:' + data.local.text_color + ';">' + item['number'] + '</div>');
            } else {
                player = '<li><span>' + item['short_name'] + ' (' + item['number'] + ')</span></li>';
            }
            $('#formation-local').append(player);
        });

        data.visit.formation.forEach(function(item, index) {
            if (index < 11) {
                player = '<li><span>(' + item['number'] + ')</span> ' + item['short_name'] + '</li>';
                $('#formation-field').append('<div class="player-container player-container-visit" style="left:' + item['left'] + '%;top:' + item['top'] + '%;background-color:rgba(' + data.visit.rgb_primary + ', 0.5);border-color:' + data.visit.rbg_secondary + ';color:' + data.visit.text_color + ';">' + item['number'] + '</div>');
            } else {
                player = '<li><span>(' + item['number'] + ') ' + item['short_name'] + '</span></li>';
            }
            $('#formation-visit').append(player);
        });

        data.plays.forEach(function(item) {
            if ($.inArray(parseInt(item[2]), [1, 2, 4, 5, 6, 7, 8, 9, 11, 12, 14, 17, 18, 19, 21, 22, 23, 24, 25, 26]) >= 0) {
                time = item[0].split(':');
                time = (parseInt(time[0]) * 60) + parseInt(time[1]);
                item.unshift(time);
                log_plays.push(item);
            }
        });
        start_timer();
    });

    match_others.forEach(function(item) {
        $.ajax({
            'method' : 'GET',
            'url' : '/vivo/relato/' + item,
            'dataType' : 'json'
        }).done(function(data) {
            $('<div class="col-sm-6 match-other"><div class="col-sm-4"><img class="svg" id="shield-' + data.local.id + '" src="' + data.local.shield_file + '" data-color-primary="' + data.local.primary_color + '" data-color-secondary="' + data.local.secondary_color + '" style="height:50px;"><div class="match-other-name">' + data.local.short_name + '</div></div><div class="col-sm-4"><span id="score-' + data.logfile.replace('.', '-') + '-local">0</span> : <span id="score-' +  data.logfile.replace('.', '-') + '-visit">0</span></div><div class="col-sm-4"><img class="svg" id="shield-' + data.visit.id + '" src="' + data.visit.shield_file + '" data-color-primary="' + data.visit.primary_color + '" data-color-secondary="' + data.visit.secondary_color + '" style="height:50px;"><div class="match-other-name">' + data.visit.short_name + '</div></div></div>').appendTo('.match-other-wrapper').hide().fadeIn(1000);

            $('img.svg').each(function(){
                loadSVGintoIMG($(this), $(this).attr('src'));
            });

            data.plays.forEach(function(item) {
                if ($.inArray(parseInt(item[2]), [6, 19]) >= 0) {
                    time = item[0].split(':');
                    time = (parseInt(time[0]) * 60) + parseInt(time[1]);
                    other_goals.push([time, data.logfile, item[1]]);
                }
            });
        });
    });

    function addMarker(play, sound = true) {
        if ($.inArray(parseInt(play[3]), [6, 19, 22, 23, 24, 25, 26]) >= 0) {
            switch (play[3]) {
                case 6:
                case 19:
                    icon = 'fa-futbol-o';
                    player = $('#jplayer-goal');
                    break;
                case 22:
                    icon = 'fa-exchange';
                    player = false;
                    break;
                case 23:
                    icon = 'fa-plus-square fa-square-red';
                    player = false;
                    break;
                case 24:
                    icon = 'fa-square fa-square-yellow'
                    player = $('#jplayer-whistle');
                    break;
                case 25:
                case 26:
                    icon = 'fa-square fa-square-red'
                    player = $('#jplayer-whistle');
                    break;

            }
            $('<span class="fa ' + icon + '" style="left:' + (play[0] * 100 / time_total) + '%" title="' + play[1] + ' - ' + play[4] + '" data-toggle="tooltip" data-placement="top"></span>').appendTo("#timeline-markers").hide().fadeIn(1000).tooltip();
            if (sound && player) {
                player.jPlayer('play');
            }
        }
    }

    function broadcast(play) {
        if (play[2]) {
            bgColor = team_visit.primary_color;
            txtColor = team_visit.text_color;
        } else {
            bgColor = team_local.primary_color;
            txtColor = team_local.text_color;
        }

        $('<div style="background-color:' + bgColor + ';color:' + txtColor + ';"><div class="col-xs-1" style="padding:0;text-align:center;">' + play[1] + '</div><div class="col-xs-11">' + play[4] + '</div><div class="clear"></div></div>').prependTo("#broadcast-wrapper").hide().fadeIn(1000);
    }

    function format_time(time) {
        minutes = Math.floor(time / 60);
        seconds = time % 60;

        if (minutes < 10) {
            minutes = '0' + minutes;
        }
        if (seconds < 10) {
            seconds = '0' + seconds;
        }

        return minutes + ':' + seconds;
    }

    function increase_stats(team, field) {
        if (team == 0) {
            field = '.local-' + field;
        } else {
            field = '.visit-' + field;
        }

        $(field).hide().text(parseInt($(field).text()) + 1).fadeIn(1000);
    }

    function score_other(id, team) {
        field = '#score-' + id.replace('.', '-');
        if (team == 0) {
            field += '-local';
        } else {
            field += '-visit';
        }

        $(field).hide().text(parseInt($(field).text()) + 1).fadeIn(1000);
    }

    function start_timer() {
        if (time_current >= time_total / 2) {
            time_half = 2;
        }
        broadcast(log_plays[0]);
        plays_next = log_plays[1][0];
        time_interval = setInterval(update_broadcast, 1000);
        update_broadcast(false);
        $('#live-loading').remove();
    }

    function update_broadcast(sound = true) {
        time_current += time_step;

        if (time_half == 1 && time_current >= time_total / 2) {
            time_current = time_total / 2;
            time_half = 2;
        }

        while(plays_index < log_plays.length && log_plays[plays_index][0] <= time_current) {
            broadcast(log_plays[plays_index]);

            if ($.inArray(parseInt(log_plays[plays_index][3]), [4, 6, 11, 12, 17, 18, 19, 22, 23, 24, 25, 26]) >= 0) {
                switch (log_plays[plays_index][3]) {
                    case 4:
                        increase_stats((log_plays[plays_index][2] + 1) % 2, 'fouls');
                        break;
                    case 6:
                    case 19:
                        increase_stats(log_plays[plays_index][2], 'goals');
                        break;
                    case 12:
                    case 18:
                        increase_stats(log_plays[plays_index][2], 'shots-on-goal');
                    case 11:
                    case 17:
                        increase_stats(log_plays[plays_index][2], 'shots');
                        break;
                    case 22:
                    case 23:
                        increase_stats(log_plays[plays_index][2], 'substitutions');
                        break;
                    case 24:
                        increase_stats(log_plays[plays_index][2], 'yellow-cards');
                        break;
                    case 25:
                    case 26:
                        increase_stats(log_plays[plays_index][2], 'red-cards');
                        break;
                }

                addMarker(log_plays[plays_index], sound);
            }

            plays_index++;
        }

        $('#match-timer').text(format_time(time_current));
        new_width = ($('#timeline').width() * time_current / time_total);
        $('#timeline-marker').animate({'left' : new_width - 3}, 1000);
        $('#timeline-progress').animate({'width' : 100 - (time_current * 100 / time_total) + '%'}, 1000);

        aux = []
        other_goals.forEach(function(item) {
            if (item[0] <= time_current) {
                score_other(item[1], item[2]);
            } else {
                aux.push(item);
            }
        });
        other_goals = aux;

        if (time_current == time_total / 2 || time_current >= time_total) {
            clearInterval(time_interval);
            $('#jplayer-final-whistle').jPlayer('play');
            if (time_current == time_total / 2) {
                $('#match-timer').text('Descanso');
                setTimeout(function() {
                    time_interval = setInterval(update_broadcast, 1000);
                }, 60000);
            } else {
                $('#match-timer').text('Finalizado');
                setTimeout(function() {
                    document.location = '/vestuario/';
                }, 10000);
            }
        }
    }
});