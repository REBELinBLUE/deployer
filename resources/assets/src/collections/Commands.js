import CollectionFactory from '../factories/CollectionFactory';
import Command from '../models/Command';

const Collection = CollectionFactory(Command);

class CommandCollection extends Collection {}

export default new CommandCollection();
