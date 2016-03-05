var app = app || {};

(function ($) {
    var SUCCESSFUL = 0;
    var UNTESTED   = 1;
    var FAILED     = 2;
    var TESTING    = 3;

    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            app.servers.servers.push(data.model);
        }
    });

    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            app.servers.servers.push(data.model);
        }
    });

    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            //app.servers.servers.$remove(data.model);
        }
    });

    app.servers = new Vue({
        el: '#manage-servers',

        data: {
            servers: []
        },

        components: {
            server: {
                props: ['server'],
                template: '#server-template',
                methods: {
                    testServer: function() {
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
                    }
                },
                computed: {
                    isTesting: function () {
                        return parseInt(this.server.status) === TESTING;
                    },
                    state: function () {
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
                    icon: function () {
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
                    label: function () {
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
            }
        },
        computed: {
            hasServers: function() {
                return this.servers.length > 0;
            }
        }
    });

})(jQuery);
