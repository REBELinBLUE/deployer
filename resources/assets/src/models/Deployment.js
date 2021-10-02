import Backbone from 'backbone';

export const COMPLETED = 0;
export const PENDING = 1;
export const RUNNING = 2;
export const FAILED = 3;
export const ERRORS = 4;
export const CANCELLED = 5;

export default class Deployment extends Backbone.Model {
  isCompleted() {
    return parseInt(this.get('status'), 10) === COMPLETED;
  }

  isPending() {
    return parseInt(this.get('status'), 10) === PENDING;
  }

  isFailed() {
    return parseInt(this.get('status'), 10) === FAILED;
  }

  isRunning() {
    return parseInt(this.get('status'), 10) === RUNNING;
  }

  isCancelled() {
    return parseInt(this.get('status'), 10) === CANCELLED;
  }

  isCompleteWithErrors() {
    return parseInt(this.get('status'), 10) === ERRORS;
  }
}

