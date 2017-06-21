@extends('layouts.admin')

@section('styles-inner')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/bootstrap-timepicker.min.css') }}" type="text/css" />
@endsection

@section('javascript-inner')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        startDate: '0d',
        autoclose: true,
    });

    $('.timepicker').timepicker({
        showMeridian: false
    });

    tinymce.init({
        selector: 'textarea',
        theme: 'modern',
        height: 300,
        menubar: false,
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough',
    });
});
</script>
@endsection

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Crear Mensaje</h3>
    @if ($editing)
    <form method="POST" action="{{ route('admin.message.save', [$_domain, $message['id']]) }}" class="form-horizontal" role="form">
    @else
    <form method="POST" action="{{ route('admin.message.store', $_domain) }}" class="form-horizontal" role="form">
    @endif
        {{ csrf_field() }}
        @if ($editing)
        <input type="hidden" name="_method" value="PATCH">
        @endif
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="valid-from" class="col-md-2 control-label">Válido desde</label>
            <div class="col-md-10">
                <div class="input-group bootstrap-timepicker">
                    <input class="datepicker form-control" type="text" name="from_date" style="float:left; width:100px;" value="{{ old('from_date', $message['from_date']) }}" />
                    <input class="timepicker form-control" type="text" name="from_time" style="float:left; width:100px;" value="{{ old('from_time', $message['from_time']) }}" />
                </div>
                @if ($errors->has('valid_from'))
                <label class="error">
                    <strong>{{ $errors->first('valid_from') }}</strong>
                </label>
                @endif
            </div>
            <label for="valid-to" class="col-md-2 control-label">Válido hasta</label>
            <div class="col-md-10">
                <div class="input-group bootstrap-timepicker">
                    <input class="datepicker form-control" type="text" name="to_date" style="float:left; width:100px;" value="{{ old('to_date', $message['to_date']) }}" />
                    <input class="timepicker form-control" type="text" name="to_time" style="float:left; width:100px;" value="{{ old('to_time', $message['to_time']) }}" />
                </div>
                @if ($errors->has('valid_to'))
                <label class="error">
                    <strong>{{ $errors->first('valid_to') }}</strong>
                </label>
                @endif
            </div>
            <label for="title" class="col-md-2 control-label">Título</label>
            <div class="col-md-10">
                <input type="text" class="form-control input-default" name="title" value="{{ old('title', $message['title']) }}" required />
                @if ($errors->has('title'))
                <label class="error">
                    <strong>{{ $errors->first('title') }}</strong>
                </label>
                @endif
            </div>
            <label for="valid-from" class="col-md-2 control-label">Mensaje</label>
            <div class="col-md-10">
                <textarea name="message" class="form-control input-default" required>{{ old('message', $message['message']) }}</textarea>
                @if ($errors->has('message'))
                <label class="error">
                    <strong>{{ $errors->first('message') }}</strong>
                </label>
                @endif
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <button type="submit" class="btn btn-default">{{ $editing ? 'Guardar' : 'Crear' }}</button>
            </div>
        </div>
    </form>
</div>
@endsection
