import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Notification, { SLACK, MAIL, TWILIO } from '../../src/models/Notification';

use(dirtyChai);

export default () => {
  describe('Notification', () => {
    let model;

    beforeEach(() => {
      model = new Notification();
    });

    it('isSlack returns false when type is not slack', () => {
      model.set('type', TWILIO);

      expect(model.isSlack()).to.be.false();
    });

    it('isSlack returns true when status is slack', () => {
      model.set('type', SLACK);

      expect(model.isSlack()).to.be.true();
    });

    it('isMail returns false when type is not e-mail', () => {
      model.set('type', SLACK);

      expect(model.isMail()).to.be.false();
    });

    it('isMail returns true when status is e-mail', () => {
      model.set('type', MAIL);

      expect(model.isMail()).to.be.true();
    });

    it('isTwilio returns false when type is not twilio', () => {
      model.set('type', MAIL);

      expect(model.isTwilio()).to.be.false();
    });

    it('isTwilio returns true when status is twilio', () => {
      model.set('type', TWILIO);

      expect(model.isTwilio()).to.be.true();
    });
  });
};
