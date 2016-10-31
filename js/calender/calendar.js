
currentMonthDate = new Date();
currentMonthDate.setDate(1);
token = null;
user = null;
events = {};

function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}


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

    // console.log(startDate);
    
    var date = startDate.getDate();

    var dateLoc = 0;

    var emptyCell = true;


    $table = $('<table></table>');

    $tr = $('<tr></tr>');
    for(var j=0; j<7; j++){
        $tr.append($('<th>'+NAME_OF_DAY[j]+'</th>'));
    }
    $table.append($tr);


    var daysInThisMonth = daysInMonth(startDate.getMonth()+1, startDate.getFullYear());

    while(dateLoc+1 < daysInThisMonth){
        $tr = $('<tr></tr>');
        for(var j=0; j<7; j++){

            var d = startDate;
            d.setDate(date+dateLoc);

            if(d.getDay() == j){
                emptyCell = false;
            }

            if(dateLoc+1 > daysInThisMonth){
                emptyCell = true;
            }

            if(!emptyCell){

                $cell = $cellTemplate.clone(true);
                $cell.find('.date').text(''+d.getDate());

                var dateStr = getDateString(d);
                if(events.hasOwnProperty(dateStr)){
                    var thisDateEvent = events[dateStr];

                    $.each(thisDateEvent, function(key, e){
                        $li = $('<li event-id="'+e.id+'">'+e.title+'</li>');
                        $li.css('color', COLORS[e.tag_id]);
                        $li.addClass('tag-'+tags[e.tag_id].id);
                        $cell.find('ul').append($li);
                    });
                }

                $tr.append($cell);
                dateLoc ++;
            }else{
                $cell = $emptyCellTemplate.clone(true);
                $tr.append($cell);
            }
            
        }
        $table.append($tr);
    }


    return $table;
}


// function to display calendar
function displayCalendar(){
    t = createTable(getDateString(currentMonthDate));
    $wrapper.append(t);
}

function displayEmptyCalendar(){
    currentMonthDate.setDate(1);
    events = {};
    tags = {};
    t = createTable(getDateString(currentMonthDate));
    $wrapper.append(t);
}


function getEventsAtDate(){
	
    currentMonthDate.setDate(1);

    $.ajax({
        method: "POST",
        url: "getEventsAtDate.php",
        data: {thedate:date,token:token},
        success: function(d){
            //events = JSON.parse(d);
            events = d;
            //displayCalendar()
        }
    });
  
}

function getEvents(){
    currentMonthDate.setDate(1);
    $.ajax({
        method: "POST",
        url: "getTags.php",
        data: {token:token},
        success: function(data){
            //data already parsed
			//tags = JSON.parse(data);
            tags = data;
            //console.log(data);
            $.each(tags, function(key, e){
                $option = $('<option value="'+e.id+'">'+e.tag+'</option>');
                $('#event-tag-select').append($option);
                $check = $('<input type="checkbox" checked value="'+e.id+'">');
                $checkSpan = $('<span>'+e.tag+'</span>');
                $checkSpan.css('color', COLORS[key]);
                $('#tag-toggle').append($check);
                $('#tag-toggle').append($checkSpan);
            });
        }
    });

    $.ajax({
        method: "POST",
        url: "getEvents.php",
        data: {token:token},
        success: function(d){
            //events = JSON.parse(d);
            events = d;
            displayCalendar()
        }
    });
  
}

function showDailyEvent(d){
    $eventList.empty();
    var dateStr = getDateString(d);
    var thisDateEvent = events[dateStr];
	if (!jQuery.isEmptyObject(thisDateEvent)) {
		$.each(thisDateEvent, function(key, e){
			$eventDiv = $eventTemplate.clone(true);
			$eventDiv.attr('event-id', e.id);
			$eventDiv.addClass('tag-'+tags[e.tag_id].id);
			$eventDiv.find('.event-time').text(e.time);
			$eventDiv.find('.event-tag').text(tags[e.tag_id].tag);
			$eventDiv.find('.event-title').text(e.title);
			$eventDiv.css('color', COLORS[e.tag_id]);
			$eventList.append($eventDiv);
		});
	}
}


