import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Deployment, { COMPLETED, PENDING, RUNNING, FAILED, ERRORS, CANCELLED, } from '../../src/models/Deployment';

use(dirtyChai);

export default () => {
  describe('Deployment', () => {
    let model;

    beforeEach(() => {
      model = new Deployment();
    });

    it('isCompleted returns false when status is not completed', () => {
      model.set('status', PENDING);

      expect(model.isCompleted()).to.be.false();
    });

    it('isCompleted returns true when status is completed', () => {
      model.set('status', COMPLETED);

      expect(model.isCompleted()).to.be.true();
    });

    it('isPending returns false when status is not pending', () => {
      model.set('status', COMPLETED);

      expect(model.isPending()).to.be.false();
    });

    it('isPending returns true when status is pending', () => {
      model.set('status', PENDING);

      expect(model.isPending()).to.be.true();
    });

    it('isFailed returns false when status is not failed', () => {
      model.set('status', COMPLETED);

      expect(model.isFailed()).to.be.false();
    });

    it('isFailed returns true when status is failed', () => {
      model.set('status', FAILED);

      expect(model.isFailed()).to.be.true();
    });

    it('isRunning returns false when status is not running', () => {
      model.set('status', ERRORS);

      expect(model.isRunning()).to.be.false();
    });

    it('isRunning returns true when status is running', () => {
      model.set('status', RUNNING);

      expect(model.isRunning()).to.be.true();
    });

    it('isCancelled returns false when status is not cancelled', () => {
      model.set('status', ERRORS);

      expect(model.isCancelled()).to.be.false();
    });

    it('isCancelled returns true when status is cancelled', () => {
      model.set('status', CANCELLED);

      expect(model.isCancelled()).to.be.true();
    });

    it('isCompleteWithErrors returns false when status is not completed with errors', () => {
      model.set('status', RUNNING);

      expect(model.isCompleteWithErrors()).to.be.false();
    });

    it('isCompleteWithErrors returns true when status is completed with errors', () => {
      model.set('status', ERRORS);

      expect(model.isCompleteWithErrors()).to.be.true();
    });


  });
};
