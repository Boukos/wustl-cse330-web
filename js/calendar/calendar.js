/*globals $:false */
var wd = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

var thedate = new Date(); // date of the page
var thetoken;

var events = {}; //var tags = {};
var publicevents = {};

// $(document).ready()
$(function(){
	logoutAjax();
	
	$("#logoutdiv").hide();
	$("#logindiv").show();
	updateDate(thedate);
	renderCalendar(thedate);
	
	$("#lastmonth").on("click", lastMonthButton);
	$("#nextmonth").on("click", nextMonthButton);
	$(document).on("click","td",tdClicked); // $("td") ?
	
	$("#loginbutton").on("click", loginAjax);
	$("#logoutbutton").on("click", logoutAjax);
	$("#registerbutton").on("click", registerAjax);
	
	getTags();
	$("#addEventButton").on("click", addEvent);
	$(document).on("click",".deletebuttons",deleteEvent);
	$(document).on("click",".editbuttons",editEvent);
	
	$(".closemodal").on("click", closemodal);
	$("#editEventButton").on("click", submitEditEvent);
	
	$(document).on("change","[name='tag']",handleTags);
	
	$("#profileButton").on("click", displayProfile);
	$("#testbutton").on("click", test);
	
});

function test(){
	console.log(thedate);
	console.log(thetoken);
	viewcurrentuser();
	sqlToJsDate('1992-11-16 00:00:00');
}

function viewcurrentuser(){
	$.ajax({
		type: 'GET',
		url: 'info.php',
		success: function(msg) {
			console.log(msg);
		}
	});
}

function getDateString(d){
	// http://stackoverflow.com/questions/1531093/how-do-i-get-the-current-date-in-javascript
	
	var today = d; //new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd='0'+dd;
	} 

	if(mm<10) {
		mm='0'+mm;
	} 

	var todaystring = mm+'/'+dd+'/'+yyyy;
	return todaystring;
}

function jsToSqlDate(jsDate){
	//http://stackoverflow.com/questions/10830357/javascript-toisostring-ignores-timezone-offset
	var localISOTime = (new Date(jsDate.getTime() - jsDate.getTimezoneOffset() * 60000));
	//http://stackoverflow.com/questions/20083807/javascript-date-to-sql-date-object
	//return jsDate.toISOString().slice(0, 19).replace('T', ' ');
	return localISOTime.toISOString().slice(0, 19).replace('T', ' ');
}

function sqlToJsDate(sqlDate){
	//http://deepinthecode.com/2014/08/05/converting-a-sql-datetime-to-a-javascript-date/
    //sqlDate in SQL DATETIME format ("yyyy-mm-dd hh:mm:ss.ms")
    var sqlDateArr1 = sqlDate.split("-");
    //format of sqlDateArr1[] = ['yyyy','mm','dd hh:mm:ms']
    var sYear = sqlDateArr1[0];
    var sMonth = (Number(sqlDateArr1[1]) - 1).toString();
    var sqlDateArr2 = sqlDateArr1[2].split(" ");
    //format of sqlDateArr2[] = ['dd', 'hh:mm:ss.ms']
    var sDay = sqlDateArr2[0];
    var sqlDateArr3 = sqlDateArr2[1].split(":");
    //format of sqlDateArr3[] = ['hh','mm','ss.ms']
    var sHour = sqlDateArr3[0];
    var sMinute = sqlDateArr3[1];
    var sqlDateArr4 = sqlDateArr3[2].split(".");
    //format of sqlDateArr4[] = ['ss','ms']
    var sSecond = sqlDateArr4[0];
    //var sMillisecond = sqlDateArr4[1];
	
    //return new Date(sYear,sMonth,sDay,sHour,sMinute,sSecond,sMillisecond);
	return new Date(sYear,sMonth,sDay,sHour,sMinute,sSecond);
}

function updateDate(date){
	$("#calendarmonth").text(getDateString(date));
}

