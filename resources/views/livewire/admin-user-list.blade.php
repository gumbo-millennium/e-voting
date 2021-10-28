<div>
    {{-- Render votes --}}
    <div class="number-grid mb-4" wire:poll.5s>
        <div class="number-grid__tile">
            <data class="number-grid__number">{{ $scores->presentVoters }}</data>
            <small class="number-grid__label">aanwezige stemgerechtigden</small>
        </div>
        <div class="number-grid__separator">+</div>
        <div class="number-grid__tile">
            <data class="number-grid__number">{{ $scores->presentProxies }}</data>
            <small class="number-grid__label">aanwezigen met machtiging</small>
        </div>
        <div class="number-grid__separator">=</div>
        <div class="number-grid__tile">
            <data class="number-grid__number">{{ $scores->totalVotes }}</data>
            <small class="number-grid__label">totaal stemmen</small>
        </div>
    </div>

    {{-- Render search --}}
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="member-search" class="block mr-2 flex-none w-24">Zoeken</label>
        <input wire:model="search" id="member-search" type="search" autocomplete="off" autofocus class="form-input flex-grow"
            placeholder="Doorzoeken" />
    </div>

    {{-- Render fitlers --}}
    @if (empty($search))
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="search-filter" class="block mr-2 flex-none w-24">Filter</label>
        <select class="form-select flex-grow" id="search-filter" wire:model="filter">
            <option value="can-vote-present">Alleen aanwezig stemgerechtigd</option>
            <option value="can-vote">Alleen stemgerechtigd</option>
            <option value="proxy">Machtiging afgegeven</option>
            <option value="is-proxy">Gemachtigd</option>
            <option value="present">Aanwezig</option>
            <option value="all">Niet filteren</option>
        </select>
    </div>
    @endif

    {{-- Render users --}}
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 border-b border-b-gray-300 text-left p-2">
                <th class="p-2">Naam</th>
                <th class="p-2">Rechten</th>
                <th class="p-2">Aanwezig</th>
                <th class="p-2">Machtiging</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->users as $user)
            <livewire:admin-user :user="$user" :key="$user->id" />
            @empty
            <tr>
                <td class="py-2" colspan="4">
                    <div class="notice notice--info">
                        Er zijn geen leden die voldoen aan de zoekresultaten
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Links --}}
    <div class="mt-4 flex items-center justify-between">
        {{ $this->users->links() }}
    </div>

    {{-- Legend --}}
    <div class="mt-4">
        <h2 class="font-title font-bold text-lg mb-2">Legenda</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-2">
            <div class="flex flex-col gap-y-2">
                {{-- Stemrecht --}}
                <div class="flex items-center">
                    <x-fas-vote-yea class="h-4 text-brand-800 mr-4"  />
                    <p>Heeft stemrecht</p>
                </div>

                <div class="flex items-center">
                    <x-fas-vote-yea class="h-4 text-orange-800 mr-4" />
                    <p>Heeft iemand gemachtigd</p>
                </div>

                <div class="flex items-center">
                    <x-fas-vote-yea class="h-4 text-gray-200 mr-4" />
                    <p>Heeft geen stemrecht</p>
                </div>
            </div>

            <div class="flex flex-col gap-y-2">
                {{-- Machtiging --}}
                <div class="flex items-center">
                    <x-fas-exchange-alt class="h-4 text-brand-800 mr-4"  />
                    <p>Kan machtiging afgeven</p>
                </div>

                <div class="flex items-center">
                    <x-fas-exchange-alt class="h-4 text-gray-200 mr-4" />
                    <p>Kan geen machtiging afgeven</p>
                </div>
            </div>

            <div class="flex flex-col gap-y-2">
                {{-- Board --}}
                <div class="flex items-center">
                    <x-fas-crown class="h-4 text-brand-800 mr-4"  />
                    <p>Bestuurslid</p>
                </div>

                <div class="flex items-center">
                    <x-fas-crown class="h-4 text-gray-200 mr-4" />
                    <p>Geen bestuurslid</p>
                </div>
            </div>

            <div class="flex flex-col gap-y-2">
                {{-- Telcommissie --}}
                <div class="flex items-center">
                    <x-fas-binoculars class="h-4 text-brand-800 mr-4"  />
                    <p>Telcommissie</p>
                </div>

                <div class="flex items-center">
                    <x-fas-binoculars class="h-4 text-gray-200 mr-4" />
                    <p>Geen telcommissie</p>
                </div>
            </div>
        </div>
    </div>
</div>
