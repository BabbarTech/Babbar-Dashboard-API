<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>

    <div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="filter-{{ $property }}-radio-null" v-model="filters.{{ $property }}" :value="null">
            <label class="form-check-label" for="filter-{{ $property }}-radio-null">{{ $anyLabel ?? __('Any') }}</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="filter-{{ $property }}-radio-yes" v-model="filters.{{ $property }}" value="1">
            <label class="form-check-label" for="filter-{{ $property }}-radio-yes">{{ $trueLabel ?? __('Yes') }}</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="filter-{{ $property }}-radio-no" v-model="filters.{{ $property }}" value="0">
            <label class="form-check-label" for="filter-{{ $property }}-radio-no">{{ $falseLabel ?? __('No') }}</label>
        </div>
    </div>
</div>
