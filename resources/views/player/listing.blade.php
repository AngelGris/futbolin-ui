@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function(){
    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false
    });

    $('.attribute-upgraded').each(function(index, item) {
        $(item).after('<sub class="attribute-upgraded">+' + $(item).data('upgraded') + '</sub>');
    });

    $('#players-filtering').change(function() {
        if ($(this).val() != '') {
            $('tbody tr').hide();
            $('tbody tr.' + $(this).val()).show();
        } else {
            $('tbody tr').show();
        }
    });
});
</script>
@endsection

@section('content-inner')
@lang('labels.show'):
<select id="players-filtering">
    <option value="">@lang('labels.all')</option>
    <option value="arq">@lang('labels.goalkeepers')</option>
    <option value="def">@lang('labels.defenders')</option>
    <option value="med">@lang('labels.midfielders')</option>
    <option value="ata">@lang('labels.forwards')</option>
</select>
<table id="dyntable" class="table table-bordered table-players responsive">
    <thead>
        <tr>
            <th>#</th>
            <th>@lang('labels.name')</th>
            <th>@lang('attributes.age')</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.position')">@lang('attributes.position_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.stamina')">@lang('attributes.stamina_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.average')">@lang('attributes.average_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.goalkeeping')">@lang('attributes.goalkeeping_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.defending')">@lang('attributes.defending_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.dribbling')">@lang('attributes.dribbling_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.heading')">@lang('attributes.heading_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.jumping')">@lang('attributes.jumping_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.passing')">@lang('attributes.passing_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.precision')">@lang('attributes.precision_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.speed')">@lang('attributes.speed_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.strength')">@lang('attributes.strength_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.tackling')">@lang('attributes.tackling_short')</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="@lang('attributes.experience')">@lang('attributes.experience_short')</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($players as $player)
        <tr class="{{ strtolower($player['position']) }}">
            <td align="right">{{ $player['number'] }}</td>
            <td><a href="{{ route('player', $player['id']) }}">{!! $player['name']  !!}</a></td>
            <td align="center">{{ $player['age'] }}</td>
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $player['position_long'] }}">@lang('positions.' . strtolower($player['position']) . '_short')</span></td>
            <td align="right">{{ $player['stamina'] }}</td>
            <td align="right"><strong>{{ $player['average'] }}</strong></td>
            @foreach (['goalkeeping', 'defending', 'dribbling', 'heading', 'jumping', 'passing', 'precision', 'speed', 'strength', 'tackling'] as $attr)
            <td align="right">
                @if ($player['upgraded'] && !empty($player['last_upgrade']->{$attr}))
                <span class="attribute-upgraded" data-upgraded="{{ $player['last_upgrade']->{$attr} }}">{{ $player[$attr] }}</span>
                @else
                {{ $player[$attr] }}
                @endif
            </td>
            @endforeach
            <td align="right">{{ $player['experience'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@include('modules.playerslegends')
@endsection