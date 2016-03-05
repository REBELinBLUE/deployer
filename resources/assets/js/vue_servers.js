var app = app || {};

(function ($) {

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
                    deploy: false,
                    status: '2'
                },
                {
                    id: 2,
                    name: 'cron',
                    user: 'root',
                    ip_address: '127.0.0.1',
                    port: 22,
                    deploy: false,
                    status: '2'
                },
                {
                    id: 3,
                    name: 'db',
                    user: 'root',
                    ip_address: '127.0.0.1',
                    port: 22,
                    deploy: false,
                    status: '2'
                }
            ]
        }
    });

})(jQuery);
