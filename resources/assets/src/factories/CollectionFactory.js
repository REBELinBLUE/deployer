import Backbone from 'backbone';

export default model =>
  class Collection extends Backbone.Collection {
    constructor(options) {
      super(options);

      this.model = model;
    }
  };
