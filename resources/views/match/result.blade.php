<div class="col-md-12 modal-match-result-teams">
    @if ($show_remaining)
    <div class="col-md-12" style="margin-bottom:20px;text-align:center;">En {{ $remaining_time }} podrás jugar un nuevo amistoso contra éste equipo</div>
    @endif
    <h4 style="margin-bottom:20px;">{{ $stadium }} ({{ $datetime }})</h4>
    <div class="col-md-4">
        <h4>{{ $local['name'] }}</h4>
        <div class="primarycolor" style="background-color:{{ $local['primary_color'] }};border-color:{{ ($local['primary_color'] == '#ffffff') ? $local['secondary_color'] : $local['primary_color'] }}"></div>
        <div class="secondarycolor" style="background-color:{{ $local['secondary_color'] }};border-color:{{ ($local['secondary_color'] == '#ffffff') ? $local['primary_color'] : $local['secondary_color'] }}"></div>
    </div>
    <div class="col-md-4"><h4>{{ $local['goals'] }} - {{ $visit['goals'] }}</h4></div>
    <div class="col-md-4">
        <h4>{{ $visit['name'] }}</h4>
        <div class="primarycolor" style="background-color:{{ $visit['primary_color'] }};border-color:{{ ($visit['primary_color'] == '#ffffff') ? $visit['secondary_color'] : $visit['primary_color'] }}"></div>
        <div class="secondarycolor" style="background-color:{{ $visit['secondary_color'] }};border-color:{{ ($visit['secondary_color'] == '#ffffff') ? $visit['primary_color'] : $visit['secondary_color'] }}"></div>
    </div>
</div>
<div class="col-md-12 modal-match-result-goals">
    <div class="col-md-6">
        <ul>
            @foreach ($scorers[0] as $scorer)
            <li>{!! $scorer !!}</li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-6">
        <ul>
            @foreach ($scorers[1] as $scorer)
            <li>{!! $scorer !!}</li>
            @endforeach
        </ul>
    </div>
</div>
<h3>Alineaciones</h3>
<div class="col-md-12 modal-match-result-formations">
    <div class="col-md-3">
        <ul>
            @for ($i = 0; $i < 18; $i++)
                @if ($i < 11)
                <li>{{ $local['formation'][$i]['short_name'] }} <span>({{ $local['formation'][$i]['number'] }})</span></li>
                @else
                <li><span>{{ $local['formation'][$i]['short_name'] }} ({{ $local['formation'][$i]['number'] }})</span></li>
                @endif
            @endfor
        </ul>
    </div>
    <div class="col-md-6">
        <img src="{{ asset('img/field-large.png') }}">
        @for ($i = 0; $i < 11; $i++)
        <div class="player-container player-container-local" style="left:{{ $local['formation'][$i]['left'] }}%;top:{{ $local['formation'][$i]['top'] }}%;background-color:rgba({{ implode(', ', $local['rgb_primary']) }}, 0.5);border-color:{{ $local['secondary_color'] }};color:{{ $local['text_color'] }};">{{ $local['formation'][$i]['number'] }}</div>
        <div class="player-container player-container-visit" style="left:{{ $visit['formation'][$i]['left'] }}%;top:{{ $visit['formation'][$i]['top'] }}%;background-color:rgba({{ implode(', ', $visit['rgb_primary']) }}, 0.5);border-color:{{ $visit['secondary_color'] }};color:{{ $visit['text_color'] }};">{{ $visit['formation'][$i]['number'] }}</div>
        @endfor
    </div>
    <div class="col-md-3">
        <ul>
            @for ($i = 0; $i < 18; $i++)
                @if ($i < 11)
                <li><span>({{ $visit['formation'][$i]['number'] }})</span> {{ $visit['formation'][$i]['short_name'] }}</li>
                @else
                <li><span>({{ $visit['formation'][$i]['number'] }}) {{ $visit['formation'][$i]['short_name'] }}</span></li>
                @endif
            @endfor
        </ul>
    </div>
</div>
<h3>Resumen</h3>
<div class="col-md-12 modal-match-result-actions">
    @foreach ($actions as $action)
    <div class="col-md-12" style="background-color:{{ $action[1] }};color:{{ $action[2] }};">
        <div class="col-md-1" style="text-align:center">{{ $action[0] }}'</div>
        <div class="col-md-11">{!! $action[3] !!}</div>
    </div>
    @endforeach
</div>
<h3>Estadísticas</h3>
<div class="col-md-12 modal-match-result-statistics">
    <div class="col-md-12">
        <div class="col-md-4 {{ $local['goals'] > $visit['goals'] ? 'best' : '' }}">{{ $local['goals'] }}</div>
        <div class="col-md-4">Goles</div>
        <div class="col-md-4 {{ $local['goals'] < $visit['goals'] ? 'best' : '' }}">{{ $visit['goals'] }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-4 {{ $local['posession'] > $visit['posession'] ? 'best' : '' }}">{{ $local['posession'] }}</div>
        <div class="col-md-4">Posesión</div>
        <div class="col-md-4 {{ $local['posession'] < $visit['posession'] ? 'best' : '' }}">{{ $visit['posession'] }}</div>
    </div>
    <div class="col-md-12">
        <div class="col-md-4 {{ $local['shots'] > $visit['shots'] ? 'best' : '' }}">{{ $local['shots'] }} ({{ $local['shots_goal'] }})</div>
        <div class="col-md-4">Disparos</div>
        <div class="col-md-4 {{ $local['shots'] < $visit['shots'] ? 'best' : '' }}">{{ $visit['shots'] }} ({{ $visit['shots_goal'] }})</div>
    </div>
</div>
<div class="clear"></div>