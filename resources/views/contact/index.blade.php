@extends('layouts.app')

@section('content')
<div class="loginpanel" style="width:500px;">
    <div class="loginpanelinner">
        <h1>@lang('labels.contact')</h1>
        <form id="contact" action="{{ route('contact') }}" method="POST" role="form">
            {{ csrf_field() }}
            <div class="inputwrapper">
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="@lang('labels.full_name')" class="form-control" required autofocus>
                @if ($errors->has('name'))
                <label class="error">
                    <strong>{{ $errors->first('name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="@lang('labels.email_address')" class="form-control" required>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                @if ($errors->has('message'))
                <label class="error">
                    <strong>{{ $errors->first('message') }}</strong>
                </label>
                @endif
                <textarea id="message" name="message" placeholder="@lang('labels.message')" class="form-control" style="height:100px;" required>{{ old('message') }}</textarea>
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">@lang('labels.send')</button>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection