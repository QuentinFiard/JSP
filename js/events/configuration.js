var old_rental_option = null;
var old_rental_price = null;
var old_subvention_option = null;
var old_subvention_price = null;
var has_subvention = false;
var old_forfait_option = null;
var old_forfait_price = null;
var is_reveillon = false;
var old_repas_option = null;
var old_repas_price = null;

var old_label = null;

var currentTable = null;

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
		if(data['success'] || data['more_data_needed'])
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
			alert('Failed');
		}
	}).fail(handleFailure);
	return false;
}

function updateSubventionOffsets(new_subvention_price)
{
	var offsets = $('.subvention .offset')
	for(var i=0 ; i<offsets.length ; i++)
	{
		var offset = offsets.eq(i);
		var price = parseFloat(offset.parents('.field').children('input.price').val());
		
		var price_offset = (price-new_subvention_price);
		if(price_offset!=0)
		{
			var label = ' (';
			if(price_offset>=0)
			{
				label += '+';
			}
			label += price_offset+' €)'
			offset.text(label);
		}
		else
		{
			offset.text('');
		}
	}
}

function updateRepasOffsets(new_repas_price)
{
	var offsets = $('#repas .offset')
	for(var i=0 ; i<offsets.length ; i++)
	{
		var offset = offsets.eq(i);
		var price = parseFloat(offset.parents('.field').children('input.price').val());
		
		var price_offset = (price-new_repas_price);
		if(price_offset!=0)
		{
			var label = ' (';
			if(price_offset>=0)
			{
				label += '+';
			}
			label += price_offset+' €)'
			offset.text(label);
		}
		else
		{
			offset.text('');
		}
	}
}

function updateForfaitsOffsets(new_forfait_price)
{
	var offsets = $('#forfaits .offset')
	for(var i=0 ; i<offsets.length ; i++)
	{
		var offset = offsets.eq(i);
		var price = parseFloat(offset.parents('.field').children('input.price').val());
		
		var price_offset = (price-new_forfait_price);
		if(price_offset!=0)
		{
			var label = ' (';
			if(price_offset>=0)
			{
				label += '+';
			}
			label += price_offset+' €)'
			offset.text(label);
		}
		else
		{
			offset.text('');
		}
	}
}

function handleOptionChange()
{
	var new_rental_option = $('.column input[name="location"]:checked');
	var hasChanged = (new_rental_option[0]!=old_rental_option[0]);
	var has_subvention_changed = false;
	var new_subvention_option = $('.subvention input:checked');
	var new_subvention_price = null;

	var new_forfait_option = $('#forfaits input:checked');
	var new_forfait_price = null;
	var new_repas_option = $('#repas input:checked');
	var new_repas_price = null;
	
	if(has_subvention)
	{
		has_subvention_changed = (new_subvention_option[0]!=old_subvention_option[0]);
		hasChanged |= has_subvention_changed;
	}
	if(is_reveillon)
	{
		hasChanged |= (new_subvention_option[0]!=old_subvention_option[0]);
		new_forfait_price = parseFloat(new_forfait_option.parent().children('input.price').val());
		new_repas_price = parseFloat(new_repas_option.parent().children('input.price').val());

		hasChanged |= (new_forfait_option[0]!=old_forfait_option[0]);
		hasChanged |= (new_repas_option[0]!=old_repas_option[0]);
	}
	if(new_rental_option.val()=='no')
	{
		$('.column table').hide();
		currentTable = null;
		$('#rentalType input:radio').removeAttr('checked');
	}
	if(hasChanged)
	{
		var new_rental_price = parseFloat(new_rental_option.parent().children('input.price').val());
		var price = parseFloat($('#global_price').val()) - old_rental_price + new_rental_price;
		if(has_subvention)
		{
			new_subvention_price = parseFloat(new_subvention_option.parent().children('input.price').val());
			price += - old_subvention_price + new_subvention_price;
		}
		if(is_reveillon)
		{
			price += - old_forfait_price + new_forfait_price;
			price += - old_repas_price + new_repas_price;
		}
		var newLabel = price+" €";
		if(has_subvention && new_subvention_price!=0)
		{
			newLabel += " + "+(-new_subvention_price)+" € (non encaissé)";
		}
		$('#global_price_display').text(newLabel);
		
		updateSubventionOffsets(new_subvention_price);
		if(is_reveillon)
		{
			updateForfaitsOffsets(new_forfait_price);
			updateRepasOffsets(new_repas_price);
		}
		
		$('#global_price_display').addClass('modified');
		$('#saveButton').show();
	}
	else
	{
		$('#global_price_display').text(old_label);
		$('#global_price_display').removeClass('modified');
		$('#saveButton').hide();

		updateSubventionOffsets(old_subvention_price);
		if(is_reveillon)
		{
			updateForfaitsOffsets(old_forfait_price);
			updateRepasOffsets(old_repas_price);
		}
	}
	
	
}

