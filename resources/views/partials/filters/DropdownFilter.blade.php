<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>

    <dropdown
        class=""
        v-model="filters.{{ $property }}"
        :multiple="{{ $multiple ?? 'false' }}"
        :taggable="{{ $taggable ?? 'false' }}"
        :options="{{ $options }}"
    >
    </dropdown>
</div>
