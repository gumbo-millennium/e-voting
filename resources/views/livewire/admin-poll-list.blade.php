<div>
    {{-- Render search --}}
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="member-search" class="block mr-2 flex-none w-24">Zoeken</label>
        <input wire:model="search" id="member-search" type="search" autocomplete="off" autofocus
            class="form-input flex-grow" placeholder="Doorzoeken" />
    </div>

    {{-- Render fitlers --}}
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="search-filter" class="block mr-2 flex-none w-24">Filter</label>
        <select class="form-select flex-grow" id="search-filter" wire:model="filter">
            <option value="recent">Afgelopen 24 uur bewerkt</option>
            <option value="complete">Alleen afgerond</option>
            <option value="closed">Alleen gesloten</option>
            <option value="open">Alleen open</option>
            <option value="concepts">Alleen concepten</option>
            <option value="all">Niet filteren</option>
        </select>
    </div>

    {{-- Render users --}}
    <div class="mb-4">
        @php($lastDate = null)

        @forelse ($this->polls as $poll)

        @if ($lastDate !== $poll->created_at->format('Y-m-d HH') && $lastDate = $poll->created_at->format('Y-m-d HH'))
        <p class="p-4 text-center text-gray-600">
            {{ $poll->created_at->isoFormat('dddd DD MMMM YYYY, HH:mm') }}
        </p>
        @endif

        <livewire:admin-poll :poll="$poll" :key="$poll->id" />
        @empty
        <div class="notice notice--info">
            Er zijn geen voorstellen die voldoen aan de zoekresultaten
        </div>
        @endforelse
    </div>
</div>
