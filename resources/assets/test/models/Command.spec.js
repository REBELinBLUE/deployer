import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import Command from '../../src/models/Command';

use(dirtyChai);

export default () => {
  describe('Command', () => {
    let model;

    const getStep = (step) => {
      switch (step) {
        case 1:
        case 3:
          return 'Clone';
        case 4:
        case 6:
          return 'Install';
        case 7:
        case 9:
          return 'Activate';
        case 10:
        case 12:
          return 'Purge';
        default:
          throw new Error('Unexpected deployment step');
      }
    };

    const beforeSteps = [1, 4, 7, 10];
    const afterSteps = [3, 6, 9, 12];

    beforeEach(() => {
      model = new Command();
    });

    afterSteps.forEach((step) => {
      it(`isAfter returns true when the command is for an "After ${getStep(step)}" step`, () => {
        model.set('step', step);

        expect(model.isAfter()).to.be.true();
      });
    });

    afterSteps.forEach((step) => {
      it(`isBefore returns false when the command is for an "After ${getStep(step)}" step`, () => {
        model.set('step', step);

        expect(model.isBefore()).to.be.false();
      });
    });

    beforeSteps.forEach((step) => {
      it(`isBefore returns true when the command is for an "Before ${getStep(step)}" step`, () => {
        model.set('step', step);

        expect(model.isBefore()).to.be.true();
      });
    });

    beforeSteps.forEach((step) => {
      it(`isAfter returns false when the command is for a "Before ${getStep(step)}" step`, () => {
        model.set('step', step);

        expect(model.isAfter()).to.be.false();
      });
    });
  });
};
