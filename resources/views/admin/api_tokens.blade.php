@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Installation') }} {{ config('app.name', 'Laravel') }} - Step 2/2</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.api_tokens.store') }}">
                            @csrf

                            <fieldset>
                                <legend>{{ __('API accounts') }}</legend>

                                @foreach($gatewayCollection as $gatewayKey => $gateway)
                                    <div class="row mb-3">
                                        <label for="gateways_{{ $gatewayKey }}_api_token"
                                               class="col-md-4 col-form-label text-md-end">{{ $gateway['label'] }} Token</label>
                                        <div class="col-md-8">
                                            <input id="gateways_{{ $gatewayKey }}_api_token"
                                                   type="text"
                                                   class="form-control @error('gateways.' . $gatewayKey . '.api_token') is-invalid @enderror"
                                                   name="gateways[{{ $gatewayKey }}][api_token]"
                                                @if($gatewayKey == 'Trafilatura')
                                                value="{{ Str::random(60) }}"
                                                   @if($gateway['required'] ?? false) required @endif
                                                @else 
                                                value="{{ old('gateways.' . $gatewayKey . '.api_token', $gateway['obfuscated_api_token']) }}"
                                                   @if($gateway['required'] ?? false) required @endif
                                                @endif
                                            >
                                            @error('gateways.' . $gatewayKey . '.api_token')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </fieldset>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
