import App from '../app/containers/App';
import Dashboard from '../dashboard/components/Dashboard';
import Profile from '../profile/Profile';

import UserAdmin from '../admin/Users';
import GroupAdmin from '../admin/Groups';
import TemplateAdmin from '../admin/Templates';
import ProjectAdmin from '../admin/Projects';
import ProjectDetails from '../project/Container';

import * as actions from '../app/actions';
import decorateRoutes from './decorator';

const indexRoute = {
  component: Dashboard,
  title: 'dashboard.title',
};

const childRoutes = [
  { path: 'profile', component: Profile, title: 'users.update_profile' },
  { path: 'projects/:id', component: ProjectDetails },
  {
    path: 'admin',
    childRoutes: [
      { path: 'users', component: UserAdmin, title: 'users.manage' },
      { path: 'groups', component: GroupAdmin, title: 'groups.manage' },
      { path: 'templates', component: TemplateAdmin, title: 'templates.manage' },
      { path: 'projects', component: ProjectAdmin, title: 'projects.manage' },
    ],
  },
];

export default function (store) {
  const updateTitle = (nextState) => {
    const routes = nextState.routes;
    const string = routes[routes.length - 1].title;
    const title = string ? Lang.get(string) : '';

    store.dispatch(actions.setPageTitle(title));
  };

  decorateRoutes(childRoutes, updateTitle);

  return {
    path: '/',
    component: App,
    onEnter: updateTitle,
    indexRoute,
    childRoutes,
  };
}
