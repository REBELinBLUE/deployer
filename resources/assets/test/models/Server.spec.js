import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Server, { SUCCESSFUL, UNTESTED, FAILED, TESTING } from '../../src/models/Server';

use(dirtyChai);

export default () => {
  describe('Server', () => {
    let model;

    beforeEach(() => {
      model = new Server();
    });

    it('isSuccessful returns false when status is not successful', () => {
      model.set('status', FAILED);

      expect(model.isSuccessful()).to.be.false();
    });

    it('isSuccessful returns true when status is successful', () => {
      model.set('status', SUCCESSFUL);

      expect(model.isSuccessful()).to.be.true();
    });

    it('isUntested returns false when status is not untested', () => {
      model.set('status', SUCCESSFUL);

      expect(model.isUntested()).to.be.false();
    });

    it('isUntested returns true when status is untested', () => {
      model.set('status', UNTESTED);

      expect(model.isUntested()).to.be.true();
    });

    it('isFailed returns false when status is not failed', () => {
      model.set('status', SUCCESSFUL);

      expect(model.isFailed()).to.be.false();
    });

    it('isFailed returns true when status is failed', () => {
      model.set('status', FAILED);

      expect(model.isFailed()).to.be.true();
    });

    it('isTesting returns false when status is not testing', () => {
      model.set('status', SUCCESSFUL);

      expect(model.isTesting()).to.be.false();
    });

    it('isTesting returns true when status is testing', () => {
      model.set('status', TESTING);

      expect(model.isTesting()).to.be.true();
    });
  });
};
