<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>{{ $title or 'Futbolin' }}</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}" type="text/css" />
        @yield('styles')

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ asset('js/html5shiv.js') }}"></script>
        <script src="{{ asset('js/respond.min.js') }}"></script>
        <![endif]-->
    </head>
    <body {!! $bodyclass or '' !!} {!! $bodystyle or '' !!}>
        @yield('content')
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('javascript')
    </body>
</html>
