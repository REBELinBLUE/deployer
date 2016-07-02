import Dashboard from './Containers/Dashboard';
import Profile from './Containers/Profile';

import UserAdmin from './Containers/Admin/Users';
import GroupAdmin from './Containers/Admin/Groups';
import TemplateAdmin from './Containers/Admin/Templates';
import ProjectAdmin from './Containers/Admin/Projects';

const indexRoute = { url: '/', component: Dashboard };

const routes = [
  { url: '/profile', component: Profile },
  { url: '/admin/users', component: UserAdmin },
  { url: '/admin/groups', component: GroupAdmin },
  { url: '/admin/templates', component: TemplateAdmin },
  { url: '/admin/projects', component: ProjectAdmin },
];

export {
  indexRoute,
  routes,
};
