import $ from 'jquery';
import io from 'socket.io-client';

import handlers from './handlers';

const socket = $('meta[name="socket-url"]').attr('content');
const jwt = $('meta[name="jwt"]').attr('content');

const listener = io.connect(socket, {
  query: `jwt=${jwt}`,
  transports: ['websocket', 'polling'],
});

let hasConnectionError = false;

// FIXME: Make these easier to test
listener.on('connect_error', () => {
  if (!hasConnectionError) {
    $('#socket_offline').show();
  }

  hasConnectionError = true;
});

listener.on('connect', () => {
  $('#socket_offline').hide();
  hasConnectionError = false;
});

listener.on('reconnect', () => {
  $('#socket_offline').hide();
  hasConnectionError = false;
});

export default handlers(listener);
