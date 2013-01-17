function submitRoomForm(form)
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
			else if(data['no_such_room'])
			{
				alert("Une erreur s'est produite, la chambre indiquée n'existe pas.");
			}
			else if(data['room_is_full'])
			{
				alert("La chambre demandée est maintenant pleine.");
			}
			else if('closed' in data)
			{
				alert("Les inscriptions dans les chambres sont maintenant fermées. Rendez-vous à SuperDévoluy !");
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
	if(!window.confirm('Ajouter '+name+' à la chambre (vous ne pourrez plus le supprimer) ?'))
	{
		select.find(':selected').removeAttr('selected');
		return;
	}
	
	var form = select.parents('form');
	var roomId = form.find('input[name="roomId"]').val();
	
	var path = form.attr('action');
	var data = {
		setRoomForOtherUser:'true',
		userId:userId,
		roomId:roomId
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
			else if(data['no_such_room'])
			{
				alert("Une erreur s'est produite, la chambre indiquée n'existe pas.");
			}
			else if(data['room_is_full'])
			{
				alert("La chambre est maintenant pleine.");
			}
			else if(data['not_allowed'])
			{
				alert("Vous n'avez pas l'autorisation d'ajouter des personnes dans d'autres chambres que la vôtre.");
			}
			else if(data['user_has_room'])
			{
				alert("La personne choisie a été inscrite dans une autre chambre.");
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
}



function submitNameChange(event)
{
	var input = $(event.target);
	var name = input.val();
	
	var form = input.parents('form');
	var roomId = form.find('input[name="roomId"]').val();
	
	var path = form.attr('action');
	var data = {
		setRoomName:'true',
		roomId:roomId,
		name:name
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
			alert('Ton nouveau nom de chambre a bien été enregistré.');
		}
		else
		{
			if(data['invalid'])
			{
				alert('Requête invalide, veuillez réessayer plus tard.');
			}
			else if(data['no_such_room'])
			{
				alert("Une erreur s'est produite, la chambre indiquée n'existe pas.");
			}
			else if(data['not_allowed'])
			{
				alert("Vous n'avez pas l'autorisation de modifier le nom d'une chambre à laquelle vous n'appartenez pas.");
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
}

$(document).ready(function(){
	$('#roomsContent form').unbind('submit');
	$('#roomsContent select').change(submitSelectChange);
	$('#roomsContent .room > .title input').change(submitNameChange);
});