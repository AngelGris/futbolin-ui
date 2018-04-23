<div class="modal fade" id="modal-shield-select" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Elige el escudo de tu equipo</h4>
            </div>
            <div class="modal-body">
                @for ($i = 1; $i <= 16; $i++)
                <div class="col-xd-6 col-md-3" style="text-align:center;">
                    <a href="#" onClick="return changeShield({{ $i }});"><img src="{{ asset('/img/shield/shield-' . sprintf('%02d', $i) . '.svg') }}" style="height:70px;" /></a>
                </div>
                @endfor
                <div class="clear"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>