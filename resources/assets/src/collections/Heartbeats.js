import CollectionFactory from '../factories/CollectionFactory';
import Heartbeat from '../models/Heartbeat';

const Collection = CollectionFactory(Heartbeat);

class HeartbeatCollection extends Collection {}

export default new HeartbeatCollection();
