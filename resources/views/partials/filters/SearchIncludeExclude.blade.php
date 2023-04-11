<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>
    <div class="input-group input-group-sm">
        <span class="input-group-text">{{ __('Include') }}</span>
        <input
            type="text"
            class="form-control" v-model="filters.{{ $property }}.include"
        >
        <span class="input-group-text">{{ __('Exclude') }}</span>
        <input
            type="text"
            class="form-control" v-model="filters.{{ $property }}.exclude"
        >
    </div>
</div>
