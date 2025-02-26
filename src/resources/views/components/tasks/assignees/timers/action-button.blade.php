<button {{ $attributes->merge([ 'class' => "flex justify-center items-center rounded-full"]) }}  >
    {{ $slot }}
</button>