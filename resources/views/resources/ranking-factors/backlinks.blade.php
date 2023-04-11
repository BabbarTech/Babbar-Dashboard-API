<x-app-layout title="{{ $project->hostname }} {{ __('ranking-factor.backlinks') }}">
    <div id="mainContent" class="container">
        <x-header title="{{ __('ranking-factor.backlinks') }}" :project="$project">
            <x-slot name="right">
                <x-screenshot
                    v-if="datasetCollectionStore.datasets.main?.selected"
                    class="btn-primary btn-sm"
                    capture="mainContent"
                    filename-part="overview_%id%_%source_url%"
                >
                    @include('partials.icons.download')
                    {{ __('screenshot.overview') }}
                </x-screenshot>
            </x-slot>
        </x-header>

            <div class="row mt-2">
                <div id="mainChart" class="col">

                    <backlink-chart-bubble
                        class="mb-3"
                        v-cloak
                        :loading="datasetCollectionStore.isLoading('main')"
                        :source="datasetCollectionStore.collection('main', item => item.page_value && item.semantic_value)"
                        :selected="datasetCollectionStore.datasets.main?.selected"
                        v-on:bubble-clicked="datasetCollectionStore.datasets.main.select($event)"
                    >
                    </backlink-chart-bubble>

                    <div class="mb-3 text-center">
                        <x-chart-help>
                            <dt class="col-sm-4">{{ __('chart.help.bubble-size') }}</dt>
                            <dd class="col-sm-8 help-text">{{ __('chart.help.backlink.bubble-size-help') }}</dd>

                            <dt class="col-sm-4">{{ __('chart.help.bubble-color') }}</dt>
                            <dd class="col-sm-8 help-text"><div class="colors-legend">
                                    <span class="legend">{{ __('chart.help.backlink.bubble-color-help') }}</span>
                                    <span class="gradient"></span>
                                    <div class="row gradient-legend">
                                        <div class="col align-self-start">
                                            {{ __('chart.help.poor') }}
                                        </div>
                                        <div class="col align-self-end text-end">
                                            {{ __('chart.help.good') }}
                                        </div>
                                    </div>
                                </div>
                            </dd>
                        </x-chart-help>

                        <x-screenshot
                            class="btn-outline-secondary btn-sm"
                            capture="mainChart"
                            filename-part="chart_%id%_%source_url%"
                        >
                            @include('partials.icons.chart')
                            {{ __('screenshot.chart') }}
                        </x-screenshot>
                    </div>

                    <dataset-filters
                        data-html2canvas-ignore
                        class="bg-white p-3 rounded-2 mb-5 border"
                        v-cloak
                        v-slot="{ filters }"
                    >
                        @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'source_urls'])
                        @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'target_urls'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'page_value', 'max' => 100])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'semantic_value', 'max' => 100])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'page_trust', 'max' => 100])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'babbar_authority_score', 'max' => 100])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'induced_strength', 'max' => 100])
                        @includeIf('partials.filters.OptionFilter', ['property' => 'induced_strength_confidence', 'options' => \App\Enums\InducedStrengthConfidenceEnum::dropdownOptions()])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'source_nb_keywords_in_top20'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'source_nb_backlinks'])

                    </dataset-filters>

                    <dataset
                        v-cloak
                        api="{{ route('api.v1.ranking-factors.backlinks', [$project, $project->host]) }}"
                        :payload="{{ $mainDatasetPayload }}"
                        v-on:selected="datasetCollectionStore.datasets.details.fetch({ 'id': $event.id, foo: 'bar'});
                        datasetCollectionStore.datasets.sourceBacklinks.fetch({ 'id': $event.id, foo: 'bar'})"
                        data-html2canvas-ignore
                    >


                        <template #header="{ store }">
                            <div class="row align-items-center justify-content-between mb-3">
                                <div class="col-5">
                                    <h2>Backlinks <span class="text-muted" v-if="store">(@{{ store?.response?.data?.length || '...' }})</span></h2>
                                </div>
                                <div class="col-7 text-end">
                                    <dataset-action
                                        :actions="{{ $mainActions }}"
                                        :store="store"
                                        v-cloak
                                    >
                                    </dataset-action>
                                </div>
                            </div>
                        </template>

                        <template v-slot="{store, filters}">

                            <table class="table table-striped mb-3">
                                <thead class="bg-white sticky-header">
                                    <tr>
                                        <th class="toggle-selection" data-bs-toggle="tooltip" title="{{ __('dataset.selection.toggle.tooltip') }}">
                                            <dataset-selection-toggle :store="store"><span class="ps-1">#</span></dataset-selection-toggle>
                                        </th>
                                        <th @click="store.sortBy('source_url')" class="cursor">{{ __('Source') }}</th>
                                        <th @click="store.sortBy('target_url')" class="cursor" >{{ __('Target') }}</th>
                                        <th @click="store.sortBy('page_value', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.pv') }}">{{ __('PV') }}</th>
                                        <th @click="store.sortBy('semantic_value', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.sv') }}">{{ __('SV') }}</th>
                                        <th @click="store.sortBy('page_trust', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.pt') }}">{{ __('PT') }}</th>
                                        <th @click="store.sortBy('babbar_authority_score', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.bas') }}">{{ __('BAS') }}</th>
                                        <th @click="store.sortBy('induced_strength', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.is') }}">{{ __('IS') }}</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="align-middle"
                                    v-if="store.response?.data"
                                    :class="{ 'dataset-loading': store.loading }"
                                >
                                    <tr v-for="item in store.response.data" :key="item.id">
                                        <td><input type="checkbox" v-model="store.selections" :value="item.id"></td>
                                        <td class="text-break">
                                            <a href="#" @click="store.select(item)">
                                                <small>
                                                    @{{ item.source_url }}
                                                </small>
                                            </a>
                                        </td>
                                        <td class="text-break">
                                            <small>
                                                @{{ item.target_url }}
                                            </small>
                                        </td>
                                        <td class="text-end">@{{ item.page_value ? item.page_value : 'n/a' }}</td>
                                        <td class="text-end">@{{ item.semantic_value ? item.semantic_value : 'n/a' }}</td>
                                        <td class="text-end">@{{ item.page_trust ? item.page_trust : 'n/a' }}</td>
                                        <td class="text-end">@{{ item.babbar_authority_score ? item.babbar_authority_score : 'n/a' }}</td>
                                        <td class="text-end" :title="item.induced_strength_confidence">@{{ item.induced_strength ? item.induced_strength : 'n/a' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p v-if="store.response?.data?.length < 1">{{ __('dataset.filtering.no-result') }}</p>

                        </template>

                    </dataset>

                </div>
                <div class="col">
                    <div v-cloak v-if="datasetCollectionStore?.datasets?.main?.selected" id="detailscre">
                        <div class="sticky-title">
                            @{{ datasetCollectionStore.datasets.main.selected.source_url }}
                            @include('partials.buttons.clear-selected-item')
                        </div>

                        <div class="alert alert-info mb-5">
                            <table class="table table-sm">
                                <tr>
                                    <th>{{ __('Source') }}</th>
                                    <td class="text-break">
                                        <a :href="datasetCollectionStore.datasets.main.selected.source_url" target="_blank">
                                            @{{ datasetCollectionStore.datasets.main.selected.source_url }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Target') }}</th>
                                    <td class="text-break">
                                        <a :href="datasetCollectionStore.datasets.main.selected.target_url" target="_blank">
                                            @{{ datasetCollectionStore.datasets.main.selected.target_url }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Induced Strength') }}</th>
                                    <td>
                                        @{{ datasetCollectionStore.datasets.main.selected.induced_strength }}
                                        <span class="badge bg-success ms-3">@{{ datasetCollectionStore.datasets.main.selected.induced_strength_confidence }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Page value') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.page_value || 'n/a' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Semantic value') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.semantic_value || 'n/a' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Page trust') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.page_trust || 'n/a' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('BAS') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.babbar_authority_score || 'n/a' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('filters.nb_kw_top20.label') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.source_nb_keywords_in_top20 }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Language') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.language }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Link type') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.link_type }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Link text') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.link_text }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Link rels') }}</th>
                                    <td>@{{ datasetCollectionStore.datasets.main.selected.link_rels }}</td>
                                </tr>
                            </table>

                        </div>

                        <ul class="nav nav-tabs mb-3 hide-on-screen-cap" data-html2canvas-ignore id="myTab" role="tablist">

                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="source-keyword-tab" data-bs-toggle="tab" data-bs-target="#source-keyword" type="button" role="tab" aria-controls="source-keyword" aria-selected="true">Keywords in top20</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="source-backlink-tab" data-bs-toggle="tab" data-bs-target="#source-backlink" type="button" role="tab" aria-controls="source-backlink" aria-selected="false">Backlinks</button>
                            </li>
                        </ul>
                        <div class="tab-content hide-on-screen-cap" id="myTabContent" data-html2canvas-ignore>
                            <div class="tab-pane fade show active" id="source-keyword" role="tabpanel" aria-labelledby="source-keyword-tab">

                                <dataset
                                    dataset-id="details"
                                    api="{{ route('api.v1.ranking-factors.backlinks.source-keywords', [$project, $project->host, 'backlink' => '%id%']) }}"

                                    :payload="{{ $detailDatasetPayload }}"
                                    :idle="true"
                                    class="mb-5"
                                >

                                    <template #header="{ store }">
                                        <div class="row align-items-center justify-content-between mb-3">
                                            <div class="col-6">
                                                <h3>
                                                    Keywords in top20 <span class="text-muted" v-if="store">(@{{ store?.response?.data?.length || '...' }})</span>
                                                </h3>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </template>

                                    <template v-slot="{store, filters}">
                                        <table class="table table-striped mb-3">
                                            <thead class="bg-white sticky-header sticky-header-nested">
                                            <tr>
                                                <th @click="store.sortBy('keywords')" class="cursor">{{ __('Keywords') }}</th>
                                                <th @click="store.sortBy('rank')" class="cursor text-end">{{ __('Rank') }}</th>
                                                <th @click="store.sortBy('bks', false)" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.bks') }}">{{ __('BKS') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody
                                                class="align-middle"
                                                v-if="store.response?.data"
                                                :class="{ 'dataset-loading': store.loading }"
                                            >
                                            <tr v-for="item in store.response.data" :key="item.id">
                                                <td><a :href="'https://www.babbar.tech/keywords/' + item.keywords" target="_blank">@{{ item.keywords }}</a></td>
                                                <td class="text-end">@{{ item.rank }}</td>
                                                <td class="text-end">@{{ item.bks }}</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <p v-if="store.response?.data?.length === 0">{{ __('dataset.filtering.no-result') }}</p>

                                    </template>
                                </dataset>

                            </div>
                            <div class="tab-pane fade" id="source-backlink" role="tabpanel" aria-labelledby="source-backlink-tab">

                                <dataset
                                    dataset-id="sourceBacklinks"
                                    api="{{ route('api.v1.ranking-factors.backlinks.source-backlinks', [$project, $project->host, 'backlink' => '%id%']) }}"

                                    :payload="{{ $detailDatasetPayload }}"
                                    :idle="true"
                                >
                                    <template #header="{ store }">
                                        <div class="row align-items-center justify-content-between mb-3">
                                            <div class="col-6">
                                                <h3>
                                                    Backlinks <span class="text-muted" v-if="store">(@{{ store?.response?.data?.length || '...' }})</span>
                                                </h3>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </template>

                                    <template v-slot="{store, filters}">

                                        <table class="table table-striped mb-3">
                                            <thead class="bg-white sticky-header sticky-header-nested">
                                            <tr>
                                                <th @click="store.sortBy('source_url')" class="cursor">{{ __('Url source') }}</th>
                                                <th @click="store.sortBy('induced_strength')" class="cursor text-end" data-bs-toggle="tooltip" title="{{ __('definition.is-with-confidence') }}">{{ __('IS') }}</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody
                                                class="align-middle"
                                                v-if="store.response?.data"
                                                :class="{ 'dataset-loading': store.loading }"
                                            >
                                            <tr v-for="item in store.response.data" :key="item.id">
                                                <td class="text-break">
                                                    <a :href="'https://www.babbar.tech/url/' + item.source_url?.url" target="_blank">
                                                        <small>
                                                            @{{ item.source_url }}
                                                        </small>
                                                    </a>
                                                </td>
                                                <td class="text-end">
                                                    @{{ item.induced_strength }}
                                                </td>
                                                <td>
                                                    <span v-if="item.induced_strength" class="badge bg-secondary">@{{ item.induced_strength_confidence }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <p v-if="store.response?.data?.length === 0">{{ __('dataset.filtering.no-result') }}</p>
                                    </template>
                                </dataset>

                            </div>
                        </div>


                    </div>

                </div>
            </div>

    </div>
</x-app-layout>
