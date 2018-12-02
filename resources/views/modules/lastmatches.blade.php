@if (!empty($last_matches))
<div class="col-md-6 zebra" id="home-last-matches">
    <h3 style="margin-bottom:20px;text-align:center;">@lang('labels.last_matches')</h3>
    @foreach ($last_matches as $match)
    <div class="col-xs-12"{!! $match['won'] ? ' style="font-weight:bold;"' : '' !!}>
        <div class="col-xs-2">{{ $match['date'] }}</div>
        <div class="col-xs-4" style="text-align:right;"><a href="{{ route('team.show', $match['local_id']) }}">{{ $match['local'] }}</a> {{ $match['local_goals'] }}</div>
        <div class="col-xs-4"><a href="{{ route('team.show', $match['visit_id']) }}">{{ $match['visit'] }}</a> {{ $match['visit_goals'] }}</div>
        <div class="col-xs-2" style="text-align:right;"><a href="#" class="load-match" data-file="{{ $match['log_file'] }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
</div>
<div class="modal fade" id="modal-match-loading">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.loading_match_summary')</h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-match-loading-content">
                <img src="{{ asset('img/loader.gif') }}" />
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-match-result">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.match_summary')</h4>
            </div>
            <div class="modal-body modal-match-result" id="modal-match-result-content">
                <img src="{{ asset('img/loader.gif') }}" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
            </div>
        </div>
    </div>
</div>
@endif