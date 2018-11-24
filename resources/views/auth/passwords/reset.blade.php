@extends('layouts.app')

@section('content')
<div class="loginpanel">
    <div class="loginpanelinner">
        <h1>@lang('labels.recover_password')</h1>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('password.request') }}" method="POST" role="form">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="inputwrapper">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="@lang('labels.email_address')" class="form-control" required autofocus>
                @if ($errors->has('email'))
                <label class="error">
                    <strong>{{ $errors->first('email') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input type="password" class="form-control" name="password" placeholder="@lang('labels.password')" required>
                @if ($errors->has('password'))
                <label class="error">
                    <strong>{{ $errors->first('password') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <input type="password" class="form-control" name="password_confirmation" placeholder="@lang('labels.confirm_password')" required>
                @if ($errors->has('password_confirmation'))
                <label class="error">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </label>
                @endif
            </div>
            <div class="inputwrapper">
                <button type="submit" class="btn btn-primary">@lang('labels.change_password')</button>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">@lang('labels.contact_us')</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection
