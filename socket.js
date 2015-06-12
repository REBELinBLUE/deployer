var app = require('http').createServer(handler);
var io = require('socket.io')(app);

var Redis = require('ioredis');
var redis = new Redis();

app.listen(6001, function() {
    console.log('Server is running!');
});

function handler(req, res) {
    res.writeHead(200);
    res.end('');
}

io.on('connection', function(socket) {
   // console.log('connection');
});

redis.psubscribe('*', function(err, count) {
   // console.log('psubscribe');
});

redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);

    console.log('Message received from event ' + message.event + ' to channel ' + channel);

    io.emit(channel + ':' + message.event, message.data);
});