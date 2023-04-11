<x-app-layout title="{{ $project->hostname }} {{ __('ranking-factor.keywords-present') }}">
    <div id="mainContent" class="container">
        <x-header title="{{ __('ranking-factor.keywords-present') }}" :project="$project"></x-header>
            <div class="row mt-4">
                <div class="col">

                    <div v-cloak>
                        <dataset-filters
                            class="bg-white p-3 rounded-2 mb-5 border"
                            v-cloak
                            v-slot="{ filters }"
                        >
                            <div class="row">
                                <div class="col">
                                    @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'keywords'])
                                    @includeIf('partials.filters.SearchIncludeExclude', ['property' => 'urls'])
                                </div>
                                <div class="col">
                                    @includeIf('partials.filters.MinMaxFilter', ['property' => 'rank', 'max' => 100])
                                    @includeIf('partials.filters.MinMaxFilter', ['property' => 'bks', 'max' => 100])
                                </div>
                            </div>
                        </dataset-filters>

                        <dataset
                            api="{{ route('api.v1.ranking-factors.keywords-present', ['project' => $project]) }}"
                            :payload="{{ $mainDatasetPayload }}"
                        >
                            <template #header="{ store }">
                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col">
                                        <h2>Keywords <span class="text-muted">(@{{ store?.response?.data?.length || '...' }})</span></h2>
                                    </div>
                                    <div class="col text-end">
                                        <dataset-action
                                            :actions="{{ $mainActions }}"
                                            :store="store"
                                            v-cloak
                                        >
                                        </dataset-action>
                                    </div>
                                </div>
                            </template>

                            <template v-slot="{store, filters}" >
                                <table class="table table-striped mb-3">
                                    <thead class="bg-white sticky-header">
                                        <tr>
                                            <th @click="store.toggleSelection('keyword_id')" class="cursor" title="{{ __('dataset.selection.toggle.tooltip') }}">#</th>
                                            <th @click="store.sortBy('keywords')">{{ __('Keywords') }}</th>
                                            <th @click="store.sortBy('rank')" class="text-end">{{ __('Ranking') }}</th>
                                            <th @click="store.sortBy('bks', false)" class="text-end">{{ __('BKS') }}</th>
                                            <th @click="store.sortBy('url')">{{ __('Url') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        v-if="store.response?.data"
                                        :class="{ 'dataset-loading': store.loading }"
                                    >
                                        <tr v-for="item in store.response.data" :key="item.id">
                                            <td><input type="checkbox" v-model="store.selections" :value="item.keyword_id"></td>
                                            <td><a :href="'https://www.babbar.tech/keywords/' + item.keywords" target="_blank">@{{ item.keywords }}</a></td>
                                            <td class="text-end">@{{ item.rank }}</td>
                                            <td class="text-end">@{{ item.bks }}</td>
                                            <td>@{{ item.url }}</td>
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
