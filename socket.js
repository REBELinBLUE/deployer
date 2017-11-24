const jwt = require('jsonwebtoken');
const fs = require('fs');
const path = require('path');
const Redis = require('ioredis');
const http = require('http');
const https = require('https');
const socketIo = require('socket.io');

require('dotenv').load();

const debug = (process.env.APP_DEBUG === 'true' || process.env.APP_DEBUG === true);

const redis = new Redis({
  port: process.env.REDIS_PORT || 6379,
  host: process.env.REDIS_HOST || '127.0.0.1',
  db: process.env.REDIS_DATABASE || 0,
  password: process.env.REDIS_PASSWORD || null,
});

function handler(req, res) {
  res.writeHead(200);
  res.end('');
}

let app;
if (/^https/i.test(process.env.SOCKET_URL)) {
  const sslConfig = {
    key: (process.env.SOCKET_SSL_KEY_FILE ? fs.readFileSync(process.env.SOCKET_SSL_KEY_FILE) : null),
    cert: (process.env.SOCKET_SSL_CERT_FILE ? fs.readFileSync(process.env.SOCKET_SSL_CERT_FILE) : null),
    ca: (process.env.SOCKET_SSL_CA_FILE ? fs.readFileSync(process.env.SOCKET_SSL_CA_FILE) : null),
    passphrase: (process.env.SOCKET_SSL_KEY_PASSPHRASE ? process.env.SOCKET_SSL_KEY_PASSPHRASE : null),
  };

  app = https.createServer(sslConfig, handler);
} else {
  app = http.createServer(handler);
}

const io = socketIo(app);

app.listen(parseInt(process.env.SOCKET_PORT, 10), () => {
  if (debug) {
    console.log('Server is running!');
  }
});

// Middleware to check the JWT
io.use((socket, next) => {
  let decoded;

  if (debug) {
    console.log(`Token - ${socket.handshake.query.jwt}`);
  }

  try {
    decoded = jwt.verify(socket.handshake.query.jwt, process.env.JWT_SECRET);

    if (debug) {
      console.log(decoded);
    }
  } catch (err) {
    if (debug) {
      console.error(err);
    }

    next(new Error('Invalid token!'));
  }

  if (decoded) {
    // everything went fine - save userId as property of given connection instance
    socket.userId = decoded.data.userId;
    next();
  } else {
    // invalid token - terminate the connection
    next(new Error('Invalid token!'));
  }
});

io.on('connection', () => {
  if (debug) {
    console.log('connection');
  }
});

redis.psubscribe('*', () => {
  if (debug) {
    console.log('psubscribe');
  }
});

redis.on('pmessage', (subscribed, channel, message) => {
  const payload = JSON.parse(message);

  if (typeof payload.event !== 'undefined') {
    if (payload.event.indexOf('RestartSocketServer') !== -1) {
      if (debug) {
        console.log('Restart command received');
      }

      process.exit();
      return;
    }

    if (debug) {
      console.log(`Message received from event ${payload.event} to channel ${channel}`);
    }

    io.emit(`${channel}:${payload.event}`, payload.data);
  }
});
