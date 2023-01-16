<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }"
         class="flex flex-wrap gap-4 items-center justify-start">
        <x-filament::button wire:click="dispatchFormEvent('sendEventToTelegramChannel')">
            {{ __("Repost event") }}
        </x-filament::button>
        <x-filament::button wire:click="dispatchFormEvent('resendEventToTelegramChannel')">
            {{ __("Delete event and post again") }}
        </x-filament::button>
    </div>
</x-dynamic-component>
