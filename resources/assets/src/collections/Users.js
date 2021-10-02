import CollectionFactory from '../factories/CollectionFactory';
import User from '../models/User';

const Collection = CollectionFactory(User);

class UserCollection extends Collection {}

export default new UserCollection();
