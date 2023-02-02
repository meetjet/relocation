<div class="pb-8">
    <x-filament::header class="mb-6">
        <x-slot name="heading">
            {{ __("Add event heading") }}
        </x-slot>
        <x-slot name="subheading">
            {{ __("Add event subheading") }}
        </x-slot>
    </x-filament::header>

    <x-filament::form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit" form="submit">
            {{ __("Submit") }}
        </x-filament::button>
    </x-filament::form>
</div>
