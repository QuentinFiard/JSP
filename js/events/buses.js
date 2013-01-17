function submitBusForm(form)
{
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
		else
		{
			if(data['invalid'])
			{
				alert('Requête invalide, veuillez réessayer plus tard.');
			}
			else if(data['no_such_bus'])
			{
				alert("Une erreur s'est produite, le bus indiqué n'existe pas.");
			}
			else if(data['bus_is_full'])
			{
				alert("Le bus demandé est maintenant complet.");
			}
			else if('closed' in data)
			{
				alert("Les inscriptions dans les bus sont maintenant fermées. Rendez-vous à SuperDévoluy !");
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
	return false;
}

function submitSelectChange(event)
{
	var select = $(event.target);
	var userId = select.val();
	if(!userId)
	{
		return;
	}
	var name = select.find(':selected').text();
	if(!window.confirm('Ajouter '+name+' dans le bus (vous ne pourrez plus l\'en ôter) ?'))
	{
		select.find(':selected').removeAttr('selected');
		return;
	}
	
	var form = select.parents('form');
	var busId = form.find('input[name="busId"]').val();
	
	var path = form.attr('action');
	var data = {
		setBusForOtherUser:'true',
		userId:userId,
		busId:busId
	}
	var settings = {
		async:true,
		dataType: 'json',
		type:'post',
		data:data
	}
	$.ajax(serverUrl+path,settings).done(function(data){
		if(data['success'])
		{
			window.location.reload(true);
		}
		else
		{
			if(data['invalid'])
			{
				alert('Requête invalide, veuillez réessayer plus tard.');
			}
			else if(data['no_such_bus'])
			{
				alert("Une erreur s'est produite, le bus indiqué n'existe pas.");
			}
			else if(data['bus_is_full'])
			{
				alert("Le bus est maintenant plein.");
			}
			else if(data['not_allowed'])
			{
				alert("Vous n'avez pas l'autorisation d'ajouter des personnes dans d'autres bus que le vôtre.");
			}
			else if(data['user_has_bus'])
			{
				alert("La personne choisie a déjà été inscrite dans un bus.");
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
}


$(document).ready(function(){
	$('#busesContent form').unbind('submit');
	$('#busesContent select').change(submitSelectChange);
});