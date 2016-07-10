import * as app from '../app/constants';
import * as dashboard from '../dashboard/constants';
import * as navigation from '../navigation/constants';
import * as socket from '../socket/constants';

export default function (state) {
  // FIXME: Should this be somewhere else?
  Lang.setLocale(state[app.NAME].locale);

  const preloadedState = {};

  // TODO: This may not be needed anymore, state
  // is the exact same as preloadedState now
  [
    app.NAME,
    dashboard.NAME,
    navigation.NAME,
    socket.NAME,
  ].forEach((type) => {
    preloadedState[type] = state[type];
  });

  return preloadedState;
}
