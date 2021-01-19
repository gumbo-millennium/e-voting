@extends('layouts.base')

@section('html-body')
<main class="container container--md">
    <a href="{{ url('/') }}" class="flex flex-row items-center">
        <img src="{{ mix('images/logo.svg') }}" class="h-32 flex-none mx-auto mt-16 mb-4">
    </a>

    <div class="h-64"></div>

    <h1 class="font-title font-normal text-3xl mb-4">
        502 <span class="font-bold">Bad Gateway</span>
    </h1>

    <p class="text-xl mb-4 leading-relaxed">
        Oh no, het lijkt er op dat de e-voting server die je wilt benaderen nog aan het opstarten is.
    </p>

    <p class="leading-relaxed">
        Probeer het met een paar seconden nog een keer.
    </p>
</main>
@endsection

@push('html-body-end')
@include('layouts.parts.footer')
@endpush
