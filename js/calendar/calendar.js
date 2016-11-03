/*globals $:false */

var NAMEOFDAY = ['SUN', 'MON', 'TUE', 'WED', 'THR', 'FRI', 'SAT'];
var COLORS = ['Lime', 'Blue', 'Green', 'Khaki', 'Red', 'Fuchsia'];

var currentMonthDate = new Date();
currentMonthDate.setDate(1);
var token = null;
var user = null;
var events = {};
var tags = {};

var $wrapper = $('#wrapper');  
var $eventList = $('#event-list');
var $login = $('#login');
var $logout = $('#logout');

// date 0: last month's last date & day
/* jshint loopfunc:true */

function getMonthString(d){
    var yearStr = d.getFullYear();
    var monthStr = 1+d.getMonth()<10 ? '0'+(1+d.getMonth()) : 1+d.getMonth();
    return yearStr +'-'+ monthStr;
}

function getDateString(d){
    var yearStr = d.getFullYear();
    var monthStr = 1+d.getMonth()<10 ? '0'+(1+d.getMonth()) : 1+d.getMonth();
    var dateStr = d.getDate()<10 ? '0'+d.getDate() : d.getDate();
    return yearStr +'-'+ monthStr +'-'+ dateStr;
}

function createTable(startdate){
    var startDate = new Date(startdate);
    startDate.setTime( startDate.getTime() + startDate.getTimezoneOffset()*60*1000 );

    var $table = $('<table></table>');
	// header
    //$tr = $('<tr></tr>');
    var $tr = $('<tr></tr>');
    for(var ih=0; ih<7; ih++){
        $tr.append($('<th>'+NAMEOFDAY[ih]+'</th>'));
    }
    $table.append($tr);
    
    // content
    var d= currentMonthDate;
	var year=d.getFullYear();
	var month=d.getMonth();
    var m=new Date(year, month, 1).getDay();
	//if(month+1>11){year+=1;month=0;}
    var n=new Date(year, month+1, 0).getDate();
	
	var sdate = startDate.getDate();
    var k=0;
    for(var i=0; i<6;i++){
		
    	$tr = $('<tr></tr>'); 	
    	for(var j=0; j<7; j++){
			d = startDate;
			d.setDate(sdate+k);
			if(k<n && ((7*i+j+1)>m)){
				k++;
    			var $tday = $('<td></td>');
				$tday.append("<div id=d"+k+">"+k+"</div>");
				//$tr.append($('<td>'+k+'<div id=d"'+k+'"></div></td>'));
				var dateStr = getDateString(d);
                if(events.hasOwnProperty(dateStr)){
					$tday.css("font-weight", "bold");
					var thisDateEvent = events[dateStr];
					
                    $.each(thisDateEvent, function(key, e){
                       	var $dot = $("<span class=t"+e.tag_id+">&#9679;</span>");
						$dot.css('color', COLORS[e.tag_id]);
						$tday.append($dot);
                    });
					
				}
				$tr.append($tday);
			}
			else{
				$tr.append($('<td></td>'));
			}
    	}
    	$table.append($tr);
    } 
    		
    return $table;	
}

// function to display calendar
function displayCalendar(){
    var t = createTable(getDateString(currentMonthDate));
    var $wrapper = $('#wrapper');  
	$wrapper.empty();
	$wrapper.append(t);
	$('#add-event').show();
	displayTags();
}

function redisplayCalendar(){
    var t = createTable(getDateString(currentMonthDate));
	$wrapper.empty();
    $wrapper.append(t);
	displayTags();
}

function displayEmptyCalendar(){
    currentMonthDate.setDate(1);
    events = {};
    tags = {};
	$('#add-event').hide();
	$('#tag-toggle').empty();
    var t = createTable(getDateString(currentMonthDate));
    $wrapper.append(t);
}

function displayTags(){
	$('#tag-toggle').empty();
	$('#event-tag-select').empty();
	if(!$.isEmptyObject(tags)){
		$.each(tags, function(key, e){
			var $option = $('<option value="'+e.id+'">'+e.tag+'</option>');
			$('#event-tag-select').append($option);
			var $check = $('<input type="checkbox" checked value="'+e.id+'">');
			var $checkSpan = $('<span>'+e.tag+'</span>');
			$checkSpan.css('color', COLORS[key]);
			$('#tag-toggle').append($check);
			$('#tag-toggle').append($checkSpan);
		});
	}
}

function getEvents(){
    currentMonthDate.setDate(1);
    $.ajax({
        method: "POST",
        url: "getTags.php",
        data: {token:token},
        success: function(data){
			//tags = JSON.parse(data); //data already parsed
            tags = data;
			displayCalendar();
        }
    });

    $.ajax({
        method: "POST",
        url: "getEvents.php",
        data: {token:token},
        success: function(data){
            //events = JSON.parse(data);
            events = data;
			displayCalendar();
        }
    });
}

