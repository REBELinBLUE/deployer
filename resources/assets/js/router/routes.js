import App from '../app/components/App';
import Dashboard from '../dashboard/containers/Dashboard';
import Profile from '../profile/Profile';

import UserAdmin from '../admin/Users';
import GroupAdmin from '../admin/Groups';
import TemplateAdmin from '../admin/Templates';
import ProjectAdmin from '../admin/Projects';
import ProjectDetails from '../project/ProjectDetails';

import * as actions from '../app/actions';

// FIXME: Clean this up
export default function (store) {
  const indexRoute = { component: Dashboard, title: Lang.get('dashboard.title') };

  const childRoutes = [
    { title: 'Update profile', path: 'profile', component: Profile },
    { title: 'Manage users', path: 'admin/users', component: UserAdmin },
    { title: 'Manage groups', path: 'admin/groups', component: GroupAdmin },
    { title: 'Manage deployment templates', path: 'admin/templates', component: TemplateAdmin },
    { title: 'Manage projects', path: 'admin/projects', component: ProjectAdmin },
    { path: 'projects/:id', component: ProjectDetails },
  ];

  // TODO: Is this really the best way to do this?
  const updateTitle = (currentState, nextState) => {
    const title = nextState.routes[nextState.routes.length - 1].title;

    store.dispatch(actions.setPageTitle(title || null));
  };

  childRoutes.map((route) => {
    const localRoute = route;

    localRoute.onChange = updateTitle;

    return localRoute;
  });

  return {
    path: '/',
    component: App,
    indexRoute,
    onChange: updateTitle,
    childRoutes,
  };
}
