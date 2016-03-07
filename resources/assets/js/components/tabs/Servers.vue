<script>
    import Server from '../items/Server.vue';

    export default {
        props: ['servers'],

        methods: {
            newItem() {
                this.$dispatch('add-item', {
                    name: '',
                    user: '',
                    ip_address: '',
                    port: 22,
                    path: '',
                    deploy_code: true
                });
            }
        },

        components: {
            Server
        },

        computed: {
            hasServers() {
                return this.servers.length > 0;
            }
        },

        ready() {
            window.socket.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                if (parseInt(data.model.project_id) === parseInt(window.app.project_id)) {
                    window.app.servers.push(data.model);
                }
            });

            window.socket.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                if (parseInt(data.model.project_id) === parseInt(window.app.project_id)) {
                    window.app.servers.push(data.model);
                }
            });

            window.socket.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                if (parseInt(data.model.project_id) === parseInt(window.app.project_id)) {
                    //window.app.servers.$remove(data.model);
                }
            });
        }
    };
</script>
