<x-form.field>
    <x-form.label name="{{ $name }}" label="{{ $label }}" />
    <input
        id="{{ 'input_' . $sanitizedName }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        aria-describedby="{{ $sanitizedName }}_HelpBlock"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name) ?? false]) }}
    />
    <x-form.help :help="$help" />
    <x-form.error name="{{ $name }}" />
</x-form.field>
