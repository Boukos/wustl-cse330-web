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
var usernewnameid = {};
var useridrooms = {'serverid':'lobby'};
var usersinroom = {'lobby':{'serverid':true}};
var roompwd = {'lobby':''};

// Do the Socket.IO magic:
var io = socketio.listen(app);

// This callback runs when a new Socket.IO connection is established.
io.sockets.on("connection", function(socket){
	
	socket.on('message_to_server', function(data) {
	// This callback runs when the server receives a new message from the client.
		console.log("message: "+ data["message"]);
		mes = data["message"];
		
		// check @user
		var results = mes.match(/@[\w]+/);
		if (results!=null){ 
			var atuser=results[0].match(/[\w]+/)[0];
			userid = usernewnameid[atuser];
			io.to(userid).emit("message_to_client",{
				message:mes,
				user:"@YOU: "+useridnames[socket.id]}) 
		}
		
		// censor
		var filterWords = ["shit", "fuck", "ass"];
		var rgx = new RegExp("("+filterWords.join("|")+")", "gi");
		mes = mes.replace(rgx, "***");
		
		// io.sockets.emit
		io.to(socket.loc).emit("message_to_client",{
			message:mes,
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
		socket.banned = {};
	});
	
	socket.on('disconnect', function(){
		console.log(useridnames[socket.id] + " " + "left");
		delete useridnames[socket.id];
		delete usernewnameid[socket.name];
		delete (usersinroom[socket.loc])[socket.id];
		userids = getKeys(usersinroom[socket.loc]);
		usernames = getUsernames(userids);
		io.to(socket.loc).emit("roomusers",usernames);
	});
	
	socket.on('changeusername', function(newusername) {
		console.log('change name: '+ newusername);
		var oldusername = socket.name;
		
		if(newusername in usernewnameid ){
			if(usernewnameid[newusername] === socket.id){
				delete usernewnameid[oldusername];
				usernewnameid[newusername] = socket.id;
			
				socket.name = newusername;
				useridnames[socket.id] = newusername;
				delete usersinroom[socket.loc][oldusername];
				usersinroom[socket.loc][socket.id] = true;
				
				userids = getKeys(usersinroom[socket.loc]);
				usernames = getUsernames(userids);
				io.to(socket.loc).emit("roomusers",usernames);
			}
			else{
				io.to(socket.loc).emit("message_to_client",{
					message:'name not available',
					user:useridnames[socket.id] 
				}) 
			}
		}
		else{
			usernewnameid[newusername] = socket.id;
			
			socket.name = newusername;
			useridnames[socket.id] = newusername;
			delete usersinroom[socket.loc][oldusername];
			usersinroom[socket.loc][socket.id] = true;
			
			userids = getKeys(usersinroom[socket.loc]);
			usernames = getUsernames(userids);
			io.to(socket.loc).emit("roomusers",usernames);
		}
	});
	
	socket.on('createroom', function(roomdata) {
		// leaving
		newroomname = roomdata["room"];
		newroompwd = roomdata["pwd"];
		console.log('create room: '+ newroomname+' '+newroompwd);
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
		roompwd[newroomname] = newroompwd;
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
		if(socket.banned[roomname]){
			socket.emit('message_to_client', {
				message:"Banned from "+roomname,
				user:("SERVER")
			});
			return;
		}
		
		if (roompwd[roomname]==""){
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
		}
		else{
			socket.emit("passwordreq",roomname);
		}
	});	

	socket.on('joinroompwd', function(data) {
		roomname = data['room'];
		password = data['pwd'];
		if (roompwd[roomname]==password){
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
		}
		else{
			io.to(socket.loc).emit("message_to_client",{
				message:"Wrong password",
				user:"SERVER" 
			});
		}
	});
	
	socket.on('whisperto', function(data) {	
		username = data["user"];
		mes = data["message"];
		userid = usernewnameid[username];
		io.to(userid).emit('message_to_client', {
			message:mes,
			user:("WHISPER: "+useridnames[socket.id])
		});
	});
	
	socket.on('kickuser', function(data) {	
		username = data;
		roomname = socket.loc;
		
		if(useridrooms[socket.id]===roomname){
			userid = usernewnameid[username];
			io.to(userid).emit('message_to_client', {
				message:'kicked from '+roomname,
				user:('SERVER')
			});
			io.to(userid).emit('kickedtolobby', 'kicked');
		}
		else{
			socket.emit('message_to_client', {
				message:"No permission",
				user:("SERVER")
			});
		}
		
	});
	
	socket.on('banuser', function(data) {	
		username = data;
		roomname = socket.loc;
		if(useridrooms[socket.id]===roomname){
			userid = usernewnameid[username];
			io.to(userid).emit('message_to_client', {
				message:'banned from '+roomname,
				user:('SERVER')
			});
			io.to(userid).emit('kickedtolobby', 'kicked');
			io.to(userid).emit('bannedfromroom', roomname);
		}
		else{
			socket.emit('message_to_client', {
				message:"No permission",
				user:("SERVER")
			});
		}
	});
	
	socket.on('addbannedfromroom', function(data) {
		socket.banned[data] = true;
		console.log(socket.name+' banned from '+data);
	});
})


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