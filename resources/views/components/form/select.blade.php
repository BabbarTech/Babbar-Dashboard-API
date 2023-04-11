<x-form.field>
    <x-form.label name="{{ $name }}" label="{{ $label }}" />
    <select
        id="{{ 'input_' . $sanitizedName }}"
        name="{{ $name }}"
        aria-describedby="{{ $sanitizedName }}_HelpBlock"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name) ?? false]) }}
    >
        @foreach($options as $key => $option)
            <option value="{{ $option['value'] }}" @if($value == $option['value']) selected="selected" @endif>{{ $option['label'] }}</option>
        @endforeach
    </select>
    <x-form.help :help="$help" />
    <x-form.error name="{{ $name }}" />
</x-form.field>
