import CollectionFactory from '../factories/CollectionFactory';
import Server from '../models/Server';

const Collection = CollectionFactory(Server);

class ServerCollection extends Collection {}

export default new ServerCollection();
