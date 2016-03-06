import Server from './components/Server.vue';

window.app = {};

(function ($) {
    // Don't need to try and connect to the web socket when not logged in
    if (window.location.href.match(/login|password/) != null) {
        return;
    }

    Lang.setLocale($('meta[name="locale"]').attr('content'));

    // app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
    //     if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
    //         app.servers.servers.push(data.model);
    //     }
    // });

    // app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
    //     if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
    //         app.servers.servers.push(data.model);
    //     }
    // });

    // app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
    //     if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
    //         //app.servers.servers.$remove(data.model);
    //     }
    // });

    window.app = new Vue({
        el: '#app',

        data: {
            is_new: false,
            warning: false,
            current: null,
            project_id: null,
            servers: []
        },

        components: {
            Server
        },

        events: {
            'edit-item': function(item) {
                this.current = item;
                this.is_new = false;
                this.warning = false;
            }
        },

        computed: {
            hasServers() {
                return this.servers.length > 0;
            }
        },

        methods: {
            newItem() {
                this.current = null;
                this.is_new = true;
                this.warning = false;
            }
        }
    });

})(jQuery);
