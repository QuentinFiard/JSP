function handleFormSubmit(event)
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
	$.ajax(serverUrl+path,settings).done(function(data){
		if(data['success'])
		{
			if('css' in data)
			{
				$('head').append('<link rel="stylesheet" href="'+json['css']+'" type="text/css" />');
			}
			if('js' in data)
			{
				$.getScript(json['js']);
			}
			var box = $(data['content']);
			
			box.fadeTo(0,0);
			
			$('body').append(box);
			
			box.fadeTo(fadeDuration,1);
		}
		else
		{
			if(data['must_agree'])
			{
				alert("Merci d'indiquer que vous acceptez les conditions d'inscription avant de vous inscrire.");
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
	return false;
}

function correctWindowSize()
{
	var b = $('body');
	var height = b.height();
	var width = b.width();
	var image_height = height - 160;
	var image_ratio = 2880./1800.;
	var min_width = image_ratio*image_height;
	b.css('min-width',min_width);
}

$(window).resize(correctWindowSize);

$(document).ready(function(){
	$('nav .current').removeClass('current');
	var button = $('#'+$('#eventButton').val());
	button.addClass('current');
	
	correctWindowSize();
	$('form').submit(handleFormSubmit);
})