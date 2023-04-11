<x-app-layout title="{{ __('resources.projects.create.title') }}">

    <div class="container">
        <h1>{{ __('resources.projects.create.title') }}</h1>

        <div class="card d-block">
            <div class="card-body">
                <x-form :action="route('projects.store')" class="mb-3">

                    <x-form.input name="url" placeholder="{{ __('resources.projects.property.url.placeholder') }}" />

                    <x-form.select name="serp" :options="\App\Enums\SerpEnum::dropdownOptions()" />

                    <x-form.textarea name="description" />

                    <h2 class="mt-4 mb-3">{{ __('benchmarks.choose-benchmarks-to-process') }}</h2>

                    <x-form.checkbox
                        id="choose-benchmark-keywords"
                        name="benchmarks[]"
                        label="{{ __('benchmarks.ranking-factor') }}"
                        help="{{ __('benchmarks.ranking-factor.description') }}"
                        value="{{ \App\Services\BenchmarkService\Benchmarks\RankingFactorBenchmark::class }}"
                        checked
                    />

                    <x-form.checkbox
                        id="choose-benchmark-backlink"
                        name="benchmarks[]"
                        label="{{ __('benchmarks.backlinks') }}"
                        help="{{ __('benchmarks.backlinks.description') }}"
                        value="{{ \App\Services\BenchmarkService\Benchmarks\RankingFactorBacklinksBenchmark::class }}"
                        checked
                    />

                    <button class="btn btn-primary" type="submit">
                        {{ __('resources.projects.create.title') }}
                    </button>
                </x-form>
            </div>
        </div>
    </div>


</x-app-layout>
