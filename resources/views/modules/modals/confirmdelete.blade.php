<form id="form-delete" method="POST" action="">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<div class="modal fade" id="modal-confirm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmar acción</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que quiere borrar esto?</p>
                <p>Una vez borrado no se puede recuperar</p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Delete</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>