function handleCancelBoxReception(data)
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

function showCancelReservationBox(path)
{
	var settings = {
		async:true,
		dataType: 'json',
		type:'get'
	}
	$.ajax(serverUrl+path,settings).done(function(data){
		handleCancelBoxReception(data);
	}).fail(handleFailure);
}

function showHowToPayBox()
{
	var box = $('<div class="alert_box_wrapper" id="howToPayBox" style="display:none;">\
	<div class="alert_box">\
		<div class="title">Comment faire pour payer ?</div>\
		<div class="content_wrapper">\
			<div class="content">\
				<p>Tu peux nous donner tes chèques le midi au BôB si tu es à l\'X, nous les déposer dans la boîte aux lettres des JSP à la Kès, ou nous les envoyer par courrier (pense à écrire ton nom au dos) à l\'adresse :</p>\
				<div class="address">\
					Binet JSP<br/>\
					Kès des élèves<br/>\
					Ecole Polytechnique<br/>\
					Route de Saclay<br/>\
					91128 Palaiseau Cedex\
				</div>\
				<input class="primaryButton" type="submit" value="Fermer" onclick="hideAlertBox();"/>\
			</div>\
		</div>\
	</div>\
</div>');
	
	box.fadeTo(0,0);
	
	$('body').append(box);
	
	box.fadeTo(fadeDuration,1);
}

function changeRentalType()
{
	var input = $("#rentalType input:checked");
	var newTable = $('#'+input.val());
	if(currentTable == null || (currentTable[0] != newTable[0]))
	{
		if(currentTable)
		{
			if(currentTable[0] == $('.column table')[0])
			{
				newTable.css('margin-top',-currentTable.height());
			}
			else
			{
				currentTable.css('margin-top',-newTable.height());
			}
		}
		newTable.show();
		if(currentTable)
		{
			currentTable.fadeTo(0,0);
		}
		newTable.fadeTo(0,1);

		newTable.find('input:radio').eq(0).attr('checked','checked');
		
		currentTable = newTable;
		
		handleOptionChange();
	}
}

$(document).ready(function(){
	$('nav .current').removeClass('current');
	var button = $('#'+$('#eventButton').val());
	button.addClass('current');
	
	old_label = $('#global_price_display').text();
	
	old_rental_option = $('.column input[name="location"]:checked');
	old_rental_price = parseFloat(old_rental_option.parent().children('input.price').val());

	old_subvention_option = $('.subvention input:checked');
	has_subvention = ($('.subvention').length>0);
	old_subvention_price = parseFloat(old_subvention_option.parent().children('input.price').val());
	
	$('#rentalType input').change(changeRentalType);
	
	currentTable = $('.column table:visible');
	if(currentTable.length==0)
	{
		currentTable = null;
	}
	
	is_reveillon = ($('#eventButton').val() == 'buttonEvent1');
	
	if(is_reveillon)
	{
		old_forfait_option = $('#forfaits input:checked');
		old_forfait_price = parseFloat(old_forfait_option.parent().children('input.price').val());

		old_repas_option = $('#repas input:checked');
		old_repas_price = parseFloat(old_repas_option.parent().children('input.price').val());
	}
	
	$('form input[name!="rentalType"]').change(handleOptionChange);
	$('form').unbind('submit');
	$('form').submit(handleFormSubmit);
})