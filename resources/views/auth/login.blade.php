@extends('layouts.app')

@section('headmeta')
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="{{ date('D, d M Y H:i:s e') }}" />
<meta http-equiv="pragma" content="no-cache" />
@endsection

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>@lang('labels.login')</h1>
        <form>
            <div class="inputwrapper">
                <select name="language" id="select-language">
                    @foreach($supported_languages AS $key => $language)
                    <option value="{{ $key }}" {{ $current_language == $key ? 'selected' : '' }}>@lang('labels.' . $language['label'])</option>
                    @endforeach
                </select>
            </div>
        </form>
        <form id="login" action="{{ route('login') }}" method="POST" role="form">
            {{ csrf_field() }}
            <div class="inputwrapper">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="@lang('labels.email_address')" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input id="password" type="password" class="form-control" name="password" placeholder="@lang('labels.password')" required>
                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">@lang('labels.enter')</button>
            </div>
            <div class="inputwrapper">
                <label class="pull-left"><input type="checkbox" class="remember" name="remember" {{ old('remember', true) ? 'checked' : '' }} /> @lang('labels.remember_me')</label>
            </div>
            <div class="inputwrapper col-xs-12">
                <div class="pull-right">@lang('labels.arent_you_member') <a href="{{ route('register') }}" class="btn btn-danger">@lang('labels.affiliate')</a></div>
            </div>
            <div class="inputwrapper col-xs-12">
                <div class="pull-right">@lang('labels.forgot_your_password') <a href="{{ route('password.request') }}">@lang('labels.recover_f')</a></div>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">@lang('labels.contact_us')</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection