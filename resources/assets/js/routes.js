import App from './Containers/App';
import Dashboard from './Containers/Dashboard';
import Profile from './Containers/Profile';

import UserAdmin from './Containers/Admin/Users';
import GroupAdmin from './Containers/Admin/Groups';
import TemplateAdmin from './Containers/Admin/Templates';
import ProjectAdmin from './Containers/Admin/Projects';

const routes = {
  path: '/',
  component: App,
  indexRoute: { component: Dashboard },
  childRoutes: [
    { path: 'profile', component: Profile },
    { path: 'admin/users', component: UserAdmin },
    { path: 'admin/groups', component: GroupAdmin },
    { path: 'admin/templates', component: TemplateAdmin },
    { path: 'admin/projects', component: ProjectAdmin },
  ],
};

export default routes;
