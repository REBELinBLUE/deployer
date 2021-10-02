import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import { getProjectId, setProjectId } from '../../src/utils/projectId';

use(dirtyChai);

export default () => {
  describe('Current project', () => {
    it('Returns null when project ID is not set', () => {
      expect(getProjectId()).to.be.null();
    });

    it('Returns the project ID', () => {
      const expected = 12345;

      setProjectId(expected);

      expect(getProjectId()).to.be.equal(expected);
    });
  });
};
