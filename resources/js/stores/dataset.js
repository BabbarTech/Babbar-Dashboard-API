import { defineStore } from 'pinia'

export const useDatasetStore = function (name) {
    return defineStore({
        id: 'dataset-' + name,

        state: () => ({
            default: {
                api: null,
                payload: {}
            },
            api: null,
            payload: {
                filters: null,
                orderBy: null,
                page: null,
            },
            response: null,
            selected: null,
            selections: [],
            loading: false,
            processingAction: false,
            error: false,
            action: null,
            params: {}
        }),
        getters: {
            defaultFilters(states) {
                return states.default.payload?.filters
            },
            filters(states) {
                return states.payload?.filters
            },
        },
        actions: {
            init(api, defaultPayload) {
                this.api = api
                this.default.api = api
                this.default.payload = defaultPayload

                if (defaultPayload) {
                    this.payload = JSON.parse(JSON.stringify(defaultPayload))
                }
                //this.datasetCollectionStore.add(this.datasetId, this.store)
            },

            select(selected) {
                if (JSON.stringify(this.selected) === JSON.stringify(selected)) {
                    this.unselect();
                } else {
                    this.selected = selected
                }
            },

            unselect() {
                this.selected = null
            },

            toggleSelection(columnProperty = 'id') {
                let currentSelections = this.selections;
                // Reset selection
                this.selections = [];
                this.response?.data?.forEach(item => {
                    let value = item[columnProperty];
                    if (! currentSelections.includes(value)) {
                        this.selections.push(value);
                    }
                })
            },

            resetFilters(processFetching = true) {
                this.payload.filters = JSON.parse(JSON.stringify(this.defaultFilters))

                if (processFetching) {
                    this.fetch()
                }
            },

            makeApiUrl: function(parameters) {
                if (parameters instanceof Object) {

                    // Reset api url
                    this.api = this.default.api

                    for(let p in parameters) {
                        let pattern = new RegExp('%' + p + '%');

                        if (this.api.match(pattern)) {
                            // Replace parameters into url if found %pattern% into uri
                            this.api = this.api.replace(pattern, parameters[p])
                        } else {
                            // Or add to http query parameters
                            this.payload[p] = parameters[p]
                        }
                    }
                }

                return this.api + '?' + this.http_build_query(this.payload);
            },

            filtering: async function() {
                return await this.fetch()
            },

            fireAction: async function() {
                let vm = this

                vm.processingAction = true
                vm.error = null

                let selections = (this.selections.length === this.response?.data?.length) ?
                    null : this.selections

                let formParam = {
                    _method: 'get',
                    action: {
                        handler: this.action?.handler,
                        params: this.action?.params,
                        selections: selections,
                    },
                    ...this.payload
                };

                return await axios.post(this.api, formParam).then(response => {
                    let message = response.data.message || 'Action submitted';

                    // If respose has attachement, download
                    if (response.headers['content-disposition']) {
                        return vm.download(response);
                    }

                    vm.$toasted.success(message);
                }).catch(function (error) {
                    let message = error.response.data?.message || error.response.data?.exception || 'An error occurred';
                    vm.$toasted.error(message);
                }).finally(() => {
                    vm.processingAction = false
                });
            },

            download: function(response)
            {
                let filename = response.headers['content-disposition'].match(/filename=(.*)/)[1];

                let link = document.createElement('a');
                let url;

                if (response.headers['stream-download-url'] !== undefined) {
                    url = response.headers['stream-download-url'];
                } else {
                    url = window.URL.createObjectURL(new Blob([response.data]));
                    link.setAttribute('download', filename);
                }

                link.href = url;
                document.body.appendChild(link);
                link.click();
                URL.revokeObjectURL(url);
                link.remove();
            },

            fetch: async function(parameters) {
                let vm = this

                vm.loading = true
                vm.error = null

                let url = this.makeApiUrl(parameters)

                return await axios.get(url).then(response => {
                    vm.response = response.data
                    return response.data;
                }).catch(function (error) {
                    if (error.response) {
                        vm.error = error.response.data?.message || error.response.data?.exception || 'An error occurred';
                    } else {
                        vm.error = 'An error occurred'
                    }
                }).finally(() => {
                    vm.loading = false
                });
            },

            scrollToTop: function() {
                let top = window.document.getElementById(this.$id);
                if (top) {
                    top.scrollIntoView({behavior: 'auto'})
                }
            },

            sortBy: async function(column, ascending = true) {
                this.scrollToTop()
                this.loading = true
                let vm = this;

                let currentOrderBy = this.payload?.orderBy;
                let direction = ascending;

                if (currentOrderBy) {
                    // Todo : handle multiple order at same time ?

                    let currentColumn = currentOrderBy.replace(/^-/, '')
                    direction = !currentOrderBy.startsWith('-')

                    if (currentColumn === column) {
                        direction = !direction
                    } else {
                        direction = ascending
                    }
                }

                this.payload['orderBy'] = direction ? column : '-' + column;

                this.$patch({payload: this.payload})

                setTimeout(function() {
                    vm.filtering();
                }, 250)
            },

            http_build_query: function(params = {}, prefix) {
                const query = Object.keys(params).map((k) => {
                    let key = k;
                    let value = params[key];

                    if (!value && (value === null || value === undefined || isNaN(value))) {
                        value = '';
                    }

                    switch (params.constructor) {
                        case Array:
                            key = `${prefix}[]`;
                            break;
                        case Object:
                            key = (prefix ? `${prefix}[${key}]` : key);
                            break;
                    }

                    if (typeof value === 'object') {
                        return this.http_build_query(value, key); // for nested objects
                    }

                    return `${key}=${encodeURIComponent(value)}`;
                });

                return query.join('&');
            },
        },
    })
}
