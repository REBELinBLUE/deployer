import Backbone from 'backbone';

import routes from '../routes';

export const ONLINE = 0;
export const UNTESTED = 1;
export const OFFLINE = 2;

export default class CheckUrl extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.urls;
  }

  isOnline() {
    return parseInt(this.get('status'), 10) === ONLINE;
  }

  isUntested() {
    return parseInt(this.get('status'), 10) === UNTESTED;
  }

  isOffline() {
    return parseInt(this.get('status'), 10) === OFFLINE;
  }
}

