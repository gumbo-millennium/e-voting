@extends('layouts.base')

@php
$user = request()->user();
@endphp

@section('content')
<h1 class="font-title font-bold text-2xl">
    Welkom bij Gumbo Millennium <span class="font-normal">e-voting</span>
</h1>

<p class="text-lg mb-4">
    Hieronder zie je de stemmingen die op dit moment open zijn.
</p>

{{-- Notice if proxied --}}
@cannot('vote')
<div class="notice notice--warning">
    <strong class="notice__title">Je mag niet stemmen</strong>
@if (!$user->is_voter)
    Je hebt geen stemrecht op deze ALV.
@elseif ($user->is_voter && !$user->is_present)
    Je bent niet aangemeld, meld je eerst aan bij het bestuur.
@else
    We weten niet waarom
@endif
</div>
@endcan

{{-- render polls --}}
@forelse ($polls as $poll)
<livewire:poll-vote-card :poll="$poll" />
@empty
<div class="notice notice--info">
    Er zijn momenteel geen actieve stemmingen.
</div>
@endforelse

{{-- done --}}
@endsection