<div class="modal fade" id="modal-playing">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('labels.match_time')</h4>
            </div>
            <div class="modal-body">
                <p id="modal-playing-message">@lang('messages.playing_match')</p>
            </div>
            <div class="modal-footer">
                <img src="{{ asset('img/loader.gif') }}" />
            </div>
        </div>
    </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
            </div>
        </div>
    </div>
</div>