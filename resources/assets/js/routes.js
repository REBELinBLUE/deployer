import App from './Containers/App';
import Dashboard from './Containers/Dashboard';
import Profile from './Containers/Profile';

import UserAdmin from './Containers/Admin/Users';
import GroupAdmin from './Containers/Admin/Groups';
import TemplateAdmin from './Containers/Admin/Templates';
import ProjectAdmin from './Containers/Admin/Projects';

import { setPageTitle } from './Actions/app';

export default function (store) {
  // TODO: Is this really the best way to do this?
  const updateTitle = (currentState, nextState) => {
    const title = nextState.routes[nextState.routes.length - 1].title;

    store.dispatch(setPageTitle(title || 'No title set'));
  };

  return {
    path: '/',
    component: App,
    indexRoute: { component: Dashboard, title: 'Dashboard' },
    onChange: updateTitle,
    childRoutes: [
      { onChange: updateTitle, title: 'Update Profile', path: 'profile', component: Profile },
      { onChange: updateTitle, title: 'Manage users', path: 'admin/users', component: UserAdmin },
      { onChange: updateTitle, title: 'Manage groups', path: 'admin/groups', component: GroupAdmin },
      { onChange: updateTitle, title: 'Manage deployment templates', path: 'admin/templates', component: TemplateAdmin },
      { onChange: updateTitle, title: 'Manage projects', path: 'admin/projects', component: ProjectAdmin },
    ],
  };
};
