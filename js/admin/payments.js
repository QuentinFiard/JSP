function updateUsers(data)
{
	var table = $('#users');
	if(table.length==0)
	{
		table = $('<table id="users"></table>');
		$('#mainSection').append(table);
	}
	table.html('');
	var header = $('<tr></tr>');
	var keys = data['keys'];
	var th;
	for(var i=0 ; i<keys.length ; i++)
	{
		th = $('<th></th>');
		th.text(keys[i]);
		header.append(th);
	}
	th = $('<th>Prix à payer</th>');
	header.append(th);
	th = $('<th>Caution à payer</th>');
	header.append(th);
	th = $('<th>Confirmer prix</th>');
	header.append(th);
	th = $('<th>Confirmer caution</th>');
	header.append(th);
	th = $('<th>Confirmer paiement</th>');
	header.append(th);
	
	table.append(header);
	
	var form = $('#filters');
	var path = form.attr('action');

	var users = data['users'];
	var price = data['price'];
	var caution = data['caution'];
	var hasPaid = data['hasPaid'];
	
	for(var i=0 ; i<users.length ; i++)
	{
		if(hasPaid[i])
		{
			var tr = $('<tr></tr>');
			var td;
			for(var j=0 ; j<keys.length ; j++)
			{
				td = $('<td></td>');
				td.text(users[i][j]);
				tr.append(td);
			}
			td = $('<td></td>');
			td.text(price[i]+"€");
			tr.append(td);
			td = $('<td></td>');
			td.text(caution[i]+"€");
			tr.append(td);
			td = $('<td>Payé</td>');
			tr.append(td);
			td = $('<td>Payé</td>');
			tr.append(td);
			td = $('<td>Payé</td>');
			tr.append(td);
			
			table.append(tr);
		}
		else
		{
			var tr = $('<tr></tr>');
			
			var hidden = $('<input type="hidden" name="confirmPayment" value="true"/>');
			tr.append(hidden);
			hidden = $('<input type="hidden" name="eventId"/>');
			hidden.val($('#filters select').val());
			tr.append(hidden);
			
			var userId = null;
			
			for(var j=0 ; j<keys.length ; j++)
			{
				if(keys[j]=='userId')
				{
					userId = users[i][j];
				}
				var td = $('<td></td>');
				td.text(users[i][j]);
				tr.append(td);
			}
			hidden = $('<input type="hidden" name="userId"/>');
			hidden.val(userId);
			tr.append(hidden);
			

			td = $('<td></td>');
			td.text(price[i]+"€");
			tr.append(td);
			hidden = $('<input type="hidden" name="price"/>');
			hidden.val(price[i]);
			tr.append(hidden);
			td = $('<td></td>');
			td.text(caution[i]+"€");
			tr.append(td);
			hidden = $('<input type="hidden" name="caution"/>');
			hidden.val(caution[i]);
			tr.append(hidden);
			td = $('<td class="notpaid"><input type="checkbox" name="confirmPrice" required="required" value="true" /></td>');
			tr.append(td);
			td = $('<td class="notpaid"><input type="checkbox" name="confirmCaution" required="required" value="true" /></td>');
			tr.append(td);
			td = $('<td class="notpaid"><input type="submit" value="Paiement reçu" onclick="submitPaymentConfirm(this);" /></td>');
			tr.append(td);
			
			table.append(tr);
		}
	}
}

function getUsers(evt)
{
	var form = $('#filters');
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
			updateUsers(data);
		}
		else
		{
			alert('Failed');
		}
	}).fail(handleFailure);
	if(evt)
	{
		evt.stopPropagation();
	}
}

function submitPaymentConfirm(button)
{
	button = $(button);
	var row = button.parents('tr');
	
	var form = $('#filters');
	var path = form.attr('action');
	
	var tmp = $('<form></form>')
	var inputs = row.find('input');
	for(var i=0 ; i<inputs.length ; i++)
	{
		var orig = inputs.eq(i);
		
		if(orig.attr('type')!='checkbox' || orig.attr('checked'))
		{
			var input = $('<input type="hidden" />');
			input.attr('name',inputs.eq(i).attr('name'));
			input.attr('value',inputs.eq(i).attr('value'));
			tmp.append(input);
		}
	}
	
	var data = tmp.serialize();
	var settings = {
		async:true,
		dataType: 'json',
		type:'post',
		data:data
	}
	$.ajax(serverUrl+path,settings).done(function(data){
		if(data['success'])
		{
			var notpaid = row.find('.notpaid');
			notpaid.removeClass('notpaid');
			notpaid.html('Payé');
		}
		else
		{
			if(data['confirm_price_required'])
			{
				alert("Veuillez confirmer que les montants payés sont corrects.");
			}
			else if(data['no_such_event'])
			{
				alert("L'évênement choisi n'existe pas.");
			}
			else if(data['no_such_user'])
			{
				alert("Cet utilisateur n'existe plus.");
			}
			else if(data['no_reservation'])
			{
				alert("Cet utilisateur n'a pas de réservation pour cet évênement.");
			}
			else if(data['invalid_price'])
			{
				alert("Le prix a changé depuis la confirmation : il doit maintenant payer "+data['price']+"€ pour cet évênement, et une caution de "+data['caution']+"€.");
			}
			else if(data['already_paid'])
			{
				alert("Cet utilisateur a déjà payé pour l'évênement.");
			}
			else
			{
				alert("Une erreur inconnue s'est produite.");
			}
		}
	}).fail(handleFailure);
}

$(document).ready(function(){
	$('#filters select').change(getUsers);
	$('#filters input').keyup(getUsers);
	
	getUsers();
})