import CollectionFactory from '../factories/CollectionFactory';
import Log from '../models/Log';

const Collection = CollectionFactory(Log);

class LogCollection extends Collection {}

export default new LogCollection();
