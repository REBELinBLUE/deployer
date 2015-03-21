var Server = Backbone.Model.extend({
    urlRoot: '/servers',
    defaults: {
        'status':  'Untested'
    }
});

/*
        var ArrayStorage = function(){
            this.storage = {};
        };
        ArrayStorage.prototype.get = function(key)
        {
            return this.storage[key];
        };
        ArrayStorage.prototype.set = function(key, val)
        {
            return this.storage[key] = val;
        };

      var BaseView = Backbone.View.extend({
            templateDriver: new ArrayStorage,
            viewPath: window.siteUrl + 'views/',
            template: function()
            {
                var view, data, template, self;

                switch(arguments.length)
                {
                    case 1:
                        view = this.view;
                        data = arguments[0];
                        break;
                    case 2:
                        view = arguments[0];
                        data = arguments[1];
                        break;
                }

                template = this.getTemplate(view, false);
                self = this;

                return template(data, function(partial)
                {
                    return self.getTemplate(partial, true);
                });
            },
            getTemplate: function(view, isPartial)
            {
                return this.templateDriver.get(view) || this.fetch(view, isPartial);
            },
            setTemplate: function(name, template)
            {
                return this.templateDriver.set(name, template);
            },
            fetch: function(view, isPartial)
            {
                var markup = $.ajax({
                    async: false,
                    url: this.viewPath + view.split('.').join('/') + '.mustache'
                }).responseText;

                return isPartial
                    ? markup
                    : this.setTemplate(view, Mustache.compile(markup));
            }
        });

var Server = BaseView.extend({
    view: 'servers.index',
    initialize: function() {
        this.fetching = this.collection.fetch();
    },
    render: function() {
        var self = this;
        this.fetching.done(function() {
            self.$el.html('');
            self.addServers();
        });
    },
    paginate: function() {
        var servers;

        servers = this.collection.rest(this.perPage * this.page);
        servers = _.first(servers, this.perPage);
        this.page++;

        return servers;
    },
    addServers: function() {
        var servers = this.paginate();

        for (var i = 0; i < servers.length; i++) {
            this.addOneServer(servers[i]);
        }
    },
    addOneServer: function(model) {
        var view = new ServerViewPartial({
            model: model
        });

        this.$el.append(view.render().el);
    },
    showPost: function(id) {
        var self = this;

        this.fetching.done(function () {
            var model = self.collection.get(id);

            if (!self.serverView) {
                self.serverView = new self.options.serverView({
                    el: self.el
                });
            }

            self.serverView.model = model;
            self.serverView.render();
        });
    }
});

var ServerView = BaseView.extend({
    view: 'servers.show',
    events: {

    },
    render: function() {
        var self = this;

        self.$el.html(this.template({
            post: this.model.attributes
        }));
    }
});

var ServerViewPartial = BaseView.extend({
    view: 'servers._server',
    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    }
});
*/
