const decorateRoutes = (routes, onEnterCallback) => {
  routes.map((route) => {
    if (typeof route.childRoutes !== 'undefined') {
      return decorateRoutes(route.childRoutes, onEnterCallback);
    }

    const localRoute = route;

    localRoute.onEnter = onEnterCallback;

    return localRoute;
  });
};

export default decorateRoutes;
