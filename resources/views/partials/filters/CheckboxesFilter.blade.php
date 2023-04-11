<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>
    <div style="columns: 3; -webkit-columns: 3; -moz-columns: 3;">
        @foreach($options as $key => $option)
            <div class="form-check form-switch">
                <input class="form-check-input me-2"
                       type="checkbox"
                       id="filter-{{ $property }}-option-{{ $option['value'] }}"
                       v-model="filters.{{ $property }}"
                       value="{{ $option['value'] }}"
                >
                <label class="form-check-label"
                       for="filter-{{ $property }}-option-{{ $option['value'] }}"
                >
                    {{ $option['label'] }}
                </label>
            </div>
        @endforeach
    </div>
</div>
