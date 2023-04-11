<x-app-layout title="{{ $project->hostname }} {{ __('ranking-factor.selected-keywords') }}">
    <div id="mainContent" class="container">
        <x-header title="{{ __('ranking-factor.selected-keywords') }}" :project="$project"></x-header>
            <div class="row mt-4">
                <div class="col-4">

                    <dataset-filters
                        class="bg-white p-3 rounded-2 mb-5 border"
                        v-cloak
                        v-slot="{ filters }"
                    >

                        @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'keywords'])
                        @includeIf('partials.filters.MinMaxFilter', ['property' => 'bks', 'max' => 100])

                    </dataset-filters>

                    <dataset
                        v-cloak
                        api="{{ route('api.v1.ranking-factors.keywords-guides', [$project]) }}"
                        :payload="{{ $mainDatasetPayload }}"
                        v-on:selected="datasetCollectionStore.datasets.details.fetch({ 'yourtextguru_guide_id': $event.yourtextguru_guide_id })"
                    >

                        <template #header="{ store }">
                            <div class="row align-items-center justify-content-between mb-3">
                                <div class="col-3">
                                    <h2>{{ __('Guides') }}</h2>
                                    <span class="text-muted" v-if="store">(@{{ store?.response?.data?.length || '...' }})</span>
                                </div>
                                <div class="col-9 text-end">
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
                                        <th @click="store.toggleSelection('guide_id')" class="cursor" title="{{ __('dataset.selection.toggle.tooltip') }}">#</th>
                                        <th @click="store.sortBy('keywords')">{{ __('Keywords') }}</th>
                                        <th @click="store.sortBy('bks', false)" class="text-end">{{ __('BKS') }}</th>
                                        <th @click="store.sortBy('yourtextguru_guide_id')" class="text-end">{!! __('Guide&nbsp;ID') !!}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="align-middle"
                                    v-if="store.response?.data"
                                    :class="{ 'dataset-loading': store.loading }"
                                >
                                    <tr v-for="item in store.response.data" :key="item.id">
                                        <td><input type="checkbox" v-model="store.selections" :value="item.guide_id"></td>
                                        <td><a href="#" @click="store.select(item)">@{{ item.keywords }}</a></td>
                                        <td class="text-end">@{{ item.bks }}</td>
                                        <td class="text-end"><a :href="'https://yourtext.guru/view/' + item.yourtextguru_guide_id" target="_blank">@{{ item.yourtextguru_guide_id }}</a></td>
                                        <td class="">@{{ item.status }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p v-if="store.response?.data?.length < 1">{{ __('dataset.filtering.no-result') }}</p>

                        </template>


                    </dataset>

                </div>
                <div class="col-8">

                    <div v-cloak v-if="datasetCollectionStore?.datasets?.main?.selected">

                        <div class="sticky-title">
                            @{{ datasetCollectionStore.datasets.main.selected.keywords }}
                            @include('partials.buttons.clear-selected-item')
                        </div>

                        <div class="alert alert-info mb-5">
                            <table class="table table-sm">
                                <tr>
                                    <th>{{ __('BKS') }}</th>
                                    <td class="text-end"> @{{ datasetCollectionStore.datasets.main.selected.bks }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('YourTextGuru Guide ID') }}</th>
                                    <td class="text-end">
                                        <a :href="'https://yourtext.guru/view/' + datasetCollectionStore.datasets.main.selected.yourtextguru_guide_id" target="_blank">@{{ datasetCollectionStore.datasets.main.selected.yourtextguru_guide_id }}</a>
                                        / <a :href="'https://yourtext.guru/stats/' + datasetCollectionStore.datasets.main.selected.yourtextguru_guide_id" target="_blank">{{ __('Stats') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td class="text-end"> @{{ datasetCollectionStore.datasets.main.selected.status }}</td>
                                </tr>
                            </table>

                        </div>

                        <dataset
                            dataset-id="details"
                            api="{{ route('api.v1.ranking-factors.keywords-guides.stats', ['project' => $project, 'yourtextguru_guide_id' => '%yourtextguru_guide_id%']) }}"
                            :payload="{{ $detailDatasetPayload }}"
                            :idle="true"
                        >

                            <template v-slot="{store, filters}">

                                <table class="table table-striped mb-3">
                                    <thead class="bg-white sticky-header sticky-header-nested">
                                        <tr>
                                            <th @click="store.sortBy('rank')" class="text-end">{{ __('Rank') }}</th>
                                            <th @click="store.sortBy('url')">{{ __('Url') }}</th>
                                            <th @click="store.sortBy('page_value')" class="text-end">{{ __('PV') }}</th>
                                            <th @click="store.sortBy('page_trust')" class="text-end">{{ __('PT') }}</th>
                                            <th @click="store.sortBy('semantic_value')" class="text-end">{{ __('SV') }}</th>
                                            <th @click="store.sortBy('babbar_authority_score')" class="text-end">{{ __('BAS') }}</th>
                                            <th @click="store.sortBy('soseo_t')" class="text-end" title="{{ __('Score (Trafilatura)') }}">{{ __('SOSEO') }}&nbsp;T</th>
                                            <th @click="store.sortBy('dseo_t')" class="text-end" title="{{ __('Danger (Trafilatura)') }}">{{ __('DSEO') }}&nbsp;T</th>
                                            <th @click="store.sortBy('soseo_y')" class="text-end" title="{{ __('Score (YourTextGuru)') }}">{{ __('SOSEO') }}&nbsp;Y</th>
                                            <th @click="store.sortBy('dseo_y')" class="text-end" title="{{ __('Score (YourTextGuru)') }}">{{ __('DSEO') }}&nbsp;Y</th>
                                            <th @click="store.sortBy('yourtextguru_rank')" class="text-end" title="{{ __('Rank (YourTextGuru)') }}">{{ __('Rank') }}&nbsp;Y</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="align-middle"
                                        v-if="store.response?.data"
                                        :class="{ 'dataset-loading': store.loading }"
                                    >
                                        <tr v-for="item in store.response.data" :key="item.rank">
                                            <td class="text-end">@{{ item.rank }}</td>
                                            <td><a :href="item.url" target="_blank">@{{ item.url }}</a></td>
                                            <td class="text-end">@{{ item.page_value }}</td>
                                            <td class="text-end">@{{ item.page_trust }}</td>
                                            <td class="text-end">@{{ item.semantic_value }}</td>
                                            <td class="text-end">@{{ item.babbar_authority_score }}</td>
                                            <td class="text-end">@{{ item.soseo_t }}</td>
                                            <td class="text-end">@{{ item.dseo_t }}</td>
                                            <td class="text-end">@{{ item.soseo_y }}</td>
                                            <td class="text-end">@{{ item.dseo_y }}</td>
                                            <td class="text-end">@{{ item.yourtextguru_rank }}</td>
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
