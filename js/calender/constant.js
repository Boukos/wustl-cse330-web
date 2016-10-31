NAME_OF_DAY = ['SUN', 'MON', 'TUE', 'WED', 'THR', 'FRI', 'SAT'];
COLORS = ['#009900', '#3366ff', '#cc00cc', '#F9910A', '#cc1230', '#8853af'];
$cellTemplate = $('<td class="cell"><div><div class="date"></div><div class="events"><ul></ul></div></div></td>');
$emptyCellTemplate = $('<td class="empty"></td>');
$eventTemplate = $('<div class="event"><span class="event-time"></span><span class="event-tag"></span><span class="event-title"></span><span class="event-action"><button class="eventDeleteBtn">Delete</button></span></div>')
