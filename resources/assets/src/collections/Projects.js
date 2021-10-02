import CollectionFactory from '../factories/CollectionFactory';
import Project from '../models/Project';

const Collection = CollectionFactory(Project);

class ProjectCollection extends Collection {}

export default new ProjectCollection();
