function handleCancelationFormResponse(data)
{
	if(data['success'])
	{
		if('css' in data)
		{
			$('head').append('<link rel="stylesheet" href="'+data['css']+'" type="text/css" />');
		}
		if('js' in data)
		{
			$.getScript(data['js']);
		}
		var newContent = $(data['content']);
		var oldContent = $('#cancelReservationBox .content');
		var wrapper = $('#cancelReservationBox .content_wrapper');
		
		newContent.fadeTo(0,0);
		
		wrapper.append(newContent);

		oldContent.fadeTo(fadeDuration,0);
		newContent.fadeTo(fadeDuration,1,function(){
			oldContent.remove();
		});
	}
	else
	{
		alert('Failed');
	}
}

function handleCancelationFormSubmit(event)
{
	var form = $(event.target);
	var path = form.attr('action');
	var data = form.serialize();
	var settings = {
		async:true,
		dataType: 'json',
		type:'post',
		data:data
	}
	$.ajax(serverUrl+path,settings).done(handleCancelationFormResponse).fail(handleFailure);
	return false;
}

$(document).ready(function(){
	$('#cancelReservationBox form').unbind('submit');
	$('#cancelReservationBox form').submit(handleCancelationFormSubmit);
})