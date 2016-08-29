import * as app from '../app/constants';
import * as dashboard from '../dashboard/constants';
import * as navigation from '../navigation/constants';
import * as socket from '../socket/constants';

export default function (state) {
  const preloadedState = {};

  [
    app.NAME,
    dashboard.NAME,
    navigation.NAME,
    socket.NAME,
  ].forEach((type) => {
    preloadedState[type] = state[type];
  });

  // FIXME: There has to be a nicer way to do this, surely?
  preloadedState[navigation.NAME].buttons = [];
  preloadedState[app.NAME].title = '';
  preloadedState[app.NAME].subtitle = null;

  return preloadedState;
}
