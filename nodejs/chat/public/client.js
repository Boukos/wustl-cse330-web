var socketio = io.connect();

var username = null;
var selectedsuser = null;

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
		
		selectedsuser = null;
		$('#selecteduser').text('user');
	});
	
	// join chatroom
	$(document).on('click', '.chatroomitem',function(){
		var roomname = $(this).text();
		
		socketio.emit("joinroom", roomname);
		
		selectedsuser = null;
		$('#selecteduser').text('user');
	});
	
	$(document).on('click', '.roomuseritem',function(){
		var clickeduser = $(this).text();
		selecteduser = clickeduser; //console.log(clickeduser);
		$('#selecteduser').text(clickeduser);
		
	});
	
	$(document).on('click', '.mutebutton',function(){
		var roomname = $(this).roomname();
		var username = $(this).username();
		socketio.emit("muteuser", {'room':roomname, 'user':username});
	});
	
	$(document).on('click', '#whispbutton',function(){
		//var roomname = $(this).roomname();
		//var username = $(this).username();
		//username = selectedsuser;
		var whisptouser = $('#selecteduser').text();
		
		//var mes = $('#text_input').val();
		var mes = prompt("whisper to "+whisptouser+": ");
		if (mes !== null){
			socketio.emit("whisperto", {'user':whisptouser, 'message':mes});
		}
	});
	
	$(document).on('click', '#kickbutton',function(){
		var kickedusername = $('#selecteduser').text();
		socketio.emit("kickuser", kickedusername);
	});
	
	$(document).on('click', '#banbutton',function(){
		var bannedusername = $('#selecteduser').text();
		socketio.emit("banuser", bannedusername);
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
	
	var $bm = $('<button></button> ').text('whisp');
	$bm.attr("id","whispbutton");
	var $bk = $('<button></button> ').text('kick');
	$bk.attr("id","kickbutton");
	var $bb = $('<button></button> ').text('ban');
	$bb.attr("id","banbutton");
	
	for (var i = 0; i < arrayLength; i++) {
		var $t = $('<tr></tr>').text(users[i]); //t.text = key;
		$t.addClass("roomuseritem");
		//$t.append($bm,$bk,$bb);
		$table.append($t);
	}
	$('#roomuserslist').append($table);
	$('#roomuserslist').append($bm,$bk,$bb);
});

socketio.on("passwordreq",function(roomname){
	var password = prompt("Password for room "+roomname+"?");
	socketio.emit('joinroompwd', {'room':roomname,'pwd':password});
});

socketio.on("kickedtolobby",function(data){
	socketio.emit("joinroom", 'lobby');
		
	selectedsuser = null;
	$('#selecteduser').text('user');
});

socketio.on("bannedfromroom",function(data){
	socketio.emit("addbannedfromroom", data);
});

socketio.on("message_to_client",function(data) {
	//Append an HR thematic break and the escaped HTML of the new message
	var t = $('<tr></tr>').text(data['user']+': '+data['message']);
	$('#chatlogs').append(t);
	//document.getElementById("chatlogs").appendChild(document.createElement("hr"));
	//document.getElementById("chatlogs").appendChild(document.createTextNode(data['message']));
});


// eof