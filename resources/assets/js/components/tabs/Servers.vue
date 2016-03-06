<script>
    import Server from '../items/Server.vue';

    export default {
        data: function() {
            return {
                servers: [
                    {id:1,name:'test', path:'/var', ip_address:'127.0.0.1',deploy_code:true,order:1,user:'root',status:2}
                ]
            }
        },

        methods: {
            newItem() {
                alert('here');
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
    };
</script>
