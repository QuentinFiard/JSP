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
			window.location.reload(true);
		}
		else
		{
			if(data['option_has_users'])
			{
				alert('Impossible de modifier cette option : elle a déjà été choisie par certains utilisateurs.');
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
	return false;
}

function changeSelectedOption(select)
{
	select = $(select);
	var optionId = select.val();
	var row = $('#option'+optionId);
	var data = row.find('td');
	
	var form = select.parents('form');
	
	var headers = $('th');
	
	for(var i=0 ; i<headers.length ; i++)
	{
		var input = form.find("[name='"+headers.eq(i).text()+"']");
		input.val(data.eq(i).text());
	}
}