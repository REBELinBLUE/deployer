import CollectionFactory from '../factories/CollectionFactory';
import ConfigFile from '../models/ConfigFile';

const Collection = CollectionFactory(ConfigFile);

class ConfigFileCollection extends Collection {}

export default new ConfigFileCollection();
