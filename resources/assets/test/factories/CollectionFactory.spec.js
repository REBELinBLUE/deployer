import { expect, use } from 'chai';
import dirtyChai from 'dirty-chai';

import CollectionFactory from '../../src/factories/CollectionFactory';

use(dirtyChai);

describe('CollectionFactory', () => {
  it('Exports a function', () => {
    expect(CollectionFactory).to.be.a('function');
  });

  it('Returns a class', () => {
    expect(CollectionFactory()).to.be.a('function');
  });
});
