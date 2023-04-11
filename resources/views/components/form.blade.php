@if ($errors->any())
    <div class="alert alert-danger">
        {{ __('error.wrong_args') }}
    </div>
@endif

<form method="{{ $spoofMethod ? 'POST' : $method }}" action="{{ $action }}" {{ $attributes }}>
    @csrf
    @if($spoofMethod)
        @method($method)
    @endif

    {{ $slot }}
</form>
