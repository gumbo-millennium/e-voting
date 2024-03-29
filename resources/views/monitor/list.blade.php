@extends('layouts.app')

<?php
$lastDate = null;
?>

@section('content')
<h1 class="font-title font-bold text-2xl">
    Telling controle
</h1>

<p class="text-lg mb-4">
    Hieronder staan afgeronde tellingen in omgekeerd-chronologische volgorde (meest recent gesloten bovenaan)
</p>

@forelse ($polls as $poll)
@if ($lastDate !== $poll->ended_at->format('Y-m-d HH') && $lastDate = $poll->ended_at->format('Y-m-d HH'))
<p class="p-4 text-center text-gray-600">
    {{ $poll->ended_at->isoFormat('dddd DD MMMM YYYY, HH:mm') }}
</p>
@endif
<livewire:monitor-result :poll="$poll" :approval="$approvals->get($poll->id)" :key="$poll->id" />
@empty
<div class="notice notice--info">
    Er zijn geen voorstellen recentelijk gesloten
</div>
@endforelse

@endsection
