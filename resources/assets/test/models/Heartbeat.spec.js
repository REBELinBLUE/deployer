import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Heartbeat, { OK, UNTESTED, MISSING } from '../../src/models/Heartbeat';

use(dirtyChai);

export default () => {
  describe('Heartbeat', () => {
    let model;

    beforeEach(() => {
      model = new Heartbeat();
    });

    it('isOK returns false when status is not OK', () => {
      model.set('status', MISSING);

      expect(model.isOK()).to.be.false();
    });

    it('isOK returns true when status is OK', () => {
      model.set('status', OK);

      expect(model.isOK()).to.be.true();
    });

    it('isUntested returns false when status is not untested', () => {
      model.set('status', MISSING);

      expect(model.isUntested()).to.be.false();
    });

    it('isUntested returns true when status is untested', () => {
      model.set('status', UNTESTED);

      expect(model.isUntested()).to.be.true();
    });

    it('isMissing returns false when status is not missing', () => {
      model.set('status', OK);

      expect(model.isMissing()).to.be.false();
    });

    it('isMissing returns true when status is missing', () => {
      model.set('status', MISSING);

      expect(model.isMissing()).to.be.true();
    });
  });
};
