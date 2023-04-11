<div class="card h-100">
    <div class="card-body">
        {{ $slot }}
    </div>
    @if(!$isDone)
    <div class="card-footer text-end">
        <x-form-button
            :disabled="$btnDisabled"
            action="{{ $action }}"
            method="get" class="btn btn-primary btn-sm"
        >
            {{ $btnLabel }}
        </x-form-button>
    </div>
    @endif
</div>
