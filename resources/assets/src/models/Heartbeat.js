import Backbone from 'backbone';

import routes from '../routes';

export const OK = 0;
export const UNTESTED = 1;
export const MISSING = 2;

export default class Heartbeat extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.heartbeats;
  }

  isOK() {
    return parseInt(this.get('status'), 10) === OK;
  }

  isUntested() {
    return parseInt(this.get('status'), 10) === UNTESTED;
  }

  isMissing() {
    return parseInt(this.get('status'), 10) === MISSING;
  }
}