function showDailyEvent(date){
	$eventList = $('#event-list');
    $eventList.empty();

	var year=currentMonthDate.getFullYear();
	var month=currentMonthDate.getMonth();
    var tempdate = new Date(year, month, date);
    var dateStr = getDateString(tempdate);
	
    var thisDateEvent = events[dateStr];
	
	if (!$.isEmptyObject(thisDateEvent)) {
		$.each(thisDateEvent, function(key, e){
			//$eventDiv = $eventTemplate.clone(true);
			var $eventDiv = $('<div class="event"></div>');
			$eventDiv.attr('event-id', e.id);
			$eventDiv.addClass('tag-'+tags[e.tag_id].id);
			
			/*
			$('<div class="event">
			<span class="event-time"></span> 
			<span class="event-tag"> /span>
			<span class="event-title"></span>
			<span class="event-action">
			<button class="eventDeleteBtn">Delete</button></span></div>')
			*/
			
			$eventDiv.append($("<span class=\"event-time\">"+e.time+": </span>"));
			$eventDiv.append($("<span class=\"event-title\">"+e.title+"; </span>"));
			$eventDiv.append($("<span class=\"event-tag\">"+tags[e.tag_id].tag+"</span>"));
			$eventDiv.append($("<span class=\"event-action\"><button class=\"eventDeleteBtn\">Delete</button></span>"));
			
			//$eventDiv.find('.event-time').text(e.time);
			//$eventDiv.find('.event-tag').text(tags[e.tag_id].tag);
			//$eventDiv.find('.event-title').text(e.title);
			console.log(e.title);
			$eventDiv.css('color', COLORS[e.tag_id]);
			//$eventDiv.append("<a class=\"twitter-share-button\"	href=\"https://twitter.com/intent/tweet?text= \">Tweet</a>")
			$eventList.append($eventDiv);
		});
	}
}

function clearDailyEvent(){
	$eventList = $('#event-list');
    $eventList.empty();
}

