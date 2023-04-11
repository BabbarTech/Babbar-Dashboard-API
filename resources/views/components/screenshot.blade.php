<screenshot
    {{ $attributes->merge(['class' => 'btn']) }}
    v-cloak
    capture-content-id="{{ $capture }}"
    filename="{{ $filename }}"
    :current="datasetCollectionStore.datasets?.main?.selected"
>
    {{ $slot }}
</screenshot>
