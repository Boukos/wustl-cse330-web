var socketio = io.connect();
var username = null;

// event listeners
$(document).ready(function(){
	// send message 
	$(document).on('click', '#sendmessage',function(){
		// sendMessage();
		var msg = $('#text_input').val();
		socketio.emit("message_to_server", {"message":msg});
		$('#text_input').val("");
	});
	
	// change username
	$(document).on('click', '#changeusername',function(){
		var username = $('#text_input').val();
		socketio.emit("changeusername", username);
		$('#text_input').val("");
	});
	
	// create new chatroom
	$(document).on('click', '#createroom',function(){
		var roomname = $('#text_input').val();
		var password = $('#password_input').val();
		socketio.emit("createroom", {"room":roomname,"pwd":password});
		$('#text_input').val("");
		$('#password_input').val("");
	});
	
	// join chatroom
	$(document).on('click', '.chatroomitem',function(){
		var roomname = $(this).text();
		socketio.emit("joinroom", roomname);
	});
	
	$(document).on('click', '.roomuseritem',function(){
		var username = $(this).text();
		var mes = $('#text_input').val();
		socketio.emit("whisperto", {user:username, message:mes});
	});
	
	$(document).on('click', '.mutebutton',function(){
		var roomname = $(this).roomname();
		var username = $(this).username();
		socketio.emit("muteuser", {room:roomname, user:username});
	});
	
	$(document).on('click', '.kickbutton',function(){
		var roomname = $(this).roomname();
		var username = $(this).username();
		socketio.emit("kickuser", {room:roomname, user:username});
	});
	
	$(document).on('click', '.banbutton',function(){
		var roomname = $(this).roomname();
		var username = $(this).username();
		socketio.emit("banuser", {room:roomname, user:username});
	});
});

// on connection
socketio.on('connect', function(){
	username = 'guest';
	socketio.emit('newuser', username);
});

socketio.on("userjoining", function(data){
	var t = $('<hr></hr>');
	t.text = 'user joining';
	$('#chatlogs').append(t);
});

// Update available public rooms
socketio.on("chatrooms", function(rooms){
	console.log(rooms);
	$('#chatroomslist').empty();
	$('#chatroomslist').text ="Available rooms:";
	var $table = $('<table></table>');
	var arrayLength = rooms.length;
	for (var i = 0; i < arrayLength; i++) {
		//alert(rooms[i]);
		var $t = $('<tr></tr>').text(rooms[i]); //t.text = key;
		$t.addClass("chatroomitem");
		$table.append($t);
	}
	$('#chatroomslist').append($table);
});

// Update users in current room
socketio.on("roomusers", function(users){
	console.log(users);
	$('#roomuserslist').empty();
	$('#roomuserslist').text ="Users in room:";
	var $table = $('<table></table>');
	var arrayLength = users.length;
	
	var $bm = $('<button></button> ').text('mute');
	$bm.addClass("mutebutton");
	var $bk = $('<button></button> ').text('kick');
	$bk.addClass("kickbutton");
	var $bb = $('<button></button> ').text('ban');
	$bk.addClass("banbutton");
	
	for (var i = 0; i < arrayLength; i++) {
		var $t = $('<tr></tr>').text(users[i]); //t.text = key;
		$t.addClass("roomuseritem");
		$t.append($bm,$bk,$bb);
		$table.append($t);
	}
	$('#roomuserslist').append($table);
});

socketio.on("message_to_client",function(data) {
	//Append an HR thematic break and the escaped HTML of the new message
	var t = $('<tr></tr>').text(data['user']+': '+data['message']);
	$('#chatlogs').append(t);
	//document.getElementById("chatlogs").appendChild(document.createElement("hr"));
	//document.getElementById("chatlogs").appendChild(document.createTextNode(data['message']));
});


// eof