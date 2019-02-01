@php
    $lang = str_replace('_', '-', app()->getLocale());
@endphp

<!doctype html>
<html lang="{{$lang}}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>@yield('title') - Journey66</title>
        @stack('meta')

        <!-- Load JS Plugin-->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.23/moment-timezone-with-data.js"></script>
        
        <!-- Load Bootstrap-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

        <!-- Load Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

        <!-- Load Custom Scripts & Stylesheet-->
        <link rel="stylesheet" href="/css/common.css">
        <script src="/js/journey66.js"></script>
        @stack('scripts')
        @stack('css')
        @stack('styles')

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Load Font By Language -->
        @switch($lang)
            @case('ko')
                <link href="https://fonts.googleapis.com/css?family=Noto+Serif+KR" rel="stylesheet">
                <style>body{font-family: 'Daehan', serif; !important} </style>
                @break
            @case('en')
                
                @break
            @default
        @endswitch

    </head>
    <body>
        @yield('contents')
    </body>
</html>