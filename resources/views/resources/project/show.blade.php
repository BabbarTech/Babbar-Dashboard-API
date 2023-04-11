<x-app-layout title="{{ __('Project') }} {{ $project->hostname }}">
    <div class="container">
        <h1>{{ $project->hostname }}</h1>

        @if(! empty($project->description))
        <div class="card mb-3">
            <div class="card-body">
                {{ $project->description }}
            </div>
        </div>
        @endif

        <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
            <div class="col">

                <x-benchmark-card benchmark-service-classname="RankingFactorBenchmark">
                    <h2 class="card-title mb-3">
                        {{ __('benchmarks.ranking-factor') }}
                        @if($component->lastBenchmarkInstance && $component->lastBenchmarkInstance->status != \App\Enums\StatusEnum::DONE)
                            <span class="ms-3 badge rounded-pill {{ $component->lastBenchmarkInstance->status->color() }}">{{ $component->lastBenchmarkInstance->status->label() }}</span>
                        @endif
                    </h2>
                    <div class="list-group list-group-flush">
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.keywords-in-common', $project) }}">
                            <div class="d-flex align-items-center">
                                @include('partials.icons.chart-bar-line')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.keywords-in-common') }}</strong>
                                    <br><small class="text-muted">{{ __('ranking-factor.keywords-in-common.detail') }}</small>
                                </div>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.keywords-present', $project) }}">
                            <div class="d-flex align-items-baseline">
                                @include('partials.icons.list')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.keywords-present') }}</strong>
                                    <br><small class="text-muted">{{ __('ranking-factor.keywords-present.detail') }}</small>
                                </div>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.keywords-not-present', $project) }}">
                            <div class="d-flex align-items-baseline">
                                @include('partials.icons.list')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.keywords-not-present') }}</strong>
                                    <br><small class="text-muted">{{ __('ranking-factor.keywords-not-present.detail') }}</small>
                                </div>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.keywords-guides', $project) }}">
                            <div class="d-flex align-items-center">
                                @include('partials.icons.clipboard-checked')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.selected-keywords') }}</strong>
                                    <br><small class="text-muted">{{ __('ranking-factor.selected-keywords.detail') }}</small>
                                </div>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.keywords-ranks', $project) }}">
                            <div class="d-flex align-items-center">
                                @include('partials.icons.chart-bar-line')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.keywords-rank') }}</strong>
                                    <br><small class="text-muted">{{ __('ranking-factor.keywords-rank.detail') }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </x-benchmark-card>

            </div>
            <div class="col">

                <x-benchmark-card benchmark-service-classname="RankingFactorBacklinksBenchmark">
                    <h2 class="card-title mb-3">
                        {{ __('benchmarks.backlinks') }}
                        @if($component->lastBenchmarkInstance && $component->lastBenchmarkInstance->status != \App\Enums\StatusEnum::DONE)
                            <span class="ms-3 badge rounded-pill {{ $component->lastBenchmarkInstance->status->color() }}">{{ $component->lastBenchmarkInstance->status->label() }}</span>
                        @endif
                    </h2>
                    <div class="list-group list-group-flush">
                        <a class="list-group-item list-group-item-action @if(! $component->hasProcessedData) disabled @endif" href="{{ route('projects.ranking-factors.backlinks', $project) }}">
                            <div class="d-flex align-items-center">
                                @include('partials.icons.chart-bar-line')
                                <div class="ms-3">
                                    <strong>{{ __('ranking-factor.backlinks') }}</strong>
                                    <br><small class="text-muted">{{ __('benchmarks.backlinks.description') }}</small>
                                </div>
                            </div>
                        </a>
                    </div>

                </x-benchmark-card>

            </div>
        </div>


        @if($latestBenchmarks->isNotEmpty())
            <div class="card d-block mb-3 text-bg-secondary" style="background:#F1F1F1">
                <div class="card-body">
                    <h3 class="mb-4">{{ __('Latest benchmarks processing') }}</h3>
                    @include('partials.benchmarks-table', ['benchmarks' => $latestBenchmarks])
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('projects.benchmarks.index', $project) }}">{{ __('benchmarks.view.all') }}</a>
                </div>
            </div>
        @endif


        <div class="mt-3 text-end">
            @include('partials.buttons.delete-project')
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm">{{ __('resources.projects.edit.title') }}</a>
        </div>
    </div>
</x-app-layout>
