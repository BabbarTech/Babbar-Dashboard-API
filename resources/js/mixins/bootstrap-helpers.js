export default {
    methods: {
        initTooltips() {
            this.$nextTick(function() {
                let tooltipTriggerList = [].slice.call(this.$el.querySelectorAll('[data-bs-toggle="tooltip"]'))

                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            })
        },

        initTabs() {
            let vm = this;
            this.$nextTick(function() {
                let tabTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))

                tabTriggerList.map(function(tabEl) {
                    tabEl.addEventListener('show.bs.tab', function (event) {
                        vm.initTooltips();
                    })
                });
            })
        },

    },
    mounted() {
        let vm = this;

        vm.initTooltips();
        vm.initTabs();
    }
}
