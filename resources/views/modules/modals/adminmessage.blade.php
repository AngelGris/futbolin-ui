@if (Session::has('admin_message'))
<div class="modal fade" id="modal-admin-message">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ session('admin_message')['title'] }}</h4>
            </div>
            <div class="modal-body modal-match-result">
                {!! session('admin_message')['message'] !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif