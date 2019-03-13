@section('javascript-inner')
@parent
<script type="text/javascript">
$(function() {
    var following = {{ $following }};
    updateSalary();

    $('#modal-make-offer-input').keyup(function() {
        updateSalary();
    });

    $('.btn-offer').click(function() {
        var limit = {{ $_team->calculateSpendingMargin() }};
        $('#modal-make-offer-player-id').val($(this).data('id'));
        $('#modal-make-offer-player-name').html(Lang.get('labels.make_an_offer_for_player', { player: $(this).data('name') }));
        $('#modal-make-offer-enabled').text(Lang.get('messages.offer_must_be_between', { min: formatCurrency($(this).data('value')), max: '{{ formatCurrency($_team->calculateSpendingMargin()) }}' }));
        $('#modal-make-offer-disabled').text(Lang.get('messages.minimum_offer_is_too_high', { minimum_offer: formatCurrency($(this).data('offer')), spending_margin: '{{ formatCurrency($_team->calculateSpendingMargin()) }}'}));
        $('#modal-make-offer-input').val($(this).data('offer'));
        updateSalary();
        if ($(this).data('value') <= limit) {
            $('#modal-make-offer-enabled').show();
            $('#modal-make-offer-disabled').hide();
            $('#modal-make-offer-input').attr('disabled', false);
            $('#buy-item').attr('disabled', false);
        } else {
            $('#modal-make-offer-enabled').hide();
            $('#modal-make-offer-disabled').show();
            $('#modal-make-offer-input').attr('disabled', true);
            $('#buy-item').attr('disabled', true);
        }
        if (following.indexOf($(this).data('id')) >= 0) {
            $('#follow-player').hide();
            $('#unfollow-player').data('transfer', $(this).data('transfer'));
            $('#unfollow-player').show();
        } else {
            $('#follow-player').data('transfer', $(this).data('transfer'));
            $('#follow-player').show();
            $('#unfollow-player').hide();
        }
        $('#modal-make-offer').modal('show');
    });

    $('#follow-player').click(function(event) {
        event.preventDefault();
        $.ajax({
            'method' : 'POST',
            'url' : '{{ route('market.follow') }}',
            'data' : {id : $(this).data('transfer'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            following.push(parseInt($('#modal-make-offer-player-id').val()));
            $('#modal-make-offer').modal('hide');
        });
    });

    $('#unfollow-player').click(function(event) {
        event.preventDefault();
        $.ajax({
            'method' : 'POST',
            'url' : '{{ route('market.unfollow') }}',
            'data' : {id : $(this).data('transfer'), _token : '{{ csrf_token() }}'},
        }).done(function(data){
            var index = following.indexOf(parseInt($('#modal-make-offer-player-id').val()));
            if (index > -1) {
                following.splice(index, 1);
            }
            $('#modal-make-offer').modal('hide');
        });
    });
});

function updateSalary() {
    var value = parseInt($('#modal-make-offer-input').val() * {{ \Config::get('constants.PLAYERS_SALARY') }});
    $('#player-salary').html(formatCurrency(value));
}
</script>
@endsection

<div class="modal fade" id="modal-make-offer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('player.offer') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">@lang('labels.make_an_offer')</h4>
                </div>
                <div class="modal-body">
                    <p id="modal-make-offer-player-name"></p>
                    <p id="modal-make-offer-enabled"></p>
                    <p id="modal-make-offer-disabled"></p>
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <label for="modal-make-offer-input">@lang('labels.value_of_the_offer')</label>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="offer" id="modal-make-offer-input" value="" />
                        </div>
                        <div class="col-sm-5">
                            <label>@lang('labels.salary')</label>
                        </div>
                        <div class="col-sm-7"><span id="player-salary"></span></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <input type="hidden" name="player_id" id="modal-make-offer-player-id" value="">
                    <button id="follow-player" class="btn btn-primary">@lang('labels.follow')</button>
                    <button id="unfollow-player" class="btn btn-primary">@lang('labels.unfollow')</button>
                    <button id="buy-item" class="btn btn-primary">@lang('labels.offer')</button>
                    <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
                </div>
            </form>
        </div>
    </div>
</div>