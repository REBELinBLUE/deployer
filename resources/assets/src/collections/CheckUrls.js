import CollectionFactory from '../factories/CollectionFactory';
import CheckUrl from '../models/CheckUrl';

const Collection = CollectionFactory(CheckUrl);

class CheckUrlCollection extends Collection {}

export default new CheckUrlCollection();
