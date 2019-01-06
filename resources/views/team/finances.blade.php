@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
    $(function() {
        $('#modal-sell-credits-credits').change(function() {
            var value = $(this).val() * {{ config('constants.CREDITS_SELL_VALUE') }}
            $('#modal-sell-credits-value').text(formatCurrency(value));
        });
    });
</script>
@endsection

@section('content-inner')
<div class="col-sm-12" style="margin-bottom:20px;">
    <h4 style="text-align: right;">@lang('labels.credits_with_value', ['value' => $_user->credits]) <a href="#" data-toggle="modal" data-target="#modal-sell-credits" class="btn btn-xs btn-primary">@lang('labels.sell_credits')</a></h4>
</div>
@if (count($movements) == 0)
<h3>@lang('labels.no_transactions')</h3>
@else
{{ $movements->links() }}
<table id="dyntable" class="table table-bordered table-players responsive">
    <thead>
        <tr>
            <th>@lang('labels.date')</th>
            <th>@lang('labels.description')</th>
            <th>@lang('labels.amount')</th>
            <th>@lang('labels.balance')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($movements as $movement)
        <tr>
            <td style="text-align: right;">{{ $movement->created_at->format('d/m/Y') }}</td>
            <td>{{ $movement->description }}</td>
            <td style="text-align: right;">{!! formatCurrency($movement->amount) !!}</td>
            <td style="text-align: right;">{!! formatCurrency($movement->balance) !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $movements->links() }}
@endif
<div class="modal fade" id="modal-sell-credits" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('labels.sell_credits')</h4>
            </div>
            @if ($_user->credits == 0)
            <div class="modal-body">
                <p>@lang('messages.no_credits_to_sell')</p>
                <p>@lang('messages.do_you_want_to_buy_credits')</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-xs btn-primary">@lang('labels.buy_credits')</a>
                <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
            </div>
            @else
            <form method="post" action="{{ route('shopping.buy') }}">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="6">
                <div class="modal-body">
                    <p>@lang('messages.how_many_credit_to_sell')</p>
                    <select name="credits" id="modal-sell-credits-credits">
                        @for($i = 1; $i <= $_user->credits; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    @lang('labels.credits') = <span id="modal-sell-credits-value">{{ formatCurrency(config('constants.CREDITS_SELL_VALUE')) }}</span>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="@lang('labels.sell_credits')" />
                    <button type="button" data-dismiss="modal" class="btn">@lang('labels.cancel')</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection