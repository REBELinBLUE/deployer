import Backbone from 'backbone';

import routes from '../routes';

export default class Variable extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.variables;
  }
}

