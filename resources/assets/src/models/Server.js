import $ from 'jquery';
import Backbone from 'backbone';

import routes from '../routes';

export const SUCCESSFUL = 0;
export const UNTESTED = 1;
export const FAILED = 2;
export const TESTING = 3;

export default class Server extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.servers;
  }

  isSuccessful() {
    return parseInt(this.get('status'), 10) === SUCCESSFUL;
  }

  isUntested() {
    return parseInt(this.get('status'), 10) === UNTESTED;
  }

  isFailed() {
    return parseInt(this.get('status'), 10) === FAILED;
  }

  isTesting() {
    return parseInt(this.get('status'), 10) === TESTING;
  }

  testConnection() {
    this.set({
      status: TESTING,
    });

    const that = this;
    $.ajax({
      type: 'POST',
      url: `${this.urlRoot}/${this.get('id')}/test`,
    }).fail(() => {
      that.set({
        status: FAILED,
      });
    });
  }
}