function renderCalendar(date){
	
	var $table = $("<table></table>");
	var $tr = $("<tr></tr>");
	// Header
	for(var ih=0; ih<7; ih++){
		var $thweek = $("<th></th>");
		$thweek.text(wd[ih]);
		$tr.append($thweek);
	}
	$table.append($tr);
	
	// body
	
	var year = date.getFullYear();
	var month = date.getMonth();
	// day of first date
    var firstDay = new Date(year, month, 1).getDay();
	// last date of month
    var lastDate = new Date(year, month+1, 0).getDate();
	// date counter
	var counter=0;
	
    for(var i=0; i<6;i++){
    	$tr = $("<tr></tr>"); 	
    	for(var j=0; j<7; j++){
			if(counter<lastDate && (7*i+j+1)>firstDay){
				counter++;
    			var $tdday = $("<td></td>");
				var $spandate = $("<span></span>");
				$spandate.attr("id","d"+counter);
				$spandate.text(counter);
				$tdday.append($spandate);
				$tr.append($tdday);
			}
			else{
				$tr.append($("<td></td>"));
			}
    	}
    	$table.append($tr);
    } 
    //return $table;
	$("#calendarbody").empty();
	$("#calendarbody").append($table);
}

function resetCalendar(){
	events = {};
	publicevents = {};
	$("#eventlist").empty();
	
	renderCalendar(thedate);
}

function lastMonthButton(){
	if(thedate.getMonth()===0){
			thedate.setMonth(thedate.getMonth()+11);
			thedate.setYear(thedate.getFullYear()-1);
	}
	else
		thedate.setMonth(thedate.getMonth()-1);
	updateDate(thedate);
	renderCalendar(thedate);
	getEvents(thedate);
	getPublicEvents(thedate);
	
	$("#eventlist").empty();
	dispTodayEvents();
}

function nextMonthButton(){
	if(thedate.getMonth()==11){
		thedate.setMonth(thedate.getMonth()-11);
		thedate.setYear(thedate.getFullYear()+1);
	}
	else
		thedate.setMonth(thedate.getMonth()+1);
	updateDate(thedate);
	renderCalendar(thedate);
	getEvents(thedate);
	getPublicEvents(thedate);
	
	$("#eventlist").empty();
	dispTodayEvents();
}

function tdClicked(){
	var tdid = $(this).children(":first").attr("id");
	thedate.setDate(tdid.slice(1));
	updateDate(thedate);
	
	$("#eventlist").empty();
	dispTodayEvents();
}

function loginAjax(event){
	var username = document.getElementById("usernameinput").value; // Get the username from the form
	var password = document.getElementById("passwordinput").value; // Get the password from the form
 
	// Make a URL-encoded string for passing POST data:
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
 
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "login_ajax.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		if(jsonData.success){  // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
			thetoken = jsonData.token;
			alert("You've been Logged In!");
			$("#logindiv").hide();
			$("#logoutdiv").show();
			$("#calendartags").show();
			$("#addeventdiv").show();
			getEvents(thedate);
			getPublicEvents(thedate);
		}else{
			alert("You were not logged in.  "+jsonData.message);
		}
	}, false); // Bind the callback to the load event
	xmlHttp.send(dataString); // Send the data
}

function logoutAjax(event){
	
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "logout_ajax.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
	xmlHttp.addEventListener("load", function(event){
		//var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		//console.log(jsonData.message);
		$("#calendartags").hide();
		$("#addeventdiv").hide();
		$("#logoutdiv").hide();
		$("#logindiv").show();
		resetCalendar();
	}, false); // Bind the callback to the load event
	xmlHttp.send(); // Send the data
}

function registerAjax(event){
	var username = document.getElementById("usernameinput").value; // Get the username from the form
	var password = document.getElementById("passwordinput").value; // Get the password from the form
 
	// Make a URL-encoded string for passing POST data:
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
 
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "register_ajax.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		if(jsonData.success){  // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
			alert("You've been registered!");
		}else{
			alert("You were not registered.  "+jsonData.message);
		}
	}, false); // Bind the callback to the load event
	xmlHttp.send(dataString); // Send the data
}

