var app = app || {};

(function ($) {
    var SUCCESSFUL = 0;
    var UNTESTED   = 1;
    var FAILED     = 2;
    var TESTING    = 3;

    app.servers = new Vue({
        el: '#manage-servers',

        data: {
            servers: [
                {
                    id: 1,
                    name: 'web',
                    user: 'root',
                    ip_address: '127.0.0.1',
                    port: 22,
                    deploy: true,
                    status: 0
                },
                {
                    id: 2,
                    name: 'cron',
                    user: 'root',
                    ip_address: '127.0.0.1',
                    port: 22,
                    deploy: true,
                    status: 2
                },
                {
                    id: 3,
                    name: 'db',
                    user: 'root',
                    ip_address: '127.0.0.1',
                    port: 22,
                    deploy: false,
                    status: 1
                }
            ]
        },

        computed: {
            hasServers: function() {
                return this.servers.length > 0;
            }
        },

        beforeCompile: function() {
            this.calculateStatus();
        },

        methods: {
            calculateStatus: function () {
                this.servers.forEach(function (server) {
                    server.status_css   = 'primary';
                    server.icon_css     = 'question';
                    server.status_label = Lang.get('servers.untested');
                    server.isTesting    = false;

                    if (parseInt(server.status) === SUCCESSFUL) {
                        server.status_css   = 'success';
                        server.icon_css     = 'check';
                        server.status_label = Lang.get('servers.successful');
                    } else if (parseInt(server.status) === TESTING) {
                        server.status_css   = 'warning';
                        server.icon_css     = 'spinner fa-pulse';
                        server.status_label = Lang.get('servers.testing');
                        server.server.isTesting    = true;
                    } else if (parseInt(server.status) === FAILED) {
                        server.status_css   = 'danger';
                        server.icon_css     = 'warning';
                        server.status_label = Lang.get('servers.failed');
                    }
                });
            }
        },

    });

})(jQuery);
