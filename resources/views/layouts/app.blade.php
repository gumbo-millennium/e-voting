@extends('layouts.base')

@push('html-head-end')
{{-- Scripts --}}
<script src="{{ mix('js/app.js') }}"></script>

{{-- Livewire --}}
@livewireStyles
@endpush

{{-- Warning notice --}}
@if (Config::get('app.beta'))
@push('html-body-start')
<div class="bg-red-700 text-white text-center px-4 py-2 font-bold">
    Applicatie in beta modus: Stemming en gegevens niet beveiligd.
</div>
@endpush
@endif

{{-- Deploy warning --}}
@if (Cache::get('sys.in-deploy') === true)
@push('html-body-start')
<div class="bg-orange-700 text-white text-center px-4 py-2 font-bold">
    Er wordt een nieuwe versie uitgerold, dit kan voor downtime zorgen.
</div>
@endpush
@endif

{{-- Header --}}
@push('html-body-start')
<header class="container container--md header">
    {{-- Logo --}}
    <a href="/" class="flex flex-col items-center">
        <img src="{{ mix('images/logo.svg') }}" class="h-16 flex-none" />
    </a>

    {{-- Spacer --}}
    <div class="flex-grow"></div>

    {{-- User --}}
    <div class="text-right">
        @auth
        Ingelogd als <strong>{{ Auth::user()->name }}</strong><br />
        @can('monitor')
        <a href="{{ route('monitor.index') }}">Telcommissie</a>&nbsp;
        @endcan
        @can('admin')
        <a href="{{ route('admin.index') }}">Admin</a>&nbsp;
        @endcan
        <button type="submit" form="logout" class="appearance-none underline hover:no-underline">Uitloggen</button>
        @else
        Niet ingelogd<br />
        <a href="{{ route('login') }}">Inloggen</a>
        @endauth
    </div>
</header>
@endpush

@section('html-body')
<main>
    @yield('content-before')

    <div class="container mx-auto">
        {{-- Errors --}}
        @if ($errors->any())
        <div class="notice notice--danger my-4">
            <p class="mb-4">Er is wat fout gegaan bij het versturen van de data:</p>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Messages --}}
        @if (session()->has('message'))
        <div class="notice notice--warning my-4">
            <p>{{ session()->pull('message') }}</p>
        </div>
        @endif

        {{-- Debug messages --}}
        @if (Config::get('app.beta') && session()->has('debug-message'))
        <div class="notice notice--warning my-4">
            <strong class="text-yellow-700 font-bold block text-sm uppercase">Debug message</strong>
            <p>{{ session()->pull('debug-message') }}</p>
        </div>
        @endif

        {{-- Title --}}
        @section('content')
        <div class="p-8 bg-red-100 border border-red-800 rounded text-center">
            <p>Something went wrong</p>
        </div>
        @show
    </div>
    @yield('content-after')
</main>
@endsection

@push('html-body-end')
@include('layouts.parts.footer')
@endpush

@push('html-body-end')
{{-- Logout form --}}
<form action="{{ route('logout') }}" method="post" id="logout">@csrf</form>

{{-- Livewire --}}
@livewireScripts
@endpush
