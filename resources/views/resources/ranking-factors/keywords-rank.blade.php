<x-app-layout title="{{ $project->hostname }} {{ __('ranking-factor.keywords-rank') }}">
    <div id="mainContent" class="container">
        <x-header title="{{ __('ranking-factor.keywords-rank') }}" :project="$project">
            <x-slot name="right">
                <button
                    data-html2canvas-ignore
                    class="btn-primary btn-sm"
                    data-bs-toggle="collapse"
                    data-bs-target="#fetchCompetitor"
                    aria-controls="fetchCompetitor"
                    aria-expanded="false"
                >
                    @include('partials.icons.download')
                    {{ __('actions.babbar_fetch_host_keywords') }}
                </button>
            </x-slot>
        </x-header>

        <div class="row mt-4" v-cloak>

            <div id="fetchCompetitor" class="@if(! $errors->any()) collapse @endif mb-3 bg-info-light p-3 rounded-2 mb-5 border" data-html2canvas-ignore>
                <x-fetch-competitor-keywords :project="$project" />
            </div>

            <div class="mb-3 bg-white p-3 rounded-2 mb-5 border" data-html2canvas-ignore>
                <dataset-collection
                    :sources="{{ $datasetSources }}"
                >
                </dataset-collection>
            </div>

            <ul class="nav nav-tabs mb-5" id="myTab" role="tablist" data-html2canvas-ignore>

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="per-page-tab" data-bs-toggle="tab" data-bs-target="#per-page" type="button" role="tab" aria-controls="per-page" aria-selected="true">Nb of keywords in SERP per page</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="per-rank-tab" data-bs-toggle="tab" data-bs-target="#per-rank" type="button" role="tab" aria-controls="per-rank" aria-selected="false">Nb of keywords in SERP per position</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="per-page" role="tabpanel" aria-labelledby="per-page-tab">

                    <chart
                        v-if="datasetCollectionStore?.count > 0"
                        class="mb-0"
                        :sources="datasetCollectionStore.datasets"
                        :config="{{ $perPageChartConfig }}"
                        :transformer="(source) => source.response?.data['per-page'].map((item) => ({value: item?.nb_keywords || 0, unit: 'kw'}))"
                        type="bar"
                    >
                    </chart>

                    <div class="text-center mb-5">
                        <x-screenshot
                            class="btn-outline-secondary btn-sm me-3"
                            capture="mainContent"
                            filename-part="bar"
                        >
                            @include('partials.icons.chart')
                            {{ __('screenshot.chart') }}
                        </x-screenshot>

                        <x-form-button
                            data-html2canvas-ignore
                            v-if="datasetCollectionStore"
                            action="{{ route('projects.ranking-factors.keywords-ranks.actions.export-csv', [$project, 'per-page']) }}"
                            class="btn-outline-secondary btn-sm"
                            target="_blank"
                        >
                            <input
                                v-for="hostname in Object.keys(datasetCollectionStore.datasets)"
                                type="hidden"
                                name="hostnames[]"
                                :value="hostname" />
                            @include('partials.icons.download')
                            {{ __('actions.export_to_csv') }}
                        </x-form-button>
                    </div>

                    <table class="table table-striped mb-3" data-html2canvas-ignore>
                        <thead class="bg-white sticky-header">
                        <tr>
                            <th>{{ __('Page') }}</th>
                            <th class="text-end">{{ __('Nb of keywords') }}</th>
                            <th class="text-end">{{ __('Median BKS') }}</th>
                            <th class="text-end">{{ __('Average BKS') }}</th>
                            <th class="text-end">{{ __('Average Nb words') }}</th>
                        </tr>
                        </thead>
                        <tbody
                            v-if="datasetCollectionStore.datasets"
                        >
                        <template v-for="index in {{ $nbPages }}" >
                            <tr :key="index">
                                <th colspan="5">Page @{{ index }}</th>
                            </tr>

                            <template v-for="(dataset, hostname) in datasetCollectionStore.datasets">
                                <tr v-if="dataset.response?.data['per-page']" :key="hostname + index">
                                    <td class="ps-4">@{{ hostname }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-page'][(index - 1)]?.nb_keywords }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-page'][(index - 1)]?.median_bks }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-page'][(index - 1)]?.average_bks }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-page'][(index - 1)]?.average_nb_words }}</td>
                                </tr>
                            </template>

                        </template>

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="per-rank" role="tabpanel" aria-labelledby="per-rank-tab">

                    <chart
                        v-if="datasetCollectionStore?.count > 0"
                        class="mb-3"
                        :sources="datasetCollectionStore.datasets"
                        :config="{{ $perRankChartConfig }}"
                        :transformer="(source) => source.response?.data['per-rank'].map((item) => item?.nb_keywords || 0)"
                        type="line"
                    >
                    </chart>

                    <div class="text-center mb-5">
                        <x-screenshot
                            class="btn-outline-secondary btn-sm me-3"
                            capture="mainContent"
                            filename-part="rank"
                        >
                            @include('partials.icons.chart')
                            {{ __('screenshot.chart') }}
                        </x-screenshot>

                        <x-form-button
                            data-html2canvas-ignore
                            v-if="datasetCollectionStore"
                            action="{{ route('projects.ranking-factors.keywords-ranks.actions.export-csv', [$project, 'per-rank']) }}"
                            class="btn-outline-secondary btn-sm"
                            target="_blank"
                        >
                            <input
                                v-for="hostname in Object.keys(datasetCollectionStore.datasets)"
                                type="hidden"
                                name="hostnames[]"
                                :value="hostname" />
                            @include('partials.icons.download')
                            {{ __('actions.export_to_csv') }}
                        </x-form-button>


                    </div>

                    <table class="table table-striped mb-3" data-html2canvas-ignore>
                        <thead class="bg-white sticky-header">
                        <tr>
                            <th>{{ __('Rank') }}</th>
                            <th class="text-end">{{ __('Nb of keywords') }}</th>
                            <th class="text-end">{{ __('Median BKS') }}</th>
                            <th class="text-end">{{ __('Average BKS') }}</th>
                            <th class="text-end">{{ __('Average Nb words') }}</th>
                        </tr>
                        </thead>
                        <tbody
                            v-if="datasetCollectionStore.datasets"
                        >
                        <template v-for="index in {{ $rankMax }}" >
                            <tr :key="index">
                                <th colspan="5">Position @{{ index }}</th>
                            </tr>

                            <template v-for="(dataset, hostname) in datasetCollectionStore.datasets">
                                <tr v-if="dataset.response?.data['per-rank']" :key="hostname + index">
                                    <td class="ps-4">@{{ hostname }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-rank'][(index - 1)]?.nb_keywords }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-rank'][(index - 1)]?.median_bks }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-rank'][(index - 1)]?.average_bks }}</td>
                                    <td class="text-end">@{{ dataset.response.data['per-rank'][(index - 1)]?.average_nb_words }}</td>
                                </tr>
                            </template>

                        </template>

                        </tbody>
                    </table>

                </div>
            </div>

            <dataset
                dataset-id="{{ $project->hostname }}"
                api="{{ route('api.v1.ranking-factors.keywords-ranks-distribution', ['project' => $project, 'host_id' => $project->host_id]) }}"
                :payload="{}"
            >
            </dataset>

        </div>
    </div>
</x-app-layout>
