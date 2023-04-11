@props(['action', 'method', 'disabled' => false, 'confirm' => null, 'name' => 'confirm'])
<form method="POST" action="{{ $action }}" class="d-inline-block" id="{{ $name }}Form">
    @csrf
    @method($method ?? 'POST')

    <button
        {{ $disabled  ? 'disabled' : '' }}

        @if ($confirm)
        type="button" data-bs-toggle="modal" data-bs-target="#{{ $name }}Modal"
        @else
        type="submit"
        @endif

        {{ $attributes->merge(['class' => 'btn']) }}
    >
        {{ $slot }}
    </button>
</form>

@if ($confirm)
<x-confirm-modal id="{{ $name }}Modal">
    {{ $confirm }}

    <x-slot:confirmBtn>
        <button type="button" class="btn btn-danger" onclick="document.getElementById('{{ $name }}Form').submit();">{{ __('Confirm') }}</button>
    </x-slot:confirmBtn>
</x-confirm-modal>
@endif