// event listeners
// $(document).ready()
$(function(){
	var $currentCell = null;
	var $currentDate = null;	

    $('#calendar-month').text(getMonthString(currentMonthDate));
	$('#detail-header').text(getDateString(currentMonthDate));
	
	// init
	events = {};
	tags = {};
	
	$wrapper = $('#wrapper');
    $wrapper.empty();
	$eventList = $('#event-list');
	$eventList.empty();
    //displayEmptyCalendar();
	
    // event listener for check logged and listen login button
    $.ajax({
        method: "POST",
        url: "checkLogged.php",
        success: function(data){
            if(data.keep){
                $login = $('#login');
                $login.show();
                $logout = $('#logout');
                $logout.hide();
				getEvents();//getTags();
				displayCalendar(); 
				
            }else{
				$login = $('#login');
                $login.show();
                $logout = $('#logout');
                $logout.hide();
                displayEmptyCalendar();
            }
        }
    });
	
		
	// click on calendar
	$(document).on( 'click', 'td', function(){
		//$currentDate = $(this.firstChild).attr('id');
		$currentDate = $(this.firstChild).text();
		if($currentDate.length !== 0){
			$currentCell = $(this);
			$('td').css('background', '#eee');
			$(this).css('background', '#4CAF50');
			var d = currentMonthDate;
			d.setDate(parseInt($currentDate));
			$('#detail-header').text(getDateString(d));
			//displayCalendar();
			showDailyEvent($currentDate); //d
		}
    });

	
	//register button
    $(document).on('click', '#register',function(){
        var username = $("#username")[0].value;
        var password = $("#password")[0].value;
        $.ajax({
            method: "POST",
            url: "register.php",
            data:{ username: username, password: password},
            success: function (data){
                if (data.success){
                    alert("register success!");
                    token = data.token;
                    console.log(token);
					$login = $('#login');
					$login.hide();
					$logout = $('#logout');
					$logout.show();
                    $wrapper = $('#wrapper');
                    $wrapper.empty();
                    displayCalendar();
                }
                else{
                    alert(data.message);
                }
            }
        });
    });

	//login button
    $(document).on('click', '#loginbtn', function(){
        var username = $("#username")[0].value;
        var password = $("#password")[0].value;
        $.ajax({
            method: "POST",
            url: "login.php",
            data:{ username: username, password: password},
            success: function(data){
                if (data.success){
					//alert("success,logged in!");
              
                    token = data.token;
					user = username;
					$login = $('#login');
					$login.hide();
					$logout = $('#logout');
					$logout.show();
                    $wrapper = $('#wrapper');
                    $wrapper.empty();
                    getEvents();
					alert("success,logged in!");
                    
					displayCalendar();
					redisplayCalendar();
                }
                else{
                    alert(data.message);
                }
            }
        });
    });

    //logout button 
    $(document).on('click', '#logoutbtn', function(){
        $.ajax({
            method: "POST",
            url: "logout.php",
            success: function(){
				token = null;
				user = null;
                $login = $('#login');
                $login.show();
                $logout = $('#logout');
                $logout.hide();
                $wrapper = $('#wrapper');
                $wrapper.empty();
				clearDailyEvent();
                displayEmptyCalendar();
            }
        });
    });

	//events
    $(document).on('click', '.eventDeleteBtn', function(){
        var $thisevent = $(this).parents('.event');
        var eventId = $thisevent.attr('event-id');
        $.ajax({
            method: "POST",
            url: "delEvent.php",
            data:{ event_id: eventId },
            success: function(){
				$wrapper = $('#wrapper');
                $wrapper.empty();
                getEvents();
				displayCalendar();
				//redisplayCalendar();
				clearDailyEvent();
				showDailyEvent($currentDate);
            },
			error: function(data){
				console.log(data);
			}
        });
    });
	
	$(document).on('click', '#addEventBtn', function(){
        
        var d = currentMonthDate;
        d.setDate($currentDate);
		var hrsStr = parseInt($('#hrs').val()) < 10 ? '0'+ $('#hrs').val() : $('#hrs').val();
        var minsStr = parseInt($('#mins').val()) < 10 ? '0'+ $('#mins').val() : $('#mins').val();
        var dateStr = getDateString(d);
        var timeStr = dateStr + ' '+hrsStr+':'+minsStr+':00';
        var tag_id = parseInt($('#event-tag-select').find(":selected").val());
        
		
        $.ajax({
            method: "POST",
            url: "addEvent.php",
            data:{ content: $('#content').val(), 
				//username: user,
				timestamp: timeStr, 
				tag_id: tag_id, 
				token:token},
            success: function(data){
				// data already parsed
				if (!data.success){
					console.log("failed.."+data.message);
				}
				else{
					console.log("success!");
				}
				$wrapper = $('#wrapper');
                $wrapper.empty();
                getEvents();
				displayCalendar();
				//redisplayCalendar();
				showDailyEvent($currentDate);
            },
			error: function(data){
				console.log(data);
			}
        });
    });	
	
	$(document).on('click', '#shareEventBtn', function(){
        
        var d = currentMonthDate;
        d.setDate($currentDate);
		var hrsStr = parseInt($('#hrs').val()) < 10 ? '0'+ $('#hrs').val() : $('#hrs').val();
        var minsStr = parseInt($('#mins').val()) < 10 ? '0'+ $('#mins').val() : $('#mins').val();
        var dateStr = getDateString(d);
        var timeStr = dateStr + ' '+hrsStr+':'+minsStr+':00';
        var tag_id = parseInt($('#event-tag-select').find(":selected").val());
		var sharedusername = $('#sharedUser').val();	
		
        $.ajax({
            method: "POST",
            url: "shareEvent.php",
            data:{ content: $('#content').val(), 
				sharedusername: sharedusername,
				timestamp: timeStr, 
				tag_id: tag_id, 
				token:token},
            success: function(data){
				// data already parsed
				if (!data.success){
					console.log("failed.."+data.message);
				}
				else{
					console.log("success!");
				}
				$wrapper = $('#wrapper');
                $wrapper.empty();
                getEvents();
				displayCalendar();
				redisplayCalendar();
				showDailyEvent($currentDate);
            },
			error: function(data){
				console.log(data);
			}
        });
    });		
	
    //month switch button
    $(document).on('click', '#prevMonthBtn', function(){

        currentMonthDate.setMonth(currentMonthDate.getMonth()-1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
		$('#detail-header').text(getDateString(currentMonthDate));
        redisplayCalendar();
    });

    $(document).on('click', '#nextMonthBtn', function(){
        currentMonthDate.setMonth(currentMonthDate.getMonth()+1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
		$('#detail-header').text(getDateString(currentMonthDate));
        redisplayCalendar(); //re
    });

	/*$(document).on('click', '#showstat', function(){
		console.log(events);
		console.log(tags);
		displayCalendar();
	});*/

    $(document).on('click', '#tag-toggle input:checkbox', function(){
        var tag_id = $(this).val();
		var style = null;
        if ($(this).is(':checked')) {
            style = $('<style>.tag-'+tag_id+' { display: block; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).show();
			$('.t'+tag_id).show();
        }else{
            style = $('<style>.tag-'+tag_id+' { display: none; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).hide();
			$('.t'+tag_id).hide();
        }
    });


});

