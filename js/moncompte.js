function handleChangeValueResponse(data,form)
{
	if(!data['success'])
	{
		var old_value = form.find('.old_value').val();
		form.find('.value').val(old_value);
		
		if(data['out_of_range'] || data['invalid_value'])
		{
			alert('La valeur indiquée est invalide, merci de vérifier les données entrées !');
		}
	}
	else
	{
		form.find('.value').val(data['value']);
		form.find('.old_value').val(data['value']);
		form.find("input[type='submit']").fadeTo(0,0);
	}
}

function valueChange(event)
{
	var input = $(event.target);
	var value = input.val();
	var form = input.parents('form');
	var old_value = form.find('.old_value').val();
	var button = form.find("input[type='submit']");
	if(value!=old_value)
	{
		button.fadeTo(0,1);
	}
	else
	{
		button.fadeTo(0,0);
	}
}

function updateValue(event)
{
	var form = $(event.target);
	var old_value = form.find('.old_value');
	old_value.detach();
	var data = form.serialize();
	form.append(old_value);
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleChangeValueResponse(data,form);}).fail(function(data){handleFailure(data);});

	return false;
}



function handleChangePasswordBoxReception(json)
{
	if('css' in json)
	{
		$('head').append('<link rel="stylesheet" href="'+json['css']+'" type="text/css" />');
	}
	if('js' in json)
	{
		$.getScript(json['js']);
	}
	var box = $(json['content']);
	
	box.fadeTo(0,0);
	
	$('body').append(box);
	
	box.fadeTo(fadeDuration,1);
}

function showChangePasswordBox()
{
	var url = serverUrl+'/moncompte/changepassword';
	var settings = {
		async:true,
		dataType:'json'
	}
	$.ajax(url,settings).done(function(data){handleChangePasswordBoxReception(data);}).fail(function(data){handleFailure();});
}

$(document).ready(function(){
	$('nav .current').removeClass('current');
	$('form').submit(updateValue);
	$('form input.value').keydown(valueChange).keyup(valueChange).change(valueChange);
	$('form input.button').fadeTo(0,0);
	$('form input.button').css('visibility','visible');
});