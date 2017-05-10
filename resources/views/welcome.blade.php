<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}" type="text/css" />

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ asset('js/html5shiv.js') }}"></script>
        <script src="{{ asset('js/respond.min.js') }}"></script>
        <![endif]-->
    </head>
    <body class="loginpage" style="background-image:url({{ asset('img/back/' . $back) }});">
        <div class="loginpanel">
            <div class="loginpanelinner">
                <form id="login" action="{{ route('login') }}" method="POST" role="form">
                    {{ csrf_field() }}
                    <div class="inputwrapper">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your e-mail" class="form-control" required autofocus>
                        @if ($errors->has('email'))
                        <label class="error">
                            <strong>{{ $errors->first('email') }}</strong>
                        </label>
                        @endif
                    </div>
                    <div class="inputwrapper">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Enter your password" required>
                        @if ($errors->has('password'))
                        <label class="error">
                            <strong>{{ $errors->first('password') }}</strong>
                        </label>
                        @endif
                    </div>
                    <div class="inputwrapper">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                    <div class="inputwrapper">
                        <div class="pull-right">¿No es miembro? <a href="{{ route('register') }}">Afiliarse</a></div>
                        <label><input type="checkbox" class="remember" name="remember" {{ old('remember') ? 'checked' : '' }} /> Recordarme</label>
                    </div>
                    <div class="inputwrapper">
                        <div class="pull-right">¿Olvidó su contraseña? <a href="{{ route('password.request') }}">Recuperarla</a></div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
