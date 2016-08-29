const decorateRoutes = (routes, onEnterCallback) => {
  routes.map((route) => {
    if (typeof route.childRoutes !== 'undefined') {
      return decorateRoutes(route.childRoutes, onEnterCallback);
    }

    const localRoute = route;

    if (typeof localRoute.title !== 'undefined') {
      localRoute.onEnter = onEnterCallback;
    }

    return localRoute;
  });
};

export default decorateRoutes;
