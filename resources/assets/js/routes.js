import React from 'react';

import Dashboard from './Containers/Dashboard';

const indexRoute = {
  url: '/',
  component: Dashboard,
};

const routes = [

];

const allRoutes = [indexRoute, ...routes];

export {
  indexRoute,
  routes,
  allRoutes,
};
