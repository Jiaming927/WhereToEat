var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

app.get('/', function(req, res) {
	res.sendFile('index.html', {root: __dirname});
});

io.on('connection', function(socket) {
	socket.on('coordinate', function(msg) {
		var yelp = require("yelp").createClient({
			
		});

		// See http://www.yelp.com/developers/documentation/v2/search_api
		yelp.search({term: "food", limit: "20", ll:msg, radius_filter:1000}, function(error, data) {
			console.log(error);
			console.log(data['businesses'][3]);
		});
		io.emit('place', msg);
	})
});

http.listen(3000, function() {
	console.log('listening on 3000');
});

// Function to draw stuffs
function draw() {

}