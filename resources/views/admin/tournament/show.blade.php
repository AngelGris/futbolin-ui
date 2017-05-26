@extends('layouts.admin')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    $('.show-category').click(function(event) {
        event.preventDefault();

        $('#modal-category-title').text($(this).data('title'));
        $('#modal-category-content').html('Cargando la categoría');
        $('#modal-category-loading').show();
        $('#modal-category').modal('show');

        $.ajax({
            'method' : 'GET',
            'url' : '/categoria/' + $(this).data('id'),
        }).done(function(data){
            $('#modal-category-loading').hide();
            $('#modal-category-content').html(data);
        });
    });
});

function changeTab(index) {
    if (index == 1) {
        $('#modal-category-tab-positions').addClass('active');
        $('#modal-category-tab-matches').removeClass('active');
        $('#modal-category-positions').show();
        $('#modal-category-matches').hide();
    } else {
        $('#modal-category-tab-positions').removeClass('active');
        $('#modal-category-tab-matches').addClass('active');
        $('#modal-category-positions').hide();
        $('#modal-category-matches').show();
    }
}

function changeRound(dropdown) {
    $('.modal-category-rounds').hide();
    $('#modal-category-round-' + dropdown.value).show();
}

function loadResult(fileName) {
    $.ajax({
        'method' : 'GET',
        'url' : '{{ route('match.load') }}',
        'data' : {file : fileName, show_remaining : false, _token : '{{ csrf_token() }}'},
    }).done(function(data){
        refreshResultModal(data);
    });
}
</script>
@endsection

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>{{ $tournament['name'] }}</h3>
    <div class="col-xs-12">
        <div class="col-xs-4">Categorías</div>
        <div class="col-xs-8">{{ $tournament['categories'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4">Zonas</div>
        <div class="col-xs-8">{{ $tournament['zones'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4">Posiciones</div>
        <div class="col-xs-8">
            @foreach($tournament['tournamentCategories'] as $category)
            <a href="#" class="btn btn-default show-category" data-id="{{ $category['id'] }}" data-title="{{ 'Zona ' . $category['zone_name'] . ' Cat. ' . $category['category_name'] }}">{{ 'Zona ' . $category['zone_name'] . ' Cat. ' . $category['category_name'] }}</a>
            @endforeach
        </div>
    </div>
</div>
<div class="modal fade" id="modal-category">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 id="modal-category-title" class="modal-title">Categoría</h4>
            </div>
            <div class="modal-body">
                <p id="modal-category-content">Cargando la categoría</p>
                <div id="modal-category-loading" style="margin-top:20px;"><img src="{{ asset('img/loader.gif') }}" /></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-match-result">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Resumen del partido</h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-match-result-content">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection