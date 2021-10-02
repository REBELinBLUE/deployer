import Backbone from 'backbone';

import routes from '../routes';

export default class Group extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.groups;
  }
}
