$(function() {
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        var action = $(this).attr('href');
        $('#modal-confirm').modal({
            backdrop: 'static',
            keyboard: false
        }).one('click', '#delete', function(e) {
            $('#form-delete').attr('action', action);
            $('#form-delete').submit();
        });
    });
});