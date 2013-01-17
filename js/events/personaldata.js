function handleDataSubmit(event)
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
				$('head').append('<link rel="stylesheet" href="'+data['css']+'" type="text/css" />');
			}
			if('js' in data)
			{
				$.getScript(data['js']);
			}
			var newContent = $(data['content']).find('.content_wrapper .content');
			var oldContent = $('#moreDataNeededBox .content');
			var wrapper = $('#moreDataNeededBox .content_wrapper');
			
			newContent.fadeTo(0,0);
			
			wrapper.append(newContent);

			oldContent.fadeTo(fadeDuration,0);
			newContent.fadeTo(fadeDuration,1,function(){
				oldContent.remove();
			});
		}
		else
		{
			if(data['out_of_range'] || data['invalid_value'])
			{
				alert('Les valeurs indiquées sont invalides, merci de vérifier les données entrées.');
			}
			else
			{
				alert('Failed');
			}
		}
	}).fail(handleFailure);
	return false;
}

$(document).ready(function(){
	$('#moreDataNeededBox .field input').eq(0).focus();
	$('#moreDataNeededBox form').unbind('submit');
	$('#moreDataNeededBox form').submit(handleDataSubmit);
})