<x-form.field>
    <x-form.label name="{{ $name }}" label="{{ $label }}" />
    <textarea
        id="{{ 'input_' . $sanitizedName }}"
        name="{{ $name }}"
        aria-describedby="{{ $name }}_HelpBlock"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name) ?? false]) }}

        {{ $attributes->merge(['rows' => '3']) }}
    >{{ $value }}</textarea>
    @if(isset($help))
        <div id="{{ $name }}_HelpBlock" class="form-text">
            {{ $help }}
        </div>
    @endif
    <x-form.error name="{{ $name }}" />
</x-form.field>
