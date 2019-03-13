@extends('layouts.inner')

@section('content-inner')
<div class="form-group">
    <label class="col-md-3 control-label">@lang('labels.team_name')</label>
    <div class="col-md-9">{{ $_team['name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">@lang('labels.team_short_name')</label>
    <div class="col-md-9">{{ $_team['short_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">@lang('labels.stadium_name')</label>
    <div class="col-md-9">{{ $_team['stadium_name'] }}</div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">@lang('labels.colors')</label>
    <div class="col-md-9">
        <div class="col-md-2 teamprimarycolor" style="background-color:{{ $_team['primary_color'] }};border-color:{{ ($_team['primary_color'] == '#ffffff') ? $_team['secondary_color'] : $_team['primary_color'] }}"></div>
        <div class="col-md-2 teamsecondarycolor" style="background-color:{{ $_team['secondary_color'] }};border-color:{{ ($_team['secondary_color'] == '#ffffff') ? $_team['primary_color'] : $_team['secondary_color'] }}"></div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">@lang('labels.shield')</label>
    <div class="col-md-9"><img class="svg" src="{{ $_team['shieldFile'] }}"  data-color-primary="{{ $_team['primary_color'] }}" data-color-secondary="{{ $_team['secondary_color'] }}" style="width:70px;"></div>
</div>
<div class="form-group">
    <a href="{{ route('team.edit') }}" class="btn btn-default">@lang('labels.edit_team')</a>
</div>
@endsection
