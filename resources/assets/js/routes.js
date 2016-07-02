import Dashboard from './Containers/Dashboard';
import Profile from './Containers/Profile';

const indexRoute = {
  url: '/',
  component: Dashboard,
};

const routes = [
  {
    url: '/profile',
    component: Profile,
  },
];

export {
  indexRoute,
  routes,
};
