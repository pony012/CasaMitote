<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="" content="">
        <title>Prueba de concepto</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(File::exists(base_path('public/css/'.Route::currentRouteName().'.bundle.css')))
            <link href="{{ asset('css/'.Route::currentRouteName().'.bundle.css') }}" rel="stylesheet">
        @else
            <script src="{{ asset('css/example.bundle.css') }}"></script>
        @endif
    </head>
    <body>
        <main class="container">
            <div class="row">
                @yield('content')
            </div>
        </main>
        @if(File::exists(base_path('public/js/'.Route::currentRouteName().'.bundle.js')))
            <script src="{{ asset('js/'.Route::currentRouteName() . '.bundle.js') }}"></script>
        @else
            <script src="{{ asset('js/example.bundle.js') }}"></script>
        @endif
    </body>
</html>
