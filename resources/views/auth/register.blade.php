@extends('layouts.app')

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>@lang('labels.register')</h1>
        <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}
            <div class="inputwrapper{{ $errors->has('language') ? ' has-error' : '' }}">
                <select name="language">
                    @foreach($supported_languages AS $key => $language)
                    <option value="{{ $key }}" {{ $current_language == $key ? 'selected' : '' }}>@lang('labels.' . $language['label'])</option>
                    @endforeach
                </select>
            </div>
            <div class="inputwrapper{{ $errors->has('first_name') ? ' has-error' : '' }}">
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="@lang('labels.first_name')" class="form-control" required autofocus />

                @if ($errors->has('first_name'))
                    <label class="error">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('last_name') ? ' has-error' : '' }}">
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="@lang('labels.last_name')" class="form-control" required />

                @if ($errors->has('last_name'))
                <label class="error">
                    <strong>{{ $errors->first('last_name') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('email') ? ' has-error' : '' }}">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="@lang('labels.email_address')" class="form-control" required />

                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" name="password" placeholder="@lang('labels.password')" class="form-control" required />

                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="password-confirm" type="password" name="password_confirmation" placeholder="@lang('labels.confirm_password')" class="form-control" required>
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">@lang('labels.register')</button>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">@lang('labels.contact_us')</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection
