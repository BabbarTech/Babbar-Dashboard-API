<x-app-layout title="{{ $project->hostname }} {{ class_basename($benchmark->type) }}">
    <div id="mainContent" class="container">
        <x-header :project="$project">
            <x-slot:title>
                {{ class_basename($benchmark->type) }} :
                <span class="text-muted">{{ $benchmark->getBenchmarkableTitle() }}</span>
            </x-slot:title>
        </x-header>

        <div class="card d-block">
            <div class="card-body">
                <h2>{{ __('Benchmark processing steps') }}
                    <span class="ms-3 badge text-uppercase rounded-pill {{ $benchmark->status->color() }}">{{ $benchmark->status->label() }}</span>
                </h2>

                @if($benchmark->started_at)
                    <small class="pb-3">{{ __('Processing start at') }} {{ $benchmark->started_at }}</small>
                @endif

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>{{ __('Step') }}</th>
                        <th class="text-end">{{ __('Batch') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Estimate time remaining') }}</th>
                        <th class="text-end">{{ __('Warning') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($benchmark->steps as $step)
                        <tr>
                            <td>
                                {{ class_basename($step->handler) }}
                            </td>
                            <td class="text-end">
                                @if($step->total_jobs > 0)
                                    {{ $step->finished_jobs }}/{{ $step->total_jobs }}
                                @else
                                    ...
                                @endif
                            </td>
                            <td>
                                <span class="badge text-uppercase rounded-pill {{ $step->status->color() }}">{{ $step->status->label() }}</span>
                            </td>
                            <td class="text-end">
                                @if($step->getPendingJobsRemaining())
                                    {{ $step->processingEstimateTimeRemaining() }} minute(s)
                                @endif
                            </td>
                            <td class="text-end">
                                @if($step->nbBatchErrors > 0)
                                <a href="{{ route('projects.benchmarks.steps.batch-errors', [$project, $benchmark, $step]) }}" target="_blank" title="{{ $step->batchErrors()->latest()->first()->error }}">
                                    <span class="badge text-uppercase rounded-pill bg-warning">
                                        {{ $step->nbBatchErrors }}
                                    </span>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{--
                @if($benchmark->status === \App\Enums\StatusEnum::ERROR)
                    <x-form-button action="{{ route('retry-failed-jobs') }}" class="btn-primary">
                        Retry processing
                    </x-form-button>
                @endif
                --}}
            </div>
        </div>

        @if($benchmark->status === \App\Enums\StatusEnum::PROCESSING)
            <div class="text-end mt-3">
                <x-form-button
                    action="{{ route('projects.benchmarks.cancel', [$project, $benchmark]) }}"
                    class="btn-danger"
                    confirm="{{ __('benchmark.cancel.confirm') }}"
                >
                    Cancel
                </x-form-button>
            </div>
        @endif


        @if($benchmark->isProcessing() || $benchmark->isPending())
            <x-slot:head>
                <script>
                    setTimeout(() => location.reload(), 15000);
                </script>
            </x-slot:head>

        @endif

    </div>
</x-app-layout>
