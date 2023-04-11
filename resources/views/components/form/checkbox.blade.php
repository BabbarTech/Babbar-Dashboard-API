<x-form.field>
    <div class="form-check">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="{{ $value }}"
            aria-describedby="{{ $sanitizedName }}_HelpBlock"
            {{ $attributes->class(['form-check-input', 'is-invalid' => $errors->has($sanitizedName) ?? false]) }}
        />
        <label for="{{ $attributes->get('id') ?? 'input_' . $sanitizedName }}" class="form-check-label">{{ $label }}</label>
        <x-form.help :help="$help" />
        <x-form.error name="{{ $sanitizedName }}" />
    </div>
</x-form.field>