function getEvents(date){
	
	var startdate = new Date(thedate.getFullYear(),thedate.getMonth());
	var enddate = new Date(thedate.getFullYear(),thedate.getMonth()+1);
	var startdatesql = jsToSqlDate(startdate).slice(0,10);
	var enddatesql = jsToSqlDate(enddate).slice(0,10);
	$.ajax({
		type: 'POST',
		url: 'getEvents.php',
		data:{
			time: startdatesql,
			timeend: enddatesql
		},
		success: function(msg) {
			//console.log(msg);
			var tevent = null;
			events = msg.events;
			
			for (var eventidx in msg.events){
				if (msg.events[eventidx] !== undefined){
				tevent = msg.events[eventidx];
				
				var tempdateid = "#d"+tevent.time.slice(8,10);
				
				$(tempdateid).append($("<span></span>")
					.attr("class", "t"+tevent.tag_id)
					.text("*")
				); //tevent.event_id
				}
			}
			
			dispTodayEvents();
		}
	});
}

function getPublicEvents(date){
	$.ajax({
		type: 'POST',
		url: 'getPublicEvents.php',
		success: function(msg) {
			var tevent = null;
			publicevents = msg.events;
			
			for (var eventidx in msg.events){
				if (msg.events[eventidx] !== undefined){
				tevent = msg.events[eventidx];
				
				var tempdateid = "#d"+tevent.time.slice(8,10);
				$(tempdateid).append($("<span></span>")
					.attr("class", "t"+tevent.tag_id)
					.text("!")
				); //tevent.event_id
				}
			}
			
			dispTodayEvents();
		}
	});
}

function dispTodayEvents(){
	$("#eventlist").empty();
	
	var allevents;
	if (!events.length){
		if (!publicevents.length){return;}
		else{allevents = publicevents;}
	}
	else{
		if (!publicevents.length){allevents = events;}
		else{allevents = events.concat(publicevents);}
	}
	
	var tevent;
	for (var eventidx in allevents){
		if (allevents[eventidx] !== undefined){
		tevent = allevents[eventidx];
		if (tevent.time.slice(0,10) == jsToSqlDate(thedate).slice(0,10)){
			var $eventitem = $("<li></li>").attr({
				"id":"e"+tevent.event_id,
				"class":"t"+tevent.tag_id
			});
			var $eventcont = $("<p></p>").text(tevent.content);
			var $eventbody = $("<p></p>").text(tevent.time+" "+tevent.tag_id+" ");
			$eventitem.append($eventcont);
			$eventitem.append($eventbody);
			$eventitem.append('<button class="deletebuttons">Delete</button>');
			$eventitem.append('<button class="editbuttons">Edit</button>');
			$("#eventlist").append($eventitem);
		}
		}
	}
}

function getTags(){
	$.ajax({
		type: 'POST',
		url: 'getTags.php',
		success: function(msg) {
			for (var tagidx in msg.tags){
				if (msg.tags[tagidx] !== undefined){
				//http://stackoverflow.com/questions/170986/what-is-the-best-way-to-add-options-to-a-select-from-an-array-with-jquery
				$("#eventtagselect").append($("<option></option>")
                    .attr("value",tagidx)
                    .text(msg.tags[tagidx])); 
				$("#eventtagselecte").append($("<option></option>")
                    .attr("value",tagidx)
                    .text(msg.tags[tagidx]));
				
				$("#calendartags").append($('<input>', {
					type: 'checkbox',
					name: 'tag',
					value: tagidx,
					"checked":"checked"
				}));
				$("#calendartags").append(msg.tags[tagidx]);
				}
			}	
		}
	});
}

