<div>
    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button type="submit" class="my-3" icon="heroicon-m-sparkles">
            Create
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
