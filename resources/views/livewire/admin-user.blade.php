<?php
$voteColor = 'text-gray-200';
if ($user->is_voter && $user->proxy !== null) {
    $voteColor = 'text-orange-800';
} elseif ($user->is_voter) {
    $voteColor = 'text-brand-800';
}

$proxyColor = $user->can_proxy ? 'text-brand-800' : 'text-gray-200';
$adminColor = $user->can_admin ? 'text-brand-800' : 'text-gray-200';
$monitorColor = $user->is_monitor ? 'text-brand-800' : 'text-gray-200';
?>
<tr>
    <td class="py-2"><a href="{{ route('admin.users.show', compact('user')) }}">{{ $user->name }}</a></td>
    <td class="py-2">
        <div class="flex items-center space-x-2">
            <x-fas-vote-yea class="h-4 {{ $voteColor }}" title="Stemrecht" />
            <x-fas-exchange-alt class="h-4 {{ $proxyColor }}" title="Kan machtiging afgeven" />
            <x-fas-crown class="h-4 {{ $adminColor }}" title="Bestuur" />
            <x-fas-binoculars class="h-4 {{ $monitorColor }}" title="Telcommissie" />
        </div>
    </td>
    <td class="py-2">
        <div class="flex">
            <div class="min-w-[3rem]">
                {{ $user->is_present ? 'Ja' : 'Nee' }}
            </div>

            @can('setPresent', $user)
            <button class="appearance-none ml-2 text-yellow-600" wire:click.prevent="setPresent({{ $user->is_present ? 'false' : 'true' }})">⇄</button>
            @endcan
        </div>
    </td>
    @if ($user->proxyFor)
    <td class="py-2">
        Van <a href="{{ route('admin.users.show', ['user' => $user->proxyFor]) }}">{{ $user->proxyFor->name }}</a>
    </td>
    @elseif ($user->proxy)
    <td class="py-2">
        Aan <a href="{{ route('admin.users.show', ['user' => $user->proxy]) }}">{{ $user->proxy->name }}</a>
    </td>
    @else
    <td class="py-2">&mdash;</td>
    @endif
</tr>