function handleTags(){
	var tid = "t"+this.value;
	//console.log(tid);
	if(this.checked) {
		// checkbox is checked
		$("."+tid).show();
    }
	else{
		$("."+tid).hide();
	}
}

function addEvent(){
	
	var eventtime = new Date(thedate);
	eventtime.setMinutes($("#minute").val());
	eventtime.setHours($("#hour").val());
	var eventtimesql = jsToSqlDate(eventtime);
	var eventcontent = $("#eventinput").val();
	var eventtag = $("#eventtagselect").val();
	if (!eventtag){
		eventtag = "0"; // default
	} 
	//console.log(eventcontent,eventtimesql,eventtag);
	
	$.ajax({
		type: 'POST',
		url: 'addEvent.php',
		data:{
			time: eventtimesql,
			content: eventcontent,
			tag_id: eventtag
		},
		success: function(msg) {
			//console.log(msg);
			//var obj = JSON.parse(msg);
			if (msg.success){
				$("#eventinput").val("");
				$("#minute").val("");
				$("#hour").val("");
			}
			resetCalendar();
			getEvents(thedate);
		}
	});
}

function editEvent(){
	console.log("edit");
	var eventitem = $(this).parent();
	var eid = eventitem.attr("id").slice(1);
	console.log(eid);
	$("#eventide").val(eid);
	
	$("#eventinpute").val(eventitem.children().eq(0).text());
	 
	var eventbody = eventitem.children().eq(1).text();
	//2017-03-19 06:06:49 2
	$("#houre").val(eventbody.slice(11,13));
	$("#minutee").val(eventbody.slice(14,16));
	$("#eventtagselecte").val(eventbody.slice(20,21));
	
	// https://www.w3schools.com/howto/howto_css_modals.asp
	var modal = document.getElementById('myModal');
	modal.style.display = "block";
}

function closemodal(){
	var modal = document.getElementById('myModal');
	modal.style.display = "none";
}

function submitEditEvent(){
	//console.log("submitedit");
	
	var eventtime = new Date(thedate);
	eventtime.setMinutes($("#minutee").val());
	eventtime.setHours($("#houre").val());
	var eventtimesql = jsToSqlDate(eventtime);
	var eventcontent = $("#eventinpute").val();
	var eventtag = $("#eventtagselecte").val();
	if (!eventtag){
		eventtag = "0"; // default
	} 
	var eventid = $("#eventide").val();
	//console.log(eventcontent,eventtimesql,eventtag,eventid);
	
	$.ajax({
		type: 'POST',
		url: 'editEvent.php',
		data:{
			time: eventtimesql,
			content: eventcontent,
			tag_id: eventtag,
			event_id: eventid,
			token: thetoken
		},
		success: function(msg) {
			console.log(msg);
			//var obj = JSON.parse(msg);
			if (msg.success){
				var modal = document.getElementById('myModal');
				modal.style.display = "none";
				$("#eventinpute").val("");
				$("#minutee").val("");
				$("#houre").val("");
				resetCalendar();
				getEvents(thedate);
			}
		}
	});
}

function deleteEvent(){
	//console.log("delete");
	var eid = $(this).parent().attr("id").slice(1);
	//console.log(eid);
	
	$.ajax({
		type: 'POST',
		url: 'deleteEvent.php',
		data:{
			event_id: eid,
			token: thetoken
		},
		success: function(msg) {
			console.log(msg);
			resetCalendar();
			getEvents(thedate);
		}	
	});	
}

function displayProfile(){
	//console.log("profile")
	$.ajax({
		type: 'POST',
		url: 'getUserEvents.php',
		success: function(msg) {
			//console.log(msg);
			var eventsalert = "All Events Created: \n";
			var tevent;
			for (var eventidx in msg.events){
				if (msg.events[eventidx] !== undefined){
				tevent = msg.events[eventidx];
				eventsalert += tevent.time+" "+tevent.tag_id+" "+tevent.content+"\n";
				}
			}
			alert(eventsalert);
		}	
	});	
}
