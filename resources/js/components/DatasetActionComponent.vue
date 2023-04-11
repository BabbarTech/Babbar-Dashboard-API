<template>
    <div>
        <form @submit.prevent="storeInstance.fireAction()" class="" v-if="storeInstance">
            <div class="d-inline-block">
                <label class="visually-hidden">Action</label>
                <select class="form-select form-select-sm bg-white" v-model="storeInstance.action">
                    <option selected :value="null">{{ placeholder }}</option>
                    <option v-for="handler in actions" :value="handler" :ref="handler.handler">
                        {{ handler.title}}
                    </option>
                </select>
            </div>
            <div class="d-inline-block">
                <slot name="buttons" :store="store">
                    <button
                        class="btn btn-primary btn-sm"
                        :disabled="isDisabled"
                    >
                        <span v-if="store.processingAction" class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        {{ btnLabel }}
                    </button>
                </slot>
            </div>
            <slot></slot>
        </form>
    </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useDatasetCollectionStore } from '../stores/datasetCollection'

export default {
    name: 'DatasetAction',
    props: {
        datasetId: {
            type: String,
            required: false,
            default: 'main'
        },

        store: {
            type: Object
        },

        actions: {
            type: Array,
            required: true,
        },

        btnLabel: {
            type: String,
            default: 'Submit',
        },

        placeholder: {
            type: String,
            default: 'Select action...',
        }
    },
    data() {
        return {
            storeInstance: this.store || null,
        }
    },
    computed: {
        ...mapStores(useDatasetCollectionStore),
        selections() {
            return this.storeInstance?.selections || []
        },
        action() {
            return this.storeInstance.action
        },
        isDisabled() {
            if (! this.action) {
                return true;
            }

            return false; // this.selections.length === 0;
        }
    },
    mounted () {
        let vm = this;

        this.datasetCollectionStore.$subscribe((mutation, state) => {
            if (!vm.storeInstance) {
                vm.$set(storeInstance, vm.datasetCollectionStore.datasets[vm.datasetId]);
                //vm.storeInstance = vm.datasetCollectionStore.datasets[vm.datasetId]
            }
        })
    }
}
</script>
