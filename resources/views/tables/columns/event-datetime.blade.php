@php
    $event = $getState();

    $startDatetime = $event->start_date ? $event->start_date->translatedFormat("j M Y") : null;

    if ($startDatetime && $event->start_time) {
        $startDatetime .= ', ' . $event->start_time->translatedFormat("H:i");
    }

    $finishDatetime = $event->finish_date ? $event->finish_date->translatedFormat("j M Y") : null;

    if ($finishDatetime && $event->finish_time) {
        $finishDatetime .= ', ' . $event->finish_time->translatedFormat("H:i");
    }
@endphp

<div class="filament-tables-text-column px-4 py-3">
    @if($startDatetime)
        <div class="flex items-center space-x-1">
            <x-heroicon-o-calendar class="w-5 h-5 text-success-700"/>
            <div>{{ $startDatetime }}</div>
        </div>
    @endif

    @if($finishDatetime)
        <div class="flex items-center space-x-1">
            <x-heroicon-o-calendar class="w-5 h-5 text-primary-700"/>
            <div>{{ $finishDatetime }}</div>
        </div>
    @endif
</div>
