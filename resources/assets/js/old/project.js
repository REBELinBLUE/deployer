import Server from './components/Server.vue';

(function ($) {

    app.vue = new Vue({
        el: '#app',

        components: {
            Server
        },

        data: {
            servers: []
        },

        computed: {
            hasServers() {
                return this.servers.length > 0;
            }
        }
    });


    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            app.vue.servers.push(data.model);
        }
    });

    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            app.vue.servers.push(data.model);
        }
    });

    app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
        if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
            //app.vue.servers.$remove(data.model);
        }
    });

})(jQuery);
