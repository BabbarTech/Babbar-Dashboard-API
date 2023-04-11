<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>

    <select class="form-select form-select-sm" v-model="filters.{{ $property }}">
        <option :value="null">...</option>
        @foreach($options as $key => $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>
</div>
