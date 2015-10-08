var app = require('http').createServer(handler);
var io = require('socket.io')(app);

require('dotenv').load();

var debug = (process.env.APP_DEBUG === 'true' || process.env.APP_DEBUG === true);

var Redis = require('ioredis');
var redis = new Redis();

app.listen(6001, function() {
    if (debug) {
        console.log('Server is running!');
    }
});

function handler(req, res) {
    res.writeHead(200);
    res.end('');
}

io.on('connection', function(socket) {
    if (debug) {
        console.log('connection');
    }
});

redis.psubscribe('*', function(err, count) {
    if (debug) {
        console.log('psubscribe');
    }
});

redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);

    if (debug) {
        console.log('Message received from event ' + message.event + ' to channel ' + channel);
    }

    io.emit(channel + ':' + message.event, message.data);
});