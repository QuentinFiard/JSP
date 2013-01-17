function shake()
{
	var box = $('#wrapper .menu');
	
	var offset = 10;
	var duration = 20;
	var nb = 3;
	
	if(!box.is(':animated'))
	{

		box.animate({left:'+='+offset},{
			duration:duration,
			queue:true,
		});
		
		for(var i=0 ; i<nb-1 ; i++)
		{
			box.animate({left:'-='+2*offset},{
				duration:2*duration,
				queue:true,
			});
			box.animate({left:'+='+2*offset},{
				duration:2*duration,
				queue:true,
			});
		}

		box.animate({left:'-='+offset},{
			duration:duration,
			queue:true,
		});
		
	}
}

function handleLoginSuccess(data)
{
	if(!data['success'])
	{
		var form = $("#knownUser form");
		if(data['wrong_email_format'])
		{
			form.find('#mailKnownUser').val('');
		}
		form.find('#passwordKnownUser').val('');

		shake();
	}
	else
	{
		window.location.href=serverUrl;
	}
}

function handleLoginFailure(data)
{
	alert('Impossible de communiquer avec le serveur, vérifier votre connection internet.')
}

function submitLoginForm(event)
{
	var form = $(event.target);

	var data = formToData(form);
	
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleLoginSuccess(data);}).fail(function(data){handleLoginFailure(data);});

	return false;
}



function handleCreateAccountSuccess(data)
{
	if(!data['success'])
	{
		var form = $("#newUser form");
		if(data['missing_fields'])
		{
			alert('Veuillez compléter tous les champs.');
		}
		if(data['wrong_captcha'])
		{
			alert("Le code de sécurité indiqué est invalide.");
		}
		if(data['wrong_email_format'])
		{
			alert("L'adresse email indiquée est invalide.");
			form.find('#mailKnownUser').val('');
		}
		if(data['invalid_names'])
		{
			alert("Les noms que vous avez entrés sont invalides.");
			form.find('#firstnameNewUser').val('');
			form.find('#lastnameNewUser').val('');
		}
		if(data['password_match_error'])
		{
			alert('Les mots de passe entrés ne correspondent pas !');

			form.find('#passwordNewUser').val('');
			form.find('#passwordConfirm').val('');
		}
		if(data['email_already_exists'])
		{
			alert('Un compte est déjà associé à cette adresse email !');
			form.find('#mailKnownUser').val('');
		}
		$('#captcha_code').val('');
		$('#newCaptcha').click();
	}
	else
	{
		if('css' in data)
		{
			$('head').append('<link rel="stylesheet" href="'+data['css']+'" type="text/css" />');
		}
		if('js' in data)
		{
			$.getScript(data['js']);
		}
		var box = $(data['content']);
		
		box.fadeTo(0,0);
		
		$('body').append(box);
		
		box.fadeTo(fadeDuration,1);
	}
}

function handleCreateAccountFailure(data)
{
	alert('Impossible de communiquer avec le serveur, vérifier votre connection internet.')
}

function submitCreateAccountForm(event)
{
	var form = $(event.target);

	var password = form.find('#passwordNewUser').val();
	var passwordConfirm = form.find('#passwordConfirm').val();
	
	if(password!=passwordConfirm)
	{
		alert('Les mots de passe entrés ne correspondent pas !');
		form.find('#passwordNewUser').val('');
		form.find('#passwordConfirm').val('');
		return false;
	}

	var data = createAccountFormToData(form);
	
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleCreateAccountSuccess(data);}).fail(function(data){handleCreateAccountFailure(data);});

	return false;
}

$(document).ready(function(){
	prepareLoginForm();
	$('#knownUser form').unbind('submit');
	$('#knownUser form').submit(submitLoginForm);
	$('#newUser form').unbind('submit');
	$('#newUser form').submit(submitCreateAccountForm);
});