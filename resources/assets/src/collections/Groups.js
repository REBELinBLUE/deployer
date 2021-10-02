import CollectionFactory from '../factories/CollectionFactory';
import Group from '../models/Group';

const Collection = CollectionFactory(Group);

class GroupCollection extends Collection {}

export default new GroupCollection();
