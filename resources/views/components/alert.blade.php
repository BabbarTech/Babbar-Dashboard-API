@props(['type' => 'info'])
<div {{ $attributes->merge(['class' => 'alert-dismissible alert alert-'.$type]) }} role="alert">
    {!! $slot !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
