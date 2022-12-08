<x-app-layout :model="$entity">
    <div>
        @livewire('single-faq', ['entity' => $entity])
    </div>
</x-app-layout>
