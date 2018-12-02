@extends('layouts.inner')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/market.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
@parent
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": []
    });

    $('#toggle-filters').click(function() {
        $('#filters').slideToggle();
    });

    $('#sel-position').change(function() {
        if ($(this).val() == 'all') {
            $('#chk-position').prop('checked', false);
        } else {
            $('#chk-position').prop('checked', true);
        }
    });

    $('#sel-attribute').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#sel-attribute-from').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#sel-attribute-to').change(function() {
        $('#chk-attribute').prop('checked', true);
    });

    $('#txt-value-from').keyup(function() {
        $('#chk-value').prop('checked', true);
    });

    $('#txt-value-to').keyup(function() {
        $('#chk-value').prop('checked', true);
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-sm-8">
    @if ($offers)
    <a class="btn btn-sm btn-primary" href="{{ route('market') }}">@lang('labels.back_to_market')</a>
    @else
    <button id="toggle-filters" class="btn btn-sm btn-primary">@lang('labels.filters')</button>
    @endif
    <a class="btn btn-sm btn-primary" href="{{ route('market.offers') }}">@lang('labels.my_offers')</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.following') }}">@lang('labels.following')</a>
    <a class="btn btn-sm btn-primary" href="{{ route('market.transactions') }}">@lang('Closed deals')</a>
</div>
<div class="col-sm-4" style="text-align:right;">
    Oferta máxima: {{ formatCurrency($_team->calculateSpendingMargin()) }}
</div>
<div class="col-sm-12" id="filters" style="text-align: center;{{ empty($filters) ? 'display: none;' : '' }}">
    <form>
        <div class="row">
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_position" id="chk-position"{{ !empty($filters['filter_position']) ? ' checked="checked"' : '' }} />@lang('labels.position')</label><br />
                    <select name="pos" id="sel-position">
                        <option value="all">@lang('labels.all')</option>
                        @foreach(['arq', 'def', 'med', 'ata'] as $pos)
                        @if (!empty($filters['pos']) && $pos == $filters['pos'])
                        <option value="{{ $pos }}" selected>@lang('positions.' . $pos)</option>
                        @else
                        <option value="{{ $pos }}">@lang('positions.' . $pos)</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_attribute" id="chk-attribute"{{ !empty($filters['filter_attribute']) ? ' checked="checked"' : '' }} />@lang('labels.attribute')</label><br />
                    <select name="attr" id="sel-attribute">
                        @foreach(['average', 'goalkeeping', 'defending', 'dribbling', 'heading', 'jumping', 'passing', 'precision', 'speed', 'strength', 'tackling'] as $attribute)
                        @if (!empty($filters['attr']) && $attribute == $filters['attr'])
                        <option value="{{ $attribute }}" selected>@lang('attributes.' . $attribute)</option>
                        @else
                        <option value="{{ $attribute }}">@lang('attributes.' . $attribute)</option>
                        @endif
                        @endforeach
                    </select><br />
                    @lang('labels.between')
                    <select name="attr_from" id="sel-attribute-from">
                        @for($i = 0; $i <= 100; $i += 5)
                        @if (!empty($filters['attr_from']) && $i == $filters['attr_from'])
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                        @else
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endif
                        @endfor
                    </select>
                    <select name="attr_to" id="sel-attribute-to">
                        @for($i = 0; $i <= 100; $i += 5)
                        @if ((!empty($filters['attr_to']) && $i == $filters['attr_to']) || (empty($filters['attr_to']) && $i == 100))
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                        @else
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endif
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="market-filters">
                    <label><input type="checkbox" name="filter_value" id="chk-value"{{ !empty($filters['filter_value']) ? ' checked="checked"' : '' }} />@lang('labels.value')</label><br />
                    @lang('labels.greater_than') <input type="text" name="value_from" id="txt-value-from" value="{{ !empty($filters['value_from']) ? $filters['value_from'] : 100000 }}" /><br />
                    @lang('labels.lower_than') <input type="text" name="value_to" id="txt-value-to" value="{{ !empty($filters['value_to']) ? $filters['value_to'] : $_team->calculateSpendingMargin() }}" />
                </div>
            </div>
        </div>
        <div class="row">
            <button class="btn btn-sm btn-primary">@lang('labels.filter')</button>
        </div>
    </form>
</div>
<div class="col-sm-12">
    {{ $transferables->links() }}
</div>
<div class="clear"></div>
<table id="dyntable" class="table table-bordered table-transferables responsive">
    <thead>
        <tr>
            <th>@lang('labels.name')</th>
            <th>@lang('labels.team')</th>
            <th>@lang('attributes.age')</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.position')">@lang('attributes.position_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.average')">@lang('attributes.average_short')</span></th>
            <th>@lang('labels.value')</th>
            <th>@lang('labels.final_date')</th>
            <th>@lang('labels.best_offer')</th>
            <th>@lang('labels.offer')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transferables as $transferable)
        <tr class="{{ strtolower($transferable->player->position) }}">
            <td><a href="{{ route('player', $transferable->player->id) }}">{!! $transferable->player->name  !!}</a></td>
            <td>
                @if ($transferable->player->team)
                {{ $transferable->player->team->name }}
                @else
                <i>@lang('labels.free_player')</i>
                @endif
            </td>
            <td align="center">{{ $transferable->player->age }}</td>
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $transferable->player->position_long }}">{{ $transferable->player->position }}</span></td>
            <td align="right"><strong>{{ $transferable->player->average }}</strong></td>
            <td>{{ formatCurrency($transferable->value) }}</td>
            <td>{{ $transferable->closes_at->format('d/m/Y H:i') }}</td>
            <td>
                @if ($transferable->best_offer_team)
                {{ formatCurrency($transferable->best_offer_value) }} ({{ $transferable->offeringTeam->name }})
                @else
                @lang('labels.no_offers')
                @endif
            </td>
            <td align="center">
                @if (!$transferable->player->team || $transferable->player->team->id != $_team->id)
                <button class="btn btn-primary btn-offer" data-transfer="{{ $transferable->id }}" data-id="{{ $transferable->player->id }}" data-name="{{ $transferable->player->first_name . ' ' . $transferable->player->last_name }}" data-value="{{ $transferable->offer_value + 1 }}" data-offer="{{ (int)($transferable->offer_value * 1.05) }}">@lang('labels.offer')</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="col-sm-12">
    {{ $transferables->links() }}
</div>
@include('modules.playerslegends')
@include('modules.modals.marketoffer')
@endsection
