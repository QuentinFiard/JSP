function handleChangePasswordFormResponse(data)
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
		var oldContent = $('.alert_box .content');
		var wrapper = $('.alert_box .content_wrapper');
		
		newContent.fadeTo(0,0);
		
		wrapper.append(newContent);

		oldContent.fadeTo(fadeDuration,0);
		newContent.fadeTo(fadeDuration,1,function(){
			oldContent.remove();
		});
	}
	else
	{
		var form = $('.alert_box form');
		if(data['missing_fields'])
		{
			alert('Veuillez compléter tous les champs.');
		}
		if(data['invalid_password'])
		{
			alert("Le mot de passe indiqué est invalide.");
			form.find('#old_password').val('');
			form.find('#old_password').focus();
		}
		if(data['password_match_error'])
		{
			alert('Les mots de passe entrés ne correspondent pas !');

			form.find('#password').val('');
			form.find('#passwordConfirm').val('');
			form.find('#password').focus();
		}
	}
}

function submitChangePasswordForm(event)
{
	var form = $(event.target);
	var data = createAccountFormToData(form);

	var password = form.find('#password').val();
	var passwordConfirm = form.find('#passwordConfirm').val();
	
	if(password!=passwordConfirm)
	{
		alert('Les mots de passe entrés ne correspondent pas !');
		form.find('#password').val('');
		form.find('#passwordConfirm').val('');
		return false;
	}
	
	if (typeof CryptoJS != 'undefined')
	{
		data['digest_old'] = ""+CryptoJS.SHA256(data['old_password']);
		data['sha_old'] = 'true';
		delete data['old_password'];
	}
	
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleChangePasswordFormResponse(data);}).fail(function(data){handleFailure(data);});

	return false;
}

$(document).ready(function(){
	$('.alert_box form').submit(submitChangePasswordForm)
	$('#old_password').focus();
});