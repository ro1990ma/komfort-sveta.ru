$(function(){
	
	var note = $('#note'),
		ts = new Date(2017, 0, 1),
		newYear = false;
	
	$('#countdown').countdown({
		timestamp	: ts,
		callback	: function(days, hours, minutes, seconds){
			
			var message = "";
			
			message += "Дней: " + days +", ";
			message += "часов: " + hours + ", ";
			message += "минут: " + minutes + " и ";
			message += "секунд: " + seconds + " <br />";
			
			note.html(message);
		}
	});
	
});