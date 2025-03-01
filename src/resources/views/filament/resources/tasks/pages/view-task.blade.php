<x-filament-panels::page>
    <div class="flex justify-center">
        <div class="flex-grow max-w-xl">
            <livewire:tasks.assignees.timer :task="$record" :key="$record->id" />
        </div>
    </div>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        {{ $this->form }}
    @endif
 
    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$this->activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>