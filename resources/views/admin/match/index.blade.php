@extends('layouts.admin')

@section('javascript-inner')
<script type="text/javascript">
$(function(){
    $('.load-match').click(function() {
        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('file'), show_remaining : true, _token : '{{ csrf_token() }}'},
        }).done(function(data){
            refreshResultModal(data);
        });
    })
});
</script>
@endsection

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>Partidos</h3>
    {{ $matches->links() }}
    @foreach($matches as $match)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ date('d/m/y H:i:s', strtotime($match['created_at'])) }}</div>
        <div class="col-xs-6">{{ $match['local']['name'] }} {{ $match['local_goals'] }} - {{ $match['visit_goals'] }} {{ $match['visit']['name'] }}</div>
        <div class="col-xs-1"><span class="fa fa-search load-match" data-file="{{ $match['logfile'] }}"></span></div>
        <div class="col-xs-1"><a href="{{ route('admin.match.log', [getDomain(), $match['id']]) }}" target="_blank"><span class="fa fa-file-text-o" data-file="{{ $match['logfile'] }}"></span></a></div>
    </div>
    @endforeach
    {{ $matches->links() }}
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