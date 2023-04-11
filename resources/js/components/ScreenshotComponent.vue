<template>
    <button @click="capture" data-html2canvas-ignore :disabled="processing">
        <slot></slot>
        <span v-if="processing" class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">processing...</span>
        </span>
    </button>
</template>

<script>
import html2canvas from 'html2canvas';
var slugify = require('slugify')

export default {
        props: {
            captureContentId: {
                type: String,
                required: true,
            },
            filename: {
                type: String,
                default: 'capture',
            },
            current: {
                type: Object,
            }
        },
        data() {
            return {
                processing: false,
            }
        },
        methods: {
            async capture() {
                this.$emit('capturing')
                let vm = this;
                let containerToCapture = window.document.getElementById(this.captureContentId);

                if (! containerToCapture) {
                    console.error('Can not find div to capture #' + this.captureContentId);
                    return ;
                }

                vm.processing = true;
                html2canvas(containerToCapture).then(function(canvas) {
                    vm.saveAs(canvas.toDataURL());
                    vm.processing = false
                    vm.$emit('captured')
                });
            },

            saveAs(uri, filename) {
                let  link = document.createElement('a');

                if (typeof link.download === 'string') {
                    link.href = uri;
                    link.download = this.getFilename();

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    window.open(uri);
                }
            },

            getFilename() {
                let vm = this;
                return slugify(this.filename.replace(/%(\w+)%/g, function(_,k)
                {
                    if (vm.current) {
                        let value = vm.current[k] ?? '';
                        if (typeof value === 'string') {
                            return value.replace(/^https?:\/\//, '')
                        }
                        if (typeof value === 'number') {
                            return value;
                        }

                        return '';
                    }

                    return '';
                }).replace(/__+/g, '__'));
            }
        }
    }
</script>
