<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }}, focused: false }">
        <div class="rounded flex flex-row align-middle gap-5" :class="focused ? `border-primary-500` : `border-primary-50`">
            <input type="range" x-model="state" min="{{ $minValue }}" max="{{ $maxValue }}" class="flex-grow m-2" />
            <input type="text" x-model="state" min="{{ $minValue }}" max="{{ $maxValue }}" class="rounded bg-primary-500 p-2 text-black text-xl text-center w-16" />
        </div>
    </div>
</x-dynamic-component>
