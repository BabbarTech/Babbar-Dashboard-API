<x-app-layout title="{{ $project->hostname }} {{ __('ranking-factor.keywords-in-common') }}">
    <div id="mainContent" class="container">
        <x-header title="{{ __('ranking-factor.keywords-in-common') }}" :project="$project">
            <x-slot name="right">
                <x-screenshot
                    v-if="datasetCollectionStore.datasets.main?.selected"
                    class="btn-primary btn-sm"
                    capture="mainContent"
                    filename-part="overview_%hostname%"
                >
                    @include('partials.icons.download')
                    {{ __('screenshot.overview') }}
                </x-screenshot>
            </x-slot>
        </x-header>

            <div class="row mt-4">
                <div id="mainChart" class="col">

                    <keyword-in-common-chart-bubble
                        class="mb-3"
                        v-cloak
                        :loading="datasetCollectionStore.isLoading('main')"
                        :source="datasetCollectionStore.collection('main')"
                        :selected="datasetCollectionStore.datasets.main?.selected"
                        v-on:bubble-clicked="datasetCollectionStore.datasets.main.select($event)"
                    >
                    </keyword-in-common-chart-bubble>


                    <div class="mb-3 text-center">
                        <x-chart-help>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">{{ __('chart.help.bubble-size') }}</dt>
                                <dd class="col-sm-8 help-text">{{ __('chart.help.keyword-in-common.chart.bubble-size-help') }}</dd>
                            </dl>
                        </x-chart-help>

                        <x-screenshot
                            class="btn-outline-secondary btn-sm"
                            capture="mainChart"
                            filename-part="chart_%hostname%"
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
                        @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'hostname'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'nb_keywords_in_common'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'nb_kw_top20'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'similar_score_percent', 'max' => 100])
                    </dataset-filters>

                    <dataset
                        v-cloak
                        api="{{ route('api.v1.ranking-factors.keywords-in-common', [$project]) }}"
                        :payload="{{ $mainDatasetPayload }}"
                        v-on:selected="datasetCollectionStore.datasets.details.fetch({ 'host': $event.hostname, foo: 'bar'});"
                        data-html2canvas-ignore
                    >

                        <template #header="{ store }">
                            <div class="row align-items-center justify-content-between mb-3">
                                <div class="col-5">
                                    <h2>Competitors <span class="text-muted" v-if="store">(@{{ store?.response?.data?.length || '...' }})</span></h2>
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
                                        <th @click="store.sortBy('hostname')">{{ __('Host') }}</th>
                                        <th @click="store.sortBy('nb_keywords_in_common', false)" class="text-end">{{ __('Keywords in common') }}</th>
                                        <th @click="store.sortBy('hosts.nb_kw_pos_11_20', false)" class="text-end">{{ __('Keywords in top 20') }}</th>
                                        <th @click="store.sortBy('s.score', false)" class="text-end">{{ __('Similarity in %') }}</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="align-middle"
                                    v-if="store.response?.data"
                                    :class="{ 'dataset-loading': store.loading }"
                                >
                                    <tr v-for="item in store.response.data" :key="item.id">
                                        <td><input type="checkbox" v-model="store.selections" :value="item.id"></td>
                                        <td>
                                            <a href="#" @click="store.select(item)">
                                                @{{ item.hostname }}
                                            </a>
                                        </td>
                                        <td class="text-end">@{{ item.nb_keywords_in_common }}</td>
                                        <td class="text-end">@{{ item.nb_keywords_in_top20 }}</td>
                                        <td class="text-end">@{{ item.similar_score_percent ? item.similar_score_percent + ' %' : 'n/a' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p v-if="store.response?.data?.length < 1">{{ __('dataset.filtering.no-result') }}</p>

                        </template>

                        {{--
                        <template #footer>
                            <p>Lorem ipsums ... </p>
                        </template>
                        --}}

                    </dataset>

                </div>
                <div class="col">

                    <div v-cloak v-if="datasetCollectionStore?.datasets?.main?.selected">
                        <div class="sticky-title">
                            @{{ datasetCollectionStore.datasets.main.selected.hostname }}
                            @include('partials.buttons.clear-selected-item')
                        </div>
                        <div class="alert alert-info mb-5">
                            <table class="table table-sm">
                                <tr>
                                    <th>{{ __('Host similarity') }}</th>
                                    <td class="text-end"> @{{ datasetCollectionStore.datasets.main.selected.similar_score_percent }} %</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Number of keywords in common') }}</th>
                                    <td class="text-end">@{{ datasetCollectionStore.datasets.main.selected.nb_keywords_in_common }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Number of keywords in Pos 1-10') }}</th>
                                    <td class="text-end">@{{ datasetCollectionStore.datasets.main.selected.nb_keywords_in_pos_1_10 }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Number of keywords in Pos 11-20') }}</th>
                                    <td class="text-end">@{{ datasetCollectionStore.datasets.main.selected.nb_keywords_in_pos_11_20 }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Number of keywords in Top 20') }}</th>
                                    <td class="text-end">@{{ datasetCollectionStore.datasets.main.selected.nb_keywords_in_top20 }}</td>
                                </tr>
                            </table>

                        </div>

                        <dataset
                            data-html2canvas-ignore
                            dataset-id="details"
                            api="{{ route('api.v1.ranking-factors.keywords-in-common.common-keywords', ['project' => $project, 'host' => '%host%']) }}"
                            :payload="{{ $detailDatasetPayload }}"
                            :idle="true"
                        >
                            <template #header="{ store }">
                                <dataset-filters
                                    data-html2canvas-ignore
                                    class="bg-white p-3 rounded-2 mb-5 border"
                                    v-cloak
                                    data-html2canvas-ignore
                                    dataset-id="details"
                                    v-slot="{ filters }"
                                >
                                    @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'keywords'])
                                    @includeIf('partials.filters.MinMaxFilter', ['property' => 'current_rank', 'max' => 100])
                                    @includeIf('partials.filters.MinMaxFilter', ['property' => 'competitor_rank', 'max' => 100])
                                    @includeIf('partials.filters.MinMaxFilter', ['property' => 'bks', 'max' => 100])
                                    @includeIf('partials.filters.BooleanFilter', ['property' => 'competitor_has_better_rank', 'trueLabel' => __('filters.ranking.better'), 'falseLabel' => __('filters.ranking.worse')])
                                    @includeIf('partials.filters.GroupingFilter', ['property' => 'group_keywords'])

                                </dataset-filters>

                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col">
                                        <h3>Keywords <span class="text-muted">(@{{ store?.response?.data?.length || '...' }})</span></h3>
                                    </div>
                                    <div class="col text-end">
                                        <dataset-action
                                            :actions="{{ $keywordsActions }}"
                                            :store="store"
                                            v-cloak
                                        >
                                        </dataset-action>
                                    </div>
                                </div>
                            </template>

                            <template v-slot="{store, filters}">
                                <table class="table table-striped mb-3">
                                    <thead class="bg-white sticky-header sticky-header-nested">
                                        <tr>
                                            <th @click="store.toggleSelection('keyword_id')" class="cursor" title="{{ __('dataset.selection.toggle.tooltip') }}">#</th>
                                            <th @click="store.sortBy('keywords')">{{ __('Keywords in common') }}</th>
                                            <th @click="store.sortBy('competitor.rank')" class="text-end">{{ __('Competitor Position ') }}</th>
                                            <th @click="store.sortBy('current_rank')" class="text-end">{{ __('Current Position') }}</th>
                                            <th @click="store.sortBy('competitor_has_better_rank', false)" class="text-end">{{ __('Better ranking') }}</th>
                                            <th @click="store.sortBy('bks', false)" class="text-end">{{ __('BKS') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="align-middle"
                                        v-if="store.response?.data"
                                        :class="{ 'dataset-loading': store.loading }"
                                    >
                                        <tr v-for="item in store.response.data" :key="item.id">
                                            <td><input type="checkbox" v-model="store.selections" :value="item.keyword_id"></td>
                                            <td><a :href="'https://www.babbar.tech/keywords/' + item.keywords" target="_blank">@{{ item.keywords }}</a></td>
                                            <td class="text-end"><a :href="item.competitor_url" :title="item.competitor_url" target="_blank">@{{ item.competitor_rank }}</a></td>
                                            <td class="text-end"><a :href="item.current_url" :title="item.current_url" target="_blank">@{{ item.current_rank }}</a></td>
                                            <td class="text-end">
                                                <span class="badge bg-light text-dark" v-if="item.competitor_has_better_rank">{{ __('No') }}</span>
                                                <span class="badge bg-success" v-else>{{ __('Yes') }}</span>
                                            </td>
                                            <td class="text-end">@{{ item.bks }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p v-if="store.response?.data?.length < 1">{{ __('dataset.filtering.no-result') }}</p>

                            </template>
                        </dataset>
                    </div>

                </div>
            </div>

    </div>
</x-app-layout>
