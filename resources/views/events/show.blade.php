<x-app-layout :model="$entity">
    <div>
        @livewire('single-event', ['entity' => $entity])
    </div>
</x-app-layout>
