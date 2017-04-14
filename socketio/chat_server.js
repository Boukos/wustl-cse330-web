// Require the packages we will use:
var http = require("http"),
	url = require('url'),
	path = require('path'),
	socketio = require("socket.io"),
	fs = require("fs"),
	express = require('express');

// Server setup using express for css and js
// https://expressjs.com/en/starter/static-files.html
var appexp = express();
var app = http.createServer(appexp);
appexp.use(express.static('client'));
appexp.get('/',function(req,res){
	res.sendFile(__dirname + '/client.html'); // index.html
});
app.listen(3456);


// Do the Socket.IO magic:
var io = socketio.listen(app);

var users = {"SERVER":1} // socket.id: username
var rooms = {"LOBBY":["SERVER"]} // roomname: list of users
var roompwds = {"LOBBY":""} // roomname: room password

// This callback runs when a new Socket.IO connection is established.
io.sockets.on("connection", function(socket){
	// socket.name: username
	// socket.loc: user location
	
	socket.on('newconnection', function(username){
		// update globals
		if (username === 'guest'){
			randomizer = Math.floor((Math.random() * 1000) + 1)
			username = username+randomizer.toString();
		}
		users[username] = socket.id;
		rooms["LOBBY"].push(username);
		// update socket info
		socket.name = username;
		socket.loc = "LOBBY";
		socket.bannedFrom = [];
		// join lobby channel
		socket.join("LOBBY");
		
		console.log("new user: "+username);
		
		// update chatrooms to this socket
		roomlist = Object.keys(rooms);
		console.log(roomlist);
		socket.emit("roomlist",roomlist);
		// update users to all sockets
		userlist = rooms["LOBBY"];
		console.log(userlist);
		io.to('LOBBY').emit("userlist",userlist);
		
		// emit notification
		io.to('LOBBY').emit("message_to_client",{
			message: "Welcome "+username , 
			user: "SERVER"
		});
	});
	
	socket.on('disconnect', function(){
		console.log(socket.name+" "+"left");
		// update users, rooms
		delete users[socket.name];
		// http://stackoverflow.com/questions/5767325/how-to-remove-a-particular-element-from-an-array-in-javascript
		var index = rooms[socket.loc].indexOf(socket.name);
		if (index > -1) {
			rooms[socket.loc].splice(index, 1);
		}
		// broadcast new userlist
		userlist = rooms[socket.loc];
		io.to(socket.loc).emit("userlist",userlist);
	});
	
	socket.on('message_to_server', function(data) {
	// This callback runs when the server receives a new message from the client.
		mes = data["message"];
		console.log("message: "+ mes);
		
		// Creative
		// filter words
		mes = filterMessage(mes)
		
		// io.sockets.emit
		io.to(socket.loc).emit("message_to_client",{
			message:mes,
			user:socket.name
		})
	});
	
	socket.on('changeusername', function(newusername) {
		console.log('change name: '+newusername);
		var oldusername = socket.name;
		
		if(newusername in users ){
			socket.emit("message_to_client",{
				message:'Username not available',
				user:"SERVER" 
			})
		}
		else{
			socket.name = newusername;
			users[socket.name] = socket.id;			
			delete users[oldusername];
			
			rooms[socket.loc].push(socket.name);
			var index = rooms[socket.loc].indexOf(oldusername);
			if (index > -1) {
				rooms[socket.loc].splice(index, 1);
			}
			
			usernames=rooms[socket.loc]
			io.to(socket.loc).emit("userlist",usernames);
		}
	});
	
	socket.on('createroom', function(roomdata) {
		
		newroomname = roomdata["room"];
		newroompwd = roomdata["pwd"];
		console.log('create room: '+ newroomname+' '+newroompwd);
		
		if (newroomname in rooms){
			socket.emit("message_to_client",{
				message:'Roomname not available',
				user:"SERVER" 
			})
		}
		else{
			// leave current room
			var index = rooms[socket.loc].indexOf(socket.name,1);
			if (index > -1) {
				rooms[socket.loc].splice(index, 1);
			}
			socket.leave(socket.loc);
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name + " lefts " + socket.loc , 
				user:"SERVER"
			});
			
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
			
			// creating and joining
			rooms[newroomname] = [];
			rooms[newroomname].push(socket.name); // 0 for owner
			rooms[newroomname].push(socket.name); // join room
			roompwds[newroomname] = newroompwd;
			
			socket.join(newroomname);
			socket.loc = newroomname;
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name + " joined " + socket.loc , 
				user:"SERVER"
			});	
			
			chatrooms = Object.keys(rooms);
			io.sockets.emit("roomlist",chatrooms);
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
		}
	});
	
	socket.on('joinroom', function(roomname) {
		
		if(socket.bannedFrom.indexOf(roomname) != -1){
			socket.emit('message_to_client', {
				message:"Banned from "+roomname,
				user:("SERVER")
			});
			return;
		}
		
		if (roompwds[roomname]=="" || rooms[roomname][0]===socket.name){
			// leave current room
			var index = rooms[socket.loc].indexOf(socket.name,1);
			if (index > -1) {
				rooms[socket.loc].splice(index, 1);
			}
			socket.leave(socket.loc);
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name + " lefts " + socket.loc , 
				user:"SERVER"
			});
			
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
			
			// no creating just joining
			socket.join(roomname);
			socket.loc = roomname;
			rooms[roomname].push(socket.name); // register in room
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name+" joined "+socket.loc , 
				user:"SERVER"
			});	
			
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
		}
		else{
			socket.emit("pwdreq",roomname);
		}
	});
	
	socket.on('joinroompwd', function(data) {
		roomname = data['room'];
		password = data['pwd'];
		if (roompwds[roomname]==password){
			// leave current room
			var index = rooms[socket.loc].indexOf(socket.name,1);
			if (index > -1) {
				rooms[socket.loc].splice(index, 1);
			}
			socket.leave(socket.loc);
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name + " lefts " + socket.loc , 
				user:"SERVER"
			});
			
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
			
			// no creating just joining
			socket.join(roomname);
			socket.loc = roomname;
			rooms[roomname].push(socket.name); // register in room
			
			io.to(socket.loc).emit("message_to_client",{
				message: socket.name+" joined "+socket.loc , 
				user:"SERVER"
			});	
			
			usernames = rooms[socket.loc];
			io.to(socket.loc).emit("userlist",usernames);
		}
		else{
			socket.emit("message_to_client",{
				message:"Wrong password",
				user:"SERVER" 
			});
		}
	});
	
	socket.on('whisperto', function(data) {	
		user = data["user"];
		mes = data["message"];
		userid = users[user];
		io.to(userid).emit('message_to_client', {
			message:mes,
			user:("WHISPER FROM "+socket.name)
		});
	});
	
	socket.on('kickuser', function(data) {	
		user = data;
		room = socket.loc;
		
		if(rooms[socket.loc][0] === socket.name){
			userid = users[user];
			io.to(userid).emit('message_to_client', {
				message:'Kicked from '+room,
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
		user = data;
		room = socket.loc;
		
		if(rooms[socket.loc][0] === socket.name){
			userid = users[user];
			io.to(userid).emit('message_to_client', {
				message:'banned from '+room,
				user:('SERVER')
			});
			io.to(userid).emit('kickedtolobby', 'kicked');
			io.to(userid).emit('bannedfromroom', room);
		}
		else{
			socket.emit('message_to_client', {
				message:"No permission",
				user:("SERVER")
			});
		}
	});
	
	socket.on('addbannedfromroom', function(data) {
		room = data;
		socket.bannedFrom.push(room);
		console.log(socket.name+' banned from '+room);
	});
	
});

function filterMessage(msg){
	// http://stackoverflow.com/questions/1144783/how-to-replace-all-occurrences-of-a-string-in-javascript
	var filterWords = ["shit", "fuck", "ass"];
	var rgx = new RegExp("("+filterWords.join("|")+")", "gi");
	msg = msg.replace(rgx, "***");
	return msg;
}
