<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @stack('html-head-meta')

    <title>{{ $title ?? 'Gumbo Millennium e-voting' }}</title>

    {{-- Stylesheet --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{-- Additionals --}}
    @stack('html-head-end')
</head>

<body>
    @stack('html-body-start')

    @yield('html-body')

    @stack('html-body-end')
</body>

</html>
