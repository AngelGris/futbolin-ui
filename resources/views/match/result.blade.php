<div class="col-xs-12 modal-match-result-teams">
    @if ($show_remaining)
    <div class="alert alert-danger" role="alert">@lang('messages.wait_to_play_again', ['time' => $remaining_time])</div>
    @endif
    <h4>{{ $stadium }} ({{ $datetime }})</h4>
    @if ($assistance > 0)
    <p>@lang('labels.number_spectators', ['number' => number_format($assistance)]) - @lang('labels.collection_value', ['value' => formatCurrency($incomes)])</p>
    @endif
    <div class="col-xs-4">
        <img class="svg" id="shield-local-res" src="{{ $local['shield_file'] }}"  data-color-primary="{{ $local['primary_color'] }}" data-color-secondary="{{ $local['secondary_color'] }}" style="height:70px;">
        <h4>{{ $local['name'] }}</h4>
    </div>
    <div class="col-xs-4 result"><h4>{{ $local['goals'] }} - {{ $visit['goals'] }}</h4></div>
    <div class="col-xs-4">
        <img class="svg" id="shield-visit-res" src="{{ $visit['shield_file'] }}"  data-color-primary="{{ $visit['primary_color'] }}" data-color-secondary="{{ $visit['secondary_color'] }}" style="height:70px;">
        <h4>{{ $visit['name'] }}</h4>
    </div>
</div>
<div class="col-xs-12 modal-match-result-goals">
    <div class="col-xs-6">
        <ul>
            @foreach ($scorers[0] as $scorer)
            <li>{!! $scorer !!}</li>
            @endforeach
        </ul>
    </div>
    <div class="col-xs-6">
        <ul>
            @foreach ($scorers[1] as $scorer)
            <li>{!! $scorer !!}</li>
            @endforeach
        </ul>
    </div>
</div>
<h3>@lang('labels.line_ups')</h3>
<div class="col-xs-12 modal-match-result-formations">
    <div class="col-sm-3 col-xs-6">
        <ul>
            @for ($i = 0; $i < 18; $i++)
                @if (isset($local['formation'][$i]))
                    @if ($i < 11)
                    <li>{{ $local['formation'][$i]['short_name'] }} <span>({{ $local['formation'][$i]['number'] }})</span></li>
                    @else
                    <li><span>{{ $local['formation'][$i]['short_name'] }} ({{ $local['formation'][$i]['number'] }})</span></li>
                    @endif
                @endif
            @endfor
        </ul>
    </div>
    <div class="col-sm-3 col-xs-6">
        <ul>
            @for ($i = 0; $i < 18; $i++)
                @if (isset($visit['formation'][$i]))
                    @if ($i < 11)
                    <li><span>({{ $visit['formation'][$i]['number'] }})</span> {{ $visit['formation'][$i]['short_name'] }}</li>
                    @else
                    <li><span>({{ $visit['formation'][$i]['number'] }}) {{ $visit['formation'][$i]['short_name'] }}</span></li>
                    @endif
                @endif
            @endfor
        </ul>
    </div>
    <div class="col-sm-6 col-xs-12">
        <img src="{{ asset('img/field-large.png') }}">
        @for ($i = 0; $i < 11; $i++)
            @if (isset($local['formation'][$i]))
            <div class="player-container player-container-local" style="left:{{ $local['formation'][$i]['left'] }}%;top:{{ $local['formation'][$i]['top'] }}%;background-color:rgba({{ implode(', ', $local['rgb_primary']) }}, 0.5);border-color:{{ $local['secondary_color'] }};color:{{ $local['text_color'] }};">{{ $local['formation'][$i]['number'] }}</div>
            @endif
            @if (isset($visit['formation'][$i]))
            <div class="player-container player-container-visit" style="left:{{ $visit['formation'][$i]['left'] }}%;top:{{ $visit['formation'][$i]['top'] }}%;background-color:rgba({{ implode(', ', $visit['rgb_primary']) }}, 0.5);border-color:{{ $visit['secondary_color'] }};color:{{ $visit['text_color'] }};">{{ $visit['formation'][$i]['number'] }}</div>
            @endif
        @endfor
    </div>
</div>
<h3>@lang('labels.highlights')</h3>
<div class="col-xs-12 modal-match-result-actions">
    @foreach ($actions as $action)
    <div style="background-color:{{ $action[1] }};color:{{ $action[2] }};">
        <div class="col-xs-1" style="padding:0;text-align:center;">{{ $action[0] }}'</div>
        <div class="col-xs-11">{!! $action[3] !!}</div>
        <div class="clear"></div>
    </div>
    @endforeach
</div>
<h3>@lang('labels.statistics')</h3>
<div class="col-xs-12 modal-match-result-statistics">
    <div class="col-xs-12">
        <div class="col-xs-4 {{ $local['goals'] > $visit['goals'] ? 'best' : '' }}">{{ $local['goals'] }}</div>
        <div class="col-xs-4">@lang('labels.goals')</div>
        <div class="col-xs-4 {{ $local['goals'] < $visit['goals'] ? 'best' : '' }}">{{ $visit['goals'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4 {{ $local['posession'] > $visit['posession'] ? 'best' : '' }}">{{ $local['posession'] }}</div>
        <div class="col-xs-4">@lang('labels.possession')</div>
        <div class="col-xs-4 {{ $local['posession'] < $visit['posession'] ? 'best' : '' }}">{{ $visit['posession'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-4 {{ $local['shots'] > $visit['shots'] ? 'best' : '' }}">{{ $local['shots'] }} ({{ $local['shots_goal'] }})</div>
        <div class="col-xs-4">@lang('labels.shots')</div>
        <div class="col-xs-4 {{ $local['shots'] < $visit['shots'] ? 'best' : '' }}">{{ $visit['shots'] }} ({{ $visit['shots_goal'] }})</div>
    </div>
    @if ($local['substitutions'] >= 0)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ $local['substitutions'] }}</div>
        <div class="col-xs-4">@lang('labels.substitutions')</div>
        <div class="col-xs-4">{{ $visit['substitutions'] }}</div>
    </div>
    @endif
    @if ($local['yellow_cards'] >= 0)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ $local['yellow_cards'] }} / {{ $local['red_cards'] }}</div>
        <div class="col-xs-4">@lang('labels.cards')</div>
        <div class="col-xs-4">{{ $visit['yellow_cards'] }} / {{ $visit['red_cards'] }}</div>
    </div>
    @endif
</div>
<div class="clear"></div>