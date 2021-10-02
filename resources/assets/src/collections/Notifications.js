import CollectionFactory from '../factories/CollectionFactory';
import Notification from '../models/Notification';

const Collection = CollectionFactory(Notification);

class NotificationCollection extends Collection {}

export default new NotificationCollection();
