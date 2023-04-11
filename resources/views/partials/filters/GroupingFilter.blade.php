<div class="mb-3">
    <div>
        <div class="form-check form-switch">
            <input class="form-check-input me-2" type="checkbox" role="switch" id="filter-{{ $property }}-switch" v-model="filters.{{ $property }}" value="1">
            <label class="form-check-label" for="filter-{{ $property }}-switch">{{ __('filters.' . $property . '.label') }}</label>
        </div>
    </div>
</div>
