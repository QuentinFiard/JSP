function handleForgottenPasswordFormResponse(data)
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
		var oldContent = $('#forgottenPasswordBox .content');
		var wrapper = $('#forgottenPasswordBox .content_wrapper');
		
		newContent.fadeTo(0,0);
		
		wrapper.append(newContent);

		oldContent.fadeTo(fadeDuration,0);
		newContent.fadeTo(fadeDuration,1,function(){
			oldContent.remove();
		});
	}
	else
	{
		if(data['missing_fields'])
		{
			alert('Veuillez compléter tous les champs.');
		}
		if(data['wrong_captcha'])
		{
			alert('Code de sécurité invalide, veuillez réessayer.');
		}
		if(data['invalid_email'])
		{
			alert("L'adresse mail indiquée est invalide.");
		}
		if(data['no_such_user'])
		{
			alert("Aucun compte n'est associé à cette adresse mail.");
		}
		$('#captcha_code').val('');
		$('#newCaptcha').click();
	}
}

function submitForgottenPasswordForm(event)
{
	var form = $(event.target);
	var data = form.serialize();
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleForgottenPasswordFormResponse(data);}).fail(function(data){handleFailure(data);});

	return false;
}

$(document).ready(function(){
	$('#forgottenPasswordBox form').submit(submitForgottenPasswordForm)
	$('#forgottenPasswordEmail').focus();
});