import CollectionFactory from '../factories/CollectionFactory';
import Template from '../models/Template';

const Collection = CollectionFactory(Template);

class TemplateCollection extends Collection {}

export default new TemplateCollection();
