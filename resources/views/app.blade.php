<!-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        @env ('local')
            <script src="http://localhost:8080/js/bundle.js"></script>
        @endenv
    </body>
</html> -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>HIMS</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css" async/>
        <link rel="stylesheet" href="{{ asset('uikit/css/uikit.min.css') }}" async>
        <link href="{{ asset('css/hims.css') }}" rel="stylesheet" async>
    </head>
    <body>
        <div id="app">
        </div>
    </body>

</html>
<style>

</style>
<script src="{{ asset('uikit/js/uikit.min.js') }}" defer></script>
<!-- <script src="{{ asset('uikit/js/uikit-icons.min.js') }}" defer></script> -->
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.15/dist/js/uikit-icons.min.js" defer></script>
<script src="{{ asset('js/app.js') }}" defer></script>
<!-- <script src="{{ asset('js/myscript.js') }}" defer></script> -->