@props(['help'])
@aware(['sanitizedName'])

@if(isset($help))
    <div id="{{ $sanitizedName }}_HelpBlock" class="form-text">
        {{ $help }}
    </div>
@endif
