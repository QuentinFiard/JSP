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
		data['transition']='fadeOutToWhiteThenFadeIn';
		handlePageChangeSuccess(path,data);
	}).fail(function(data){
		handlePageChangeFailure(path,data);
	});
	return false;
}

function changeSelectedRoom(select)
{
	select = $(select);
	var roomId = select.val();
	var form = select.parents('form');
	var path = form.attr('action');
	var data = {
		selectedRoomId:roomId
	}
	var settings = {
		async:true,
		dataType: 'json',
		type:'get',
		data:data
	}
	$.ajax(serverUrl+path,settings).done(function(data){
		data['transition']='fadeOutToWhiteThenFadeIn';
		handlePageChangeSuccess(path,data);
	}).fail(function(data){
		handlePageChangeFailure(path,data);
	});
}

$(document).ready(function(){
	$('form').unbind('submit');
	$('form').submit(handleFormSubmit);
});