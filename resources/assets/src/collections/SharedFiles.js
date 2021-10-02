import CollectionFactory from '../factories/CollectionFactory';
import SharedFile from '../models/SharedFile';

const Collection = CollectionFactory(SharedFile);

class SharedFileCollection extends Collection {}

export default new SharedFileCollection();
