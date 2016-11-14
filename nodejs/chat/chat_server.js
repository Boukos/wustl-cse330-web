// Require the packages we will use:
var http = require("http"),
	socketio = require("socket.io"),
	fs = require("fs");
	express = require('express');

// Server setup using express
var appexp = express();
var app = http.createServer(appexp);
appexp.use(express.static('public'))
appexp.get('/',function(req,res){
	res.sendFile(__dirname + '/client.html'); // index.html
});
app.listen(3456);


// Chatroom variables
var usernames = [];
var chatrooms = [];
var useridnames = {'serverid':'server'};
var useridrooms = {'serverid':'lobby'};
var usersinroom = {'lobby':{'serverid':true}};

// Do the Socket.IO magic:
var io = socketio.listen(app);

// This callback runs when a new Socket.IO connection is established.
io.sockets.on("connection", function(socket){
	
	socket.on('message_to_server', function(data) {
	// This callback runs when the server receives a new message from the client.
		console.log("message: "+ data["message"]); 
		// io.sockets.emit
		io.to(socket.loc).emit("message_to_client",{
			message:data["message"],
			user:useridnames[socket.id] 
		}) 
	});
	
	socket.on('newuser', function(username){
		useridnames[socket.id] = username;
		socket.name = username;
		socket.loc = 'lobby'; //socket.room
		usersinroom['lobby'][socket.id]=true;
		socket.join('lobby');
		
		console.log("new user: "+username);
		// update chatrooms to this socket
		chatrooms = getValues(useridrooms);
		socket.emit("chatrooms",chatrooms);
		// update users to all sockets
		//usernames = getValues(useridnames); // all users
		userids = getKeys(usersinroom['lobby'])
		usernames = getUsernames(userids);
		io.to('lobby').emit("roomusers",usernames);
		// emit notification
		io.to('lobby').emit("message_to_client",{
			message: "Welcome " + username , 
			user:"SERVER"
		});
	});
	
	socket.on('disconnect', function(){
		console.log(useridnames[socket.id] + " " + "left");
		delete useridnames[socket.id];
		delete (usersinroom[socket.loc])[socket.id];
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
	});
	
	socket.on('changeusername', function(newusername) {
		console.log('change name: '+ newusername);
		var oldusername = socket.name;
		socket.name = newusername;
		useridnames[socket.id] = newusername;
		delete usersinroom[socket.loc][oldusername];
		usersinroom[socket.loc][socket.id] = true;
		
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
	});
	
	socket.on('createroom', function(roomdata) {
		// leaving
		newroomname = roomdata["room"];
		console.log('create room: '+ newroomname);
		socket.leave(socket.loc);
		delete (usersinroom[socket.loc])[socket.id];
		io.to(socket.loc).emit("message_to_client",{
			message: socket.name + " left " + socket.loc , 
			user:"SERVER"
		});
		usernames = getUsernames(getKeys(usersinroom[socket.loc]));
		io.to(socket.loc).emit("roomusers",usernames);
		
		// creating and joining
		socket.join(newroomname);
		useridrooms[socket.id] = newroomname; // register user's room
		chatrooms = getValues(useridrooms);
		io.sockets.emit("chatrooms",chatrooms); // update rooms
		socket.loc = newroomname;
		usersinroom[socket.loc] = {};
		usersinroom[socket.loc][socket.id] = true;
		
		io.to(socket.loc).emit("message_to_client",{
			message: socket.name + " joined " + socket.loc , 
			user:"SERVER"
		});	
		chatrooms = getValues(useridrooms);
		socket.emit("chatrooms",chatrooms);
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
	});
	
	socket.on('joinroom', function(roomname) {
		// leaving
		console.log(socket.name + ' joined room: ' + roomname);
		socket.leave(socket.loc);
		delete (usersinroom[socket.loc])[socket.id];
		io.to(socket.loc).emit("message_to_client",{
			message: socket.name + " left " + socket.loc , 
			user:"SERVER"
		});
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
		
		// no creating just joining
		socket.join(roomname);
		// useridrooms[socket.id] = newroomname; // register user's room
		socket.loc = roomname;
		// usersinroom[socket.loc] = {};
		usersinroom[socket.loc][socket.id] = true;
		
		io.to(socket.loc).emit("message_to_client",{
			message: socket.name + " joined " + socket.loc , 
			user:"SERVER"
		});	
		chatrooms = getValues(useridrooms);
		socket.emit("chatrooms",chatrooms);
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
	});
	
});

function getUsernames(ids) {
	var names = [];
	ids.forEach(function(key){
		names.push(useridnames[key]);
	});
	return names;
}

function getKeys(dict) {
	var keys = Object.keys(dict);
	return keys;
}

function getValues(dict) {
	var vals = Object.keys(dict).map(function(key){
		return dict[key];
	});
	return vals;
}

// eof