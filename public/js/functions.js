var remaining_timer;

function changeShield(id) {
    $('#shield-value').val(id);

    loadSVGintoIMG($('#shield-svg'), '/img/shield/shield-' + (id < 10 ? '0' : '') + id + '.svg');

    $('#modal-shield-select').modal('hide');

    return false;
};

function formatCurrency(value) {
    value = value.toLocaleString().replace('.', ',');

    return value + ' $';
}

function loadAdminMessage(id) {
    $.ajax({
        'method': 'GET',
        'url': '/mensaje-admin/' + id,
        'dataType': 'json'
    }).done(function(data) {
        showAdminMessage(data.title, data.message);
    });
}

function showAdminMessage(title, message) {
    $('#modal-admin-message-title').html(title);
    $('#modal-admin-message-body').html(message);
    $('#modal-admin-message').modal();
}

function loadSVGintoIMG(img, url) {
    var imgID = img.attr('id');
    var imgClass = img.attr('class');
    var imgStyle = img.attr('style');
    var color_primary = img.data('color-primary');
    var color_secondary = img.data('color-secondary');

    $.get(url, function(data) {
        // Get the SVG tag, ignore the rest
        var svg = $(data).find('svg');

        // Add replaced image's ID to the new SVG
        if(typeof imgID !== 'undefined') {
            svg = svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if(typeof imgClass !== 'undefined') {
            svg = svg.attr('class', imgClass+' replaced-svg');
        }
        // Add replaced image's styles to the new SVG
        if(typeof imgStyle !== 'undefined') {
            svg = svg.attr('style', imgStyle);
        }

        // Remove any invalid XML tags as per http://validator.w3.org
        svg = svg.removeAttr('xmlns:a');

        // Replace image with new SVG
        img.replaceWith(svg);

        svg.find('.shield-primary-color').css({ 'fill' : color_primary});
        svg.find('.shield-secondary-color').css({ 'fill' : color_secondary});
    }, 'xml');
}

function updateShieldColor() {
    $('#shield-svg .shield-primary-color').css({ 'fill' : $('#primary_color_picker').val() });
    $('#shield-svg .shield-secondary-color').css({ 'fill' : $('#secondary_color_picker').val() });
}

function refreshResultModal(data, refreshOnClose = false) {
    $('#modal-match-result-content').html(data);
    $('img.svg').each(function(){
        loadSVGintoIMG($(this), $(this).attr('src'));
    });

    if ($('#modal-playing').is(':visible')) {
        $('#modal-playing').on('hidden.bs.modal', function () {
            $('#modal-match-result').modal('show');
        }).modal('hide');
    } else {
        $('#modal-match-result').modal('show');
    }

    if (refreshOnClose) {
        $('#modal-match-result').on('hidden.bs.modal', function () {
            location.reload();
        });
    } else {
        $('#modal-match-result').on('hidden.bs.modal', function () {});
    }
}

$(function(){
    $(document).on('click',function(){
        $('.collapse').collapse('hide');
    })

    $('[data-toggle="tooltip"]').tooltip();

    /**
     * Initiate color pickers
     */
    if ($('.colorpicker').length) {
        console.log($('.colorpicker'));
        $('.colorpicker').spectrum({
            preferredFormat: "hex",
            showPalette: true,
            palette: [
                ["#000","#666","#ccc","#fff"],
                ["#f00","#ff0","#0f0","#00f"],
                ["#ea9999","#ffe599","#b6d7a8","#9fc5e8"],
                ["#e06666","#ffd966","#93c47d","#6fa8dc"],
                ["#c00","#f1c232","#6aa84f","#3d85c6"],
                ["#900","#bf9000","#38761d","#0b5394"],
                ["#600","#7f6000","#274e13","#073763"]
            ],
            showButtons: false,
            showInitial: true,
            move: function(color) {
                $(this).val(color);
                updateShieldColor();
            },
            change: function(color) {
                updateShieldColor();
            }
        });
    }

    /*
     * Replace all SVG images with inline SVG
     */
    $('img.svg').each(function(){
        loadSVGintoIMG($(this), $(this).attr('src'));
    });

    $('#shield-select').click(function(event) {
        event.preventDefault();
        $('#modal-shield-select').modal('show');
    });

    $('.open-notification').click(function(event) {
        event.preventDefault();
        $(this).removeClass('unread');
        $.ajax({
            'method': 'GET',
            'url': '/notificacion/' + $(this).data('id'),
            'dataType': 'json'
        }).done(function(data) {
            $('.unread-count').text(data.unread);
            if (parseInt(data.unread) == 0) {
                $('.unread-count').removeClass('count-alert');
            }
            showAdminMessage(data.title, data.message);
        });
    });

    $('.trainning-button').click(function(event) {
        event.preventDefault();

        $.ajax({
            'method' : 'POST',
            'url' : '/equipo/entrenar/',
            'data' : {_token : $(this).data('token')},
            'dataType': 'json'
        }).done(function(data){
            showAdminMessage(data.title, data.message);
            if (data.remaining > 0) {
                $('button.trainning-button').hide();
                $('li.trainning').hide();
                $('button.trainning-button-disabled').show();
                $('li.trainning-disabled').show();
                startRemainingTimer(data.remaining);
            }
        });
    });

    $('#modal-admin-message').on('click', '#btn-trainning-keep', function() {
        var token = $(this).data('token');

        $('#modal-admin-message').one('hidden.bs.modal', function () {
            $.ajax({
                'method' : 'POST',
                'url' : '/shopping/comprar/',
                'data' : {_token : token, id: 7},
                'dataType': 'json'
            }).done(function(data){
                showAdminMessage('Continuar entrenando', '<p>¡Tu racha de entrenamientos es de ' + data.count + ' días!</p><p>Hoy tus jugadores ganaron ' + data.points + ' puntos de experiencia y recuperaron ' + data.points + ' puntos de energía.</p>');
                if (data.next > 0) {
                    $('button.trainning-button').hide();
                    $('li.trainning').hide();
                    $('button.trainning-button-disabled').show();
                    $('li.trainning-disabled').show();
                    startRemainingTimer(data.next);
                }
            }).fail(function(data) {
                if (data.responseJSON.type == 'credit_insufficient') {
                    showAdminMessage('No tienes Fúlbos', '<p>Necesitas Fúlbos para poder mantener tu racha de entrenamientos.</p><p>¿Quieres comprar más Fúlbos?</p><p><a href="/shopping/creditos" class="btn btn-primary">Comprar Fúlbos</a></p>');
                }
            });
        }).modal('hide');
    });

    $('#modal-admin-message').on('click', '#btn-trainning-restart', function() {
        var token = $(this).data('token');

        $('#modal-admin-message').one('hidden.bs.modal', function () {
            $.ajax({
                'method' : 'POST',
                'url' : '/equipo/entrenar/',
                'data' : {_token : token, restart: true},
                'dataType': 'json'
            }).done(function(data){
                showAdminMessage(data.title, data.message);
                $('button.trainning-button').hide();
                $('li.trainning').hide();
                $('button.trainning-button-disabled').show();
                $('li.trainning-disabled').show();
                startRemainingTimer(data.remaining);
            });
        }).modal('hide');
    });

    $('#select-language').change(function() {
        $(this).closest('form').submit();
    });

    function startRemainingTimer(remaining = 0) {
        if (remaining > 0) {
            trainable_remaining = remaining;
        }

        if (typeof trainable_remaining !== 'undefined' && trainable_remaining > 0) {
            updateTrainableTimer()
            remaining_timer = setInterval(updateTrainableTimer, 1000);
        }
    }

    function updateTrainableTimer() {
        trainable_remaining--;
        if (trainable_remaining > 0) {
            remaining = trainable_remaining;
            hours = parseInt(remaining / 3600);
            remaining -= hours * 3600;
            minutes = parseInt(remaining / 60);
            seconds = remaining - (minutes * 60);
            if (hours < 10) {
                hours = '0' + hours;
            }
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            $('.remaining-timer').text(hours + ':' + minutes + ':' + seconds);
        } else {
            clearInterval(remaining_timer);
            $('button.trainning-button-disabled').hide();
            $('li.trainning-disabled').hide();
            $('button.trainning-button').show();
            $('li.trainning').show();
        }
    }

    startRemainingTimer();
});