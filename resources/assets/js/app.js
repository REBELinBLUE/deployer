import ServerList from './components/tabs/Servers.vue';

// This is horrible, but if I don't use window. I can't access them
window.app = {};
window.socket = null;

(function ($) {
    // Don't need to try and connect to the web socket when not logged in
    if (window.location.href.match(/login|password/) != null) {
        return;
    }

    Lang.setLocale($('meta[name="locale"]').attr('content'));

    window.socket = io.connect($('meta[name="socket_url"]').attr('content'), {
        query: 'jwt=' + $('meta[name="jwt"]').attr('content')
    });

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
            ServerList
        },

        events: {
            'edit-item': function(item) {
                this.setupItem(item, false);
            },
            'add-item': function(item) {
                this.setupItem(item, true);
            }
        },

        methods: {
            setupItem(item, is_new) {
                this.current = item;
                this.is_new = is_new;
                this.warning = false;
            }
        }
    });

})(jQuery);
