// socket.io
var socketio = io.connect();

// socket username
var username = null;
var selected = null;
var mutelist = [];

// $(function(){});
$(document).ready(function(){
	
	// buttons
	$(document).on('click', '#sendButton',function(){
		var msg = $('#textInput').val();
		socketio.emit("message_to_server", {"message":msg});
		
		$('#textInput').val("");
	});
	
	$(document).on('click', '#changeButton',function(){
		var username = $('#textInput').val();
		socketio.emit("changeusername", username);
		
		$('#textInput').val("");
	});
	
	$(document).on('click', '#createButton',function(){
		var room = $('#textInput').val();
		var pwd = $('#passwordInput').val();
		socketio.emit("createroom", {"room":room,"pwd":pwd});
		
		$('#textInput').val("");
		$('#passwordInput').val("");
	});
	
	$(document).on('click', '#clearButton',function(){
		$('#chatlogs').empty();
	});
	
	// join chatroom
	$(document).on('click', '.roomitem',function(){
		var roomname = $(this).text();
		console.log(roomname);
		socketio.emit("joinroom", roomname);
		selected = null;
		$("#currentuser").text("");
	});
	
	$(document).on('click', '.useritem',function(){
		var clickeduser = $(this).text();
		console.log(clickeduser);
		selected = clickeduser;
		$("#currentuser").text(clickeduser);
	});
	
	$(document).on('click', '#whispButton',function(){
		var whisptouser = selected;
		var mes = prompt("whisper to "+whisptouser+": ");
		if (mes !== null){
			socketio.emit("whisperto", {'user':whisptouser, 'message':mes});
		}
	});
	
	$(document).on('click', '#kickButton',function(){
		var kickedusername = selected;
		socketio.emit("kickuser", kickedusername);
	});
	
	$(document).on('click', '#banButton',function(){
		var bannedusername = selected;
		socketio.emit("banuser", bannedusername);
	});
	
	$(document).on('click', '#muteButton',function(){
		var mutedusername = selected;
		//socketio.emit("muteuser", mutedusername);
		mutelist.push(selected);
	});
	
	$(document).on('click', '#updateButton',function(){
		console.log("update");
	});
	
});

// on connection
socketio.on('connect', function(){
	// default username: guest
	socketio.emit('newconnection', 'guest');
});

// Update available public rooms
socketio.on("roomlist", function(rooms){
	//console.log(rooms);
	
	$('#roomlist').empty();
	$('#roomlist').text ="Available rooms:";
	
	var $table = $('<table></table>');
	var arrayLength = rooms.length;
	for (var i = 0; i < arrayLength; i++) {
		var $t = $('<tr></tr>').text(rooms[i]).addClass("roomitem");
		$table.append($t);
	}
	$('#roomlist').append($table);
});

// Update users in current room
socketio.on("userlist", function(users){
	
	$('#userlist').empty();
	
	var $table = $('<table></table>');
	
	var $bw = $('<button></button>').text('whisp').attr("id","whispButton");
	var $bk = $('<button></button>').text('kick').attr("id","kickButton");
	var $bb = $('<button></button>').text('ban').attr("id","banButton");
	var $bm = $('<button></button>').text('mute').attr("id","muteButton");
	var $bu = $('<button></button>').text('update').attr("id","updateButton");
	
	// i == 0: owner
	var $t = $('<tr></tr>').text("Owner: "+users[0]);
	$table.append($t);
	var arrayLength = users.length;
	for (var i = 1; i < arrayLength; i++) {
		$t = $('<tr></tr>').text(users[i]).addClass("useritem");
		//$t.append($bw,$bk,$bb);
		$table.append($t);
	}
	$('#userlist').append($table);
	$('#userlist').append($bw,$bk,$bb,$bm,$bu);
	
	$("#currentuser").text("");
});

socketio.on("pwdreq",function(roomname){
	var password = prompt("Password for room "+roomname+"?");
	socketio.emit('joinroompwd', {'room':roomname,'pwd':password});
});

socketio.on("kickedtolobby",function(data){
	socketio.emit("joinroom", 'LOBBY');
	selected = null;
	$('#currentuser').text('');
});

socketio.on("mutedfromroom",function(room){
	socketio.emit("addmutedfromroom", room);
});

socketio.on("bannedfromroom",function(room){
	socketio.emit("addbannedfromroom", room);
});

socketio.on("message_to_client",function(data) {
	if (mutelist.indexOf(data['user']) > -1){
		console.log("muted "+data['user'])
	}
	else{
		var t = $('<tr></tr>').text(data['user']+': '+data['message']);
		$('#chatlogs').append(t);
	}
});
