import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Project, { FINISHED, PENDING, DEPLOYING, FAILED, NOT_DEPLOYED } from '../../src/models/Project';

use(dirtyChai);

export default () => {
  describe('Project', () => {
    let model;

    beforeEach(() => {
      model = new Project();
    });

    it('isFinished returns false when status is not finished', () => {
      model.set('status', NOT_DEPLOYED);

      expect(model.isFinished()).to.be.false();
    });

    it('isFinished returns true when status is finished', () => {
      model.set('status', FINISHED);

      expect(model.isFinished()).to.be.true();
    });

    it('isPending returns false when status is not pending', () => {
      model.set('status', NOT_DEPLOYED);

      expect(model.isPending()).to.be.false();
    });

    it('isPending returns true when status is pending', () => {
      model.set('status', PENDING);

      expect(model.isPending()).to.be.true();
    });

    it('isDeploying returns false when status is not deploying', () => {
      model.set('status', NOT_DEPLOYED);

      expect(model.isDeploying()).to.be.false();
    });

    it('isDeploying returns true when status is deploying', () => {
      model.set('status', DEPLOYING);

      expect(model.isDeploying()).to.be.true();
    });

    it('isFailed returns false when status is not failed', () => {
      model.set('status', NOT_DEPLOYED);

      expect(model.isFailed()).to.be.false();
    });

    it('isFailed returns true when status is failed', () => {
      model.set('status', FAILED);

      expect(model.isFailed()).to.be.true();
    });

    it('isNotDeployed returns false when status is not not deployed', () => {
      model.set('status', PENDING);

      expect(model.isNotDeployed()).to.be.false();
    });

    it('isNotDeployed returns true when status is not deployed', () => {
      model.set('status', NOT_DEPLOYED);

      expect(model.isNotDeployed()).to.be.true();
    });
  });
};
