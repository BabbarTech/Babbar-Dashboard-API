@foreach(['danger', 'warning', 'info', 'success'] as $messageType)
    @if(session($messageType))
        <x-alert type="{{ $messageType }}" class="mb-0">{!! session($messageType) !!}</x-alert>
    @endif
@endforeach
