@extends('layouts.admin')

@section('javascript-inner')
<script src="{{ asset('js/confirmdelete.js') }}"></script>
<script type="text/javascript">
$(function() {
    $('.message-preview').click(function(event) {
        event.preventDefault();

        $.ajax({
            'method' : 'GET',
            'url' : '/mensaje/' + $(this).data('id'),
            'dataType': 'json'
        }).done(function(data){
            $('#modal-preview-title').html(data.title);
            $('#modal-preview-message').html(data.message);
            $('#modal-preview').modal();
        });
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Mensajes</h3>
    <a href="{{ route('admin.message.create', $_domain) }}" class="btn btn-danger" style="float:right;">Crear Mensaje</a>
    <div class="clear"></div>
    @forelse($messages as $message)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ $message['title'] }}</div>
        <div class="col-xs-3">{{ $message['valid_from'] }}</div>
        <div class="col-xs-3">{{ $message['valid_to'] }}</div>
        <div class="col-xs-2"><a href="#" class="message-preview" data-id="{{ $message['id'] }}"><span class="fa fa-search"></span></a> <a href="{{ route('admin.message.edit', ['domain' => $_domain, 'id' => $message['id']]) }}"><span class="fa fa-edit"></span></a> <a href="{{ route('admin.message.delete', ['domain' => $_domain, 'id' => $message['id']]) }}" class="btn-delete"><span class="fa fa-remove"></span></a></div>
    </div>
    @empty
    <h4>No hay mensajes</h4>
    @endforelse
</div>
<div class="modal fade" id="modal-preview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-preview-title"></h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-preview-message">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('modules.modals.confirmdelete')
@endsection
