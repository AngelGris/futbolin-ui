@extends('layouts.admin')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    var groups = {{ $tournament['groups'] }};
    $('#sel_categories').change(function() {
        zones = parseInt(groups / $(this).val());
        if (groups % $(this).val()) {
            zones++;
        }
        $('#span_zones').text(zones);
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Crear Torneo</h3>
    <p>Equipos: {{ $tournament['teams'] }} - Sparrings: {{ $tournament['sparrings'] }} - Grupos: {{ $tournament['groups'] }}</p>
    <form method="POST" action="{{ route('admin.tournament.store', $domain) }}" class="form-horizontal" role="form">
        {{ csrf_field() }}
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-2 control-label">Nombre del torneo</label>
            <div class="col-md-10">
                <input type="text" class="form-control input-default" name="name" value="{{ old('name', $tournament['name']) }}" required />
                @if ($errors->has('name'))
                <label class="error">
                    <strong>{{ $errors->first('name') }}</strong>
                </label>
                @endif
            </div>
            <label for="name" class="col-md-2 control-label">Categorías</label>
            <div class="col-md-10">
                <select name="categories" id="sel_categories">
                    @for ($i = 1; $i <= $tournament['groups']; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <label for="name" class="col-md-2 control-label">Zonas</label>
            <div class="col-md-10">
                <span id="span_zones">{{ $tournament['groups'] }}</span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <button type="submit" class="btn btn-default">Crear</button>
            </div>
        </div>
    </form>
</div>
@include('modules.modals.shieldselect')
@endsection
