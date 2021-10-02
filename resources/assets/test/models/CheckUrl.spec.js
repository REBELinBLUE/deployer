import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import CheckUrl, { OFFLINE, ONLINE, UNTESTED } from '../../src/models/CheckUrl';

use(dirtyChai);

export default () => {
  describe('CheckUrl', () => {
    let model;

    beforeEach(() => {
      model = new CheckUrl();
    });

    it('isOnline returns false when status is not online', () => {
      model.set('status', OFFLINE);

      expect(model.isOnline()).to.be.false();
    });

    it('isOnline returns true when status is online', () => {
      model.set('status', ONLINE);

      expect(model.isOnline()).to.be.true();
    });

    it('isUntested returns false when status is not untested', () => {
      model.set('status', OFFLINE);

      expect(model.isUntested()).to.be.false();
    });

    it('isUntested returns true when status is untested', () => {
      model.set('status', UNTESTED);

      expect(model.isUntested()).to.be.true();
    });

    it('isOffline returns false when status is not offline', () => {
      model.set('status', ONLINE);

      expect(model.isOffline()).to.be.false();
    });

    it('isOffline returns true when status is untested', () => {
      model.set('status', OFFLINE);

      expect(model.isOffline()).to.be.true();
    });
  });
};
