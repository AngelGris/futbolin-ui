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
        <form action="{{ route('password.email') }}" method="POST" role="form">
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
                <button type="submit" class="btn btn-primary">@lang('labels.send_email')</button>
            </div>
            <div class="inputwrapper col-xs-12">
                <a href="{{ route('contact') }}" style="color:#fff;">@lang('labels.contact_us')</a>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
@endsection
