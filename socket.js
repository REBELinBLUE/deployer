/* eslint-disable no-console, global-require */
const jwt = require('jsonwebtoken');
const fs = require('fs');

const Redis = require('ioredis');
const SocketServer = require('socket.io');

require('dotenv').config();

const DEBUG = (process.env.APP_DEBUG === 'true' || process.env.APP_DEBUG === true);
const SOCKET_PORT = parseInt(process.env.SOCKET_PORT, 10);
const SOCKET_URL = process.env.SOCKET_URL;

const requestHandler = (request, response) => {
  response.writeHead(200);
  response.end('');
};

const debugMessage = (message) => {
  if (DEBUG) {
    console.log(message);
  }
};

let httpServer;
if (/^https/i.test(SOCKET_URL)) {
  const sslConfiguration = {
    key: (process.env.SOCKET_SSL_KEY_FILE ? fs.readFileSync(process.env.SOCKET_SSL_KEY_FILE) : null),
    cert: (process.env.SOCKET_SSL_CERT_FILE ? fs.readFileSync(process.env.SOCKET_SSL_CERT_FILE) : null),
    ca: (process.env.SOCKET_SSL_CA_FILE ? fs.readFileSync(process.env.SOCKET_SSL_CA_FILE) : null),
  };

  httpServer = require('https').createServer(sslConfiguration, requestHandler);
} else {
  httpServer = require('http').createServer(requestHandler);
}

const socketServer = new SocketServer(httpServer, { });

const queue = new Redis({
  port: process.env.REDIS_PORT || 6379,
  host: process.env.REDIS_HOST || '127.0.0.1',
  db: process.env.REDIS_DATABASE || 0,
  password: process.env.REDIS_PASSWORD || null,
});

httpServer.listen(SOCKET_PORT, () => debugMessage('Server is running!'));
socketServer.on('connection', () => debugMessage('connection'));
queue.psubscribe('*', () => debugMessage('psubscribe'));

// Middleware to check the JWT
socketServer.use((socket, next) => {
  if (!socket.handshake.query.jwt) {
    next(new Error('No token!'));
  }

  try {
    debugMessage(`Token - ${socket.handshake.query.jwt}`);

    const decoded = jwt.verify(socket.handshake.query.jwt, process.env.JWT_SECRET);

    if (!decoded) {
      throw new Error('Not decoded');
    }

    // everything went fine - save userId as property of given connection instance
    // socket.userId = decoded.data.userId;
    debugMessage(decoded);
    next();
  } catch (err) {
    debugMessage(err);
    next(new Error('Invalid token!'));
  }
});

// When a pmessage event occurs on the redis queue, emit it to the socket.io server
queue.on('pmessage', (subscribed, channel, message) => {
  const parsed = JSON.parse(message);

  if (parsed.event.indexOf('RestartSocketServer') !== -1) {
    debugMessage('Restart command received');

    process.exit();
    return;
  }

  debugMessage(`Message received from event ${parsed.event} to channel ${channel}`);
  socketServer.emit(`${channel}: ${parsed.event}`, parsed.data);
});
