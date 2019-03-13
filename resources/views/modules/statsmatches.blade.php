<table class="table table-bordered table-stats-versus responsive">
    <thead>
        <tr>
            <th></th>
            <th>@lang('labels.matches_played_short')</th>
            <th>@lang('labels.matches_won_short')</th>
            <th>@lang('labels.matches_draw_short')</th>
            <th>@lang('labels.matches_lost_short')</th>
            <th>@lang('labels.goals_for_short')</th>
            <th>@lang('labels.goals_against_short')</th>
            <th>@lang('labels.goals_difference_short')</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>@lang('labels.home')</strong></td>
            <td align="right">{{ $matches[0][0] + $matches[0][1] + $matches[0][2] }}</td>
            <td align="right">{{ $matches[0][1] }}</td>
            <td align="right">{{ $matches[0][0] }}</td>
            <td align="right">{{ $matches[0][2] }}</td>
            <td align="right">{{ $goals[0][0] }}</td>
            <td align="right">{{ $goals[0][1] }}</td>
            <td align="right">{{ $goals[0][0] - $goals[0][1] }}</td>
        </th>
        <tr>
            <td><strong>@lang('labels.away')</strong></td>
            <td align="right">{{ $matches[1][0] + $matches[1][1] + $matches[1][2] }}</td>
            <td align="right">{{ $matches[1][2] }}</td>
            <td align="right">{{ $matches[1][0] }}</td>
            <td align="right">{{ $matches[1][1] }}</td>
            <td align="right">{{ $goals[1][1] }}</td>
            <td align="right">{{ $goals[1][0] }}</td>
            <td align="right">{{ $goals[1][1] - $goals[1][0] }}</td>
        </th>
        <tr>
            <td><strong>@lang('labels.total')</strong></td>
            <td align="right"><strong>{{ $matches[0][0] + $matches[0][1] + $matches[0][2] + $matches[1][0] + $matches[1][1] + $matches[1][2] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][1] + $matches[1][2] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][0] + $matches[1][0] }}</strong></td>
            <td align="right"><strong>{{ $matches[0][2] + $matches[1][1] }}</strong></td>
            <td align="right"><strong>{{ $goals[0][0] + $goals[1][1] }}</strong></td>
            <td align="right"><strong>{{ $goals[0][1] + $goals[1][0] }}</strong></td>
            <td align="right"><strong>{{ ($goals[0][0] + $goals[1][1]) - ($goals[0][1] + $goals[1][0]) }}</strong></td>
        </th>
    </tbody>
</table>