// event listeners
// document.ready
$(function(){

    $currentCell = null;

    $wrapper = $('#wrapper');
    $popover = $('#popover');
    $eventList = $('#event-list');
    $('#calendar-month').text(getMonthString(currentMonthDate));

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
				getEvents();
				//displayCalendar(); // added for disp
				
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
	$(document).on('click', '.cell', function(){
		$currentCell = $(this);
        $('.cell').css('background', '#fff');
        $(this).css('background', '#00cccc');
		console.log(parseInt($(this).find('.date').text()));
		//getEventsAtDate();
		var d = currentMonthDate;
        d.setDate(parseInt($(this).find('.date').text()));
		
		$('#detail-header').text(getDateString(d));
        showDailyEvent(d);	
	});
	
    //listening click on class cell and pop out popover
    $(document).on('click', '.cellx', function(){
        $currentCell = $(this);
        $('.cell').css('background', '#fff');
        $(this).css('background', '#00cccc');
		

        var d = currentMonthDate;

        d.setDate(parseInt($(this).find('.date').text()));


        var events = [];
        var tempId = 10;
        $list_event = $popover.find('#list_event');

        $list_event.empty('li');

        $(this).find('li').each(function(){
            var e = $(this).text();
            $list_event.append('<li><div class="evtlist">'+ e + '</div><button name = '+tempId.toString() +'>[x]</button></li>')

            events.push(e);
        });

        $('#detail-header').text(getDateString(d));
        showDailyEvent(d);

    });
	
	//register button
    $(document).on('click','#register',function(){
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
                    alert("success,logged in!");
                    token = data.token;
					user = username;
					$login = $('#login');
					$login.hide();
					$logout = $('#logout');
					$logout.show();
                    $wrapper = $('#wrapper');
                    $wrapper.empty();
                    displayCalendar(); //?
                    getEvents();
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
                displayEmptyCalendar();
                $('#tag-toggle').empty();
            }
        });
    });


    $(document).on('click', '.eventDeleteBtn', function(){
        $thisevent = $(this).parents('.event');
        var eventId = $thisevent.attr('event-id');
        $.ajax({
            method: "POST",
            url: "delEvent.php",
            data:{ event_id: eventId},
            success: function(){
                $thisevent.remove();
                $currentCell.find('li').each(function (index, element) {
                    if($(element).attr('event-id') == eventId){
                        $(element).remove();
                    }
                });
				$wrapper = $('#wrapper');
                $wrapper.empty();
                displayCalendar(); //?
                getEvents();
            }
        });
    });

	$(document).on('click', '#addEventBtn', function(){
        
        var d = currentMonthDate;
        d.setDate(parseInt($currentCell.find('.date').text()));
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
				//console.log(data);
				// data already parsed
				if (!data.success){
					console.log("failed.."+jsonData.message);
				}
				else{
					console.log("success!");
				}
            },
			error: function(data){
				console.log(data);
			}
        });
    });	
	
    $(document).on('click', '#addEventBtnx', function(){
        // alert($('#hrs').val());
        
        var d = currentMonthDate;
        d.setDate(parseInt($currentCell.find('.date').text()));
		var hrsStr = parseInt($('#hrs').val()) < 10 ? '0'+ $('#hrs').val() : $('#hrs').val();
        var minsStr = parseInt($('#mins').val()) < 10 ? '0'+ $('#mins').val() : $('#mins').val();
        var dateStr = getDateString(d);
        var timeStr = dateStr + ' '+hrsStr+':'+minsStr+':00';
	
        var tag_id = parseInt($('#event-tag-select').find(":selected").val());
        console.log(timeStr+tag_id+$('#content').val());
		
        $.ajax({
            method: "POST",
            url: "addEvent.php",
            data:{ content: $('#content').val(), timestamp: timeStr, tag_id: tag_id, token:token},
            success: function(data){
				if (!data.success){
					console.log(data.message);
				}
				else{
					console.log("success!");
				}
                $li = $('<li event-id="'+data+'">'+$('#content').val()+'</li>');
                $li.css('color', COLORS[tag_id]);
                $li.addClass('tag-'+tags[tag_id].id);
                $currentCell.find('ul').append($li);
                $eventDiv = $eventTemplate.clone(true);
                $eventDiv.attr('event-id', data);
                $eventDiv.addClass('tag-'+tags[tag_id].id);
                $eventDiv.find('.event-time').text(hrsStr+':'+minsStr);
                $eventDiv.find('.event-tag').text(tags[tag_id].tag);
                $eventDiv.find('.event-title').text($('#content').val());
                $eventDiv.css('color', COLORS[tag_id]);
                $eventList.append($eventDiv);

                if(events.hasOwnProperty(dateStr)){
                    events[dateStr].push({"id":data,"title":$('#content').val(),"time":hrsStr+":"+minsStr,"tag_id":tag_id});
                }else{
                    events[dateStr] = [{"id":data,"title":$('#content').val(),"time":hrsStr+":"+minsStr,"tag_id":tag_id}];
                }
                $('#hrs').val('');
                $('#mins').val('');
                $('#content').val('');
            }
        });
    });


    //month switch button
    $(document).on('click', '#prevMonthBtn', function(){

        currentMonthDate.setMonth(currentMonthDate.getMonth()-1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
        t = createTable(getDateString(currentMonthDate));    
        $wrapper.empty();
        $wrapper.append(t);
    });

    $(document).on('click', '#nextMonthBtn', function(){
        currentMonthDate.setMonth(currentMonthDate.getMonth()+1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
        t = createTable(getDateString(currentMonthDate));    
        $wrapper.empty();
        $wrapper.append(t);
    });


    //popover close button
    $(document).on('click', '#popoverCloseBtn', function(){
        $popover.hide();
    });


    $(document).on('click', '#tag-toggle input:checkbox', function(){
        var tag_id = $(this).val();
        if ($(this).is(':checked')) {
            var style = $('<style>.tag-'+tag_id+' { display: block; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).show();
        }else{
            var style = $('<style>.tag-'+tag_id+' { display: none; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).hide();
        }
    });


});

