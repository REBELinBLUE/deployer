<script>
    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

    export default {
        props: ['server'],
        template: '#server-template',
        methods: {
            testServer() {
                if (parseInt(this.server.status) === TESTING) {
                    return;
                }

                this.server.status = TESTING;

                var that = this;

                $.ajax({
                    type: 'GET',
                    url: '/servers/' + this.server.id + '/test'
                }).fail(function (response) {
                    that.server.status = FAILED;
                });
            },
            editServer(server) {
                this.$dispatch('edit-item', this.server);
            }
        },
        computed: {
            isTesting() {
                return parseInt(this.server.status) === TESTING;
            },
            state() {
                switch (parseInt(this.server.status)) {
                    case SUCCESSFUL:
                        return 'success';
                    case TESTING:
                        return 'warning';
                    case FAILED:
                        return 'danger';
                }

                return 'primary';
            },
            icon() {
                switch (parseInt(this.server.status)) {
                    case SUCCESSFUL:
                        return 'check';
                    case TESTING:
                        return 'spinner fa-pulse';
                    case FAILED:
                        return 'warning';
                }

                return 'question';
            },
            label() {
                switch (parseInt(this.server.status)) {
                    case SUCCESSFUL:
                        return Lang.get('servers.successful');
                    case TESTING:
                        return Lang.get('servers.testing');
                    case FAILED:
                        return Lang.get('servers.failed');
                }

                return Lang.get('servers.untested');
            }
        }
    };
</script>
