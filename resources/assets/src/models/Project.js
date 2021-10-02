import Backbone from 'backbone';

import routes from '../routes';

export const FINISHED = 0;
export const PENDING = 1;
export const DEPLOYING = 2;
export const FAILED = 3;
export const NOT_DEPLOYED = 4;

export default class Project extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.projects;
  }

  isFinished() {
    return parseInt(this.get('status'), 10) === FINISHED;
  }

  isPending() {
    return parseInt(this.get('status'), 10) === PENDING;
  }

  isDeploying() {
    return parseInt(this.get('status'), 10) === DEPLOYING;
  }

  isFailed() {
    return parseInt(this.get('status'), 10) === FAILED;
  }

  isNotDeployed() {
    return parseInt(this.get('status'), 10) === NOT_DEPLOYED;
  }
}

