@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    $('.load-match').click(function (event) {
        event.preventDefault();

        $.ajax({
            'method' : 'GET',
            'url' : '{{ route('match.load') }}',
            'data' : {file : $(this).data('file'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            $('#modal-match-result-content').html(data);
            $('#modal-match-result').modal('show');
        });
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-md-6" id="home-container">
    <div id="home-strategy">
        <img src="{{ asset('/img/field-large-h.png') }}" />
        @for ($i = 0; $i < 11; $i++)
            @if (empty($formation[$i]))
            <div class="player-container" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;"></div>
            @else
            <div class="player-container {{ strtolower($players[$formation[$i]]['position']) }}" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;">{{ $players[$formation[$i]]['number'] }}</div>
            @endif
        @endfor
        <a href="{{ route('strategy') }}"><div class="overlay"></div></a>
    </div>
</div>
@if (!empty($last_matches))
<div class="col-md-6 zebra" id="home-last-matches">
    <h3 style="margin-bottom:20px;text-align:center;">Últimos partidos</h3>
    @foreach ($last_matches as $match)
    <div class="col-xs-12"{!! $match['won'] ? ' style="font-weight:bold;"' : '' !!}>
        <div class="col-xs-3">{{ $match['date'] }}</div>
        <div class="col-xs-3" style="text-align:right;">{{ $match['local'] }} {{ $match['local_goals'] }}</div>
        <div class="col-xs-3">{{ $match['visit'] }} {{ $match['visit_goals'] }}</div>
        <div class="col-xs-3" style="text-align:right;"><a href="#" class="load-match" data-file="{{ $match['log_file'] }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
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
@endif
@endsection
