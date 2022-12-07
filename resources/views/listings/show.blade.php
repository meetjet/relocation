<x-app-layout :model="$entity">
    <div>
        @livewire('single-listings', ['entity' => $entity])
    </div>
</x-app-layout>
