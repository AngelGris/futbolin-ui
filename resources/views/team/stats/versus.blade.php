<h4>Estadísticas contra {{ $rival['name'] }}</h4>
@include('modules.statsmatches')
@if (!empty($last_matches))
<h4>Últimos partidos</h4>
<table class="table table-bordered responsive">
    <thead>
        <tr>
            <th>@lang('labels.round')</th>
            <th>@lang('labels.condition_short')</th>
            <th>@lang('labels.result_short')</th>
            <th>@lang('labels.details_short')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($last_matches as $match)
        <tr>
            <td>{{ $match['date'] }}</td>
            <td>{{ $match['condition'] }}</td>
            <td align="center">{!! $match['score'] !!}</td>
            <td align="center"><a href="#" class="view-match" onClick="loadResult('{{ $match['logfile'] }}'); return false;"><span class="fa fa-search"></span></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<div class="clear"></div>