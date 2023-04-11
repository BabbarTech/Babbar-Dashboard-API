<template>
    <span v-on:click="toggle">
        <span v-if="selecting" class="spinner-border spinner-border-sm m-0" role="status">
            <span class="visually-hidden">selecting...</span>
        </span>
        <slot v-else>#</slot>
    </span>
</template>

<script>

    export default {
        props: {
            store: {
                type: Object,
                required: true,
            },
            columnProperty: {
                type: String,
                required: false,
                default: 'id'
            },
        },

        data() {
            return {
                selecting: false
            }
        },
        methods: {
            async toggle() {
                let vm = this;
                vm.selecting = true;

                setTimeout(function () {
                    vm.store.toggleSelection();
                    vm.selecting = false;
                }, 100);

            },
        }
    }
</script>
