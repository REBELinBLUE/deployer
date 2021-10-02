import CollectionFactory from '../factories/CollectionFactory';
import Variable from '../models/Variable';

const Collection = CollectionFactory(Variable);

class VariableCollection extends Collection {}

export default new VariableCollection();
