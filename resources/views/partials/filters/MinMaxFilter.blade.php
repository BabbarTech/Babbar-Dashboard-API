<div class="mb-3">
    <label class="form-label">{{ __('filters.' . $property . '.label') }}</label>
    <div class="input-group input-group-sm">
        <span class="input-group-text">Min</span>
        <input
            type="number"
            min="0"
           @if(isset($max)) max="{{ $max }}" @endif
           @if(isset($step)) step="{{ $step }}" @endif
           class="form-control" v-model="filters.{{ $property }}.min"
        >
        <span class="input-group-text">Max</span>
        <input
            type="number"
            min="0"
            @if(isset($max)) max="{{ $max }}" @endif
            @if(isset($step)) step="{{ $step }}" @endif
            class="form-control" v-model="filters.{{ $property }}.max"
        >
    </div>
</div>
