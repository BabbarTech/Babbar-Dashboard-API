<!-- Vue component -->
<template>
    <multiselect
        v-model="selected"
        :options="options"
        :taggable="taggable"
        :multiple="multiple"
        :label="label"
        :track-by="trackBy"
        :placeholder="placeholder"
    >
    </multiselect>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
    components: { Multiselect },
    props: {
        value: {
            required: true,
        },
        options: {
            type: Array,
        },
        multiple: {
            type: Boolean,
            required: false,
            default: false,
        },
        taggable: {
            type: Boolean,
            required: false,
            default: false,
        },
        label: {
            type: String,
            required: false,
            default: 'label',
        },
        trackBy: {
            type: String,
            required: false,
            default: 'value',
        },
        placeholder: {
            type: String,
            required: false,
            default: 'Select option(s)',
        },
    },

    data: function () {
        return {
            selected: null,
        }
    },

    watch: {
        selected(value) {
            if (value instanceof Array) {
                let collection = value.map(o => o[this.trackBy]);
                this.$emit('input', collection);
            } else {
                this.$emit('input', value);
            }
        }
    }
}
</script>
