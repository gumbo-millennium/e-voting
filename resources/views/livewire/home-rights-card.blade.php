<div wire:poll.60s>
    <details class="notice">
        <summary class="flex items-center cursor-pointer">
            @can('vote')
            <x-fas-check-circle class="h-8 mr-4" />

            <strong class="flex-grow">Je mag stemmen</strong>
            @else
            <x-fas-exclamation-circle class="h-8 mr-4 text-orange-700" />

            <strong class="flex-grow text-orange-700">Je mag niet stemmen</strong>
            @endcan

            <x-fas-chevron-down class="h-4 mx-2 hide-if-open" />
            <x-fas-chevron-up class="h-4 mx-2 hide-if-closed" />
        </summary>

        <div class="mt-4">
            <ul class="ml-2 list-none space-y-4">
                {{-- Role form --}}
                <li class="flex items-start">
                    @if ($user->is_voter)
                    <x-fas-check class="h-4 mr-2 mt-1" role="none" />
                    <div>
                        Jouw lidmaatschapsvorm ({{ $user->group ?? 'niet lid' }}) heeft stemrecht.

                        @if ($user->is_admin)
                        <p class="text-sm text-gray-600 flex-none">Jouw bestuursfunctie kan impact hebben op deze uitslag</p>
                        @endif
                    </div>
                    @else
                    <x-fas-times class="h-4 mr-2" role="none" />
                    Jouw lidmaatschapsvorm ({{ $user->group ?? 'niet lid' }}) heeft geen stemrecht.
                    @endif
                </li>

                {{-- Present --}}
                <li class="flex items-center">
                    @if ($user->is_present)
                    <x-fas-check class="h-4 mr-2" role="none" />
                    Je bent aangemeld voor de ALV.
                    @else
                    <x-fas-times class="h-4 mr-2" role="none" />
                    Je bent nog niet aangemeld voor de ALV.
                    @endif
                </li>

                {{-- Assigned Proxy --}}
                <li class="flex items-center">
                    @if ($user->proxy)
                    <x-fas-times class="h-4 mr-2" role="none" />
                    Je hebt een machtiging afgegeven aan <strong>{{ $user->proxy->name }}</strong>.
                    @else
                    <x-fas-check class="h-4 mr-2" role="none" />
                    Je hebt geen machtiging afgegeven.
                    @endif
                </li>

                {{-- Received Proxy --}}
                @if ($user->proxyFor)
                <li class="flex items-center">
                    <x-fas-check class="h-4 mr-2" role="none" />
                    Je bent gemachtigd door <strong>{{ $user->proxyFor->name }}</strong>.
                </li>
                @endif
            </ul>
        </div>

    </details>
</div>
