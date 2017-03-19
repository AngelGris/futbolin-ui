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
            $('#primary_color_picker').val('#'+hex);
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
            $('#secondary_color_picker').val('#'+hex);
        }
    });
}

$(function(){
});