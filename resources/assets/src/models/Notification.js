import Backbone from 'backbone';

import routes from '../routes';

export const SLACK = 'slack';
export const HIPCHAT = 'hipchat';
export const MAIL = 'mail';
export const TWILIO = 'twilio';

export default class Notification extends Backbone.Model {
  constructor(attributes, options) {
    super(attributes, options);

    this.urlRoot = routes.notifications;
  }

  isSlack() {
    return this.get('type') === SLACK;
  }

  isHipchat() {
    return this.get('type') === HIPCHAT;
  }

  isMail() {
    return this.get('type') === MAIL;
  }

  isTwilio() {
    return this.get('type') === TWILIO;
  }
}

