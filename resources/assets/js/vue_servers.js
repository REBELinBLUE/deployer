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

            }
        },
        computed: {
            hasServers: function() {
                return this.servers.length > 0;
            }
        }
    });

})(jQuery);
