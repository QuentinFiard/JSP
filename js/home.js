/*
 * Layout
 */

function correctWindowSize()
{
	var w = $(window);
	var height = w.height();
	var width = w.width();
	var image_height = height - 160;
	var image_ratio = 3000./1874.;
	var min_image_width = image_height*image_ratio;
	var image_width = Math.max(min_image_width,width);
	var min_image_height = (image_width/image_ratio)*3./5;
	$('body').css('min-width',min_image_width);
	$('body').css('min-height',min_image_height+160);
}

$(window).resize(correctWindowSize);

$(document).ready(function(){
	correctWindowSize();
})

/*
 * Controller
 */

//$().framerate({framerate: 30})

var mouseDownOnHomeLabel = false;

function prepareHomeLabelAnimation()
{
	var label = $('.home_label');
	label.mousedown(function(){
		mouseDownOnHomeLabel = true;
	}).mouseup(function(){
		if(mouseDownOnHomeLabel)
		{
			mouseDownOnHomeLabel = false;
			var res = true;
			if(label.attr('onclick'))
			{
				res = eval(label.attr('onclick'));
			}
			if(res && label.attr('href'))
			{
				window.location.href = label.attr('href');
			}
		}
	}).mouseleave(function(){
		mouseDownOnHomeLabel = false;
	})
}

function wrongPasswordLoginFormAnimation()
{
	var login = $("#login_form");
	
	var offset = 15;
	var duration = 40;
	var nb = 3;
	
	if(!$(login).is(':animated'))
	{

		login.animate({right:'+='+offset},{
			duration:duration,
			queue:true,
		});
		
		for(var i=0 ; i<nb-1 ; i++)
		{
			login.animate({right:'-='+2*offset},{
				duration:2*duration,
				queue:true,
			});
			login.animate({right:'+='+2*offset},{
				duration:2*duration,
				queue:true,
			});
		}

		login.animate({right:'-='+offset},{
			duration:duration,
			queue:true,
		});
		
	}
}

function handleExternalLoginSuccess(data)
{
	if(!data['success'])
	{
		var form = $("#login_form form");
		if(data['wrong_email_format'])
		{
			form.find('#mail').val('');
		}
		form.find('#password').val('');

		wrongPasswordLoginFormAnimation();
	}
	else
	{
		window.location.href=serverUrl;
	}
}

function handleExternalLoginFailure(data)
{
	alert('Impossible de communiquer avec le serveur, vérifier votre connection internet.')
}

function removePassword(tab)
{
	for(var i=0 ; i<tab.length ; i++)
	{
		if(tab[i]['name']='password')
		{
			tab.splice(i,1);
			i=i-1;
		}
	}
	return tab;
}

function submitExternalLoginForm(event)
{
	var form = $(event.target);

	var data_raw = form.serializeArray();
	
	var data = new Object();
	
	for(var i=0 ; i<data_raw.length ; i++)
	{
		data[data_raw[i]['name']] = data_raw[i]['value'];
	}
	
	if (typeof CryptoJS != 'undefined')
	{
		form.find('.sha').val('true');
		var password = form.find('#password');
		data['digest'] = ""+CryptoJS.SHA256(password.val());
		data['sha'] = 'true';
		delete data['password'];
	}
	
	var url = form.attr('action');
	var settings = {
		async:true,
		dataType:'json',
		data:data,
		type:'post',
	}
	$.ajax(url,settings).done(function(data){handleExternalLoginSuccess(data);}).fail(function(data){handleExternalLoginFailure(data);});

	return false;
}

$(document).ready(function(){
	prepareLoginForm();
	prepareHomeLabelAnimation();
	if($('nav .current') != $('#buttonHomePage'))
	{
		$('nav .current').removeClass('current');
		$('#buttonHomePage').addClass('current');
	}
	$('#login_form form').submit(submitExternalLoginForm);
});