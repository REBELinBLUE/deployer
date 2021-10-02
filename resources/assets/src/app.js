import './bootstrap';
import views from './views';
import models from './models';
import collections from './collections';
import listener from './listener';
import { getProjectId, setProjectId } from './utils/projectId';


window.app = {
  views,
  models,
  collections,
  listener,
  getProjectId,
  setProjectId,
};
