function loadTeamColorsPickers(primary_color, secondary_color) {
    $('#primary_color_selector').ColorPicker({
        color: primary_color,
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#primary_color_selector span').css('backgroundColor', '#' + hex);
            $('#primary_color_picker').val('#' + hex);
            updateShieldColor();
        }
    });

    $('#secondary_color_selector').ColorPicker({
        color: secondary_color,
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#secondary_color_selector span').css('backgroundColor', '#' + hex);
            $('#secondary_color_picker').val('#' + hex);
            updateShieldColor();
        }
    });
};

function changeShield(id) {
    $('#shield-value').val(id);

    loadSVGintoIMG($('#shield-svg'), '/img/shield/shield-' + (id < 10 ? '0' : '') + id + '.svg');

    $('#modal-shield-select').modal('hide');

    return false;
};

function loadSVGintoIMG(img, url) {
    var imgID = img.attr('id');
    var imgClass = img.attr('class');
    var imgStyle = img.attr('style');

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

        updateShieldColor()
    }, 'xml');
}

function updateShieldColor() {
    $('#shield-svg .shield-primary-color').css({ 'fill' : $('#primary_color_picker').val() });
    $('#shield-svg .shield-secondary-color').css({ 'fill' : $('#secondary_color_picker').val() });

    $('#shield-local .shield-primary-color').css({ 'fill' : $('#local_primary_color').val() });
    $('#shield-local .shield-secondary-color').css({ 'fill' : $('#local_secondary_color').val() });
    $('#shield-visit .shield-primary-color').css({ 'fill' : $('#visit_primary_color').val() });
    $('#shield-visit .shield-secondary-color').css({ 'fill' : $('#visit_secondary_color').val() });
}

function refreshResultModal(data) {
    $('#modal-match-result-content').html(data);
    $('img.svg').each(function(){
        loadSVGintoIMG($(this), $(this).attr('src'));
    });
    $('#modal-match-result').modal('show');
}

$(function(){
    $(document).on('click',function(){
        $('.collapse').collapse('hide');
    })

    $('[data-toggle="tooltip"]').tooltip();

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
});