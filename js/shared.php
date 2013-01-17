<?php header('Content-type:application/javascript'); ?>
<?php 
require_once('../classes/utilities/Server.php');
use \utilities\Server;
?>
/*
 * Utilities
 */

var serverUrl = "<?php 
	$url = $_SERVER["REQUEST_URI"];
	$matches = array();
	if(preg_match("|^(.*?)/js/|",$url,$matches))
	{
		$url = $matches[1];
	}
	echo $url;
?>";

var serverFullUrl = "<?php echo 'http://'.$_SERVER['HTTP_HOST'];?>"+serverUrl;

function executeScripts(scripts)
{
	scripts.each(function(){
		eval($(this).html());
	})
}

/*
 * Animations
 * 
 */



var testContent;
var currentContent;

var slideDuration = 1000;
var fadeDuration = 1000;

function insertRightAndAnimate(newContent)
{
	var contentWidth = $('body').width();
	
	var contentWrapper = $('#contentWrapper');
	contentWrapper.append(newContent);
	
	newContent.css('left',contentWidth);
	
	contentWrapper.animate({'left':-contentWidth},slideDuration,function(){
		currentContent.remove();
		newContent.css('left',0);
		contentWrapper.css('left',0);
		currentContent = newContent;
	});
}

function insertLeftAndAnimate(newContent)
{
	var contentWidth = $('body').width();
	
	var contentWrapper = $('#contentWrapper');
	contentWrapper.append(newContent);
	newContent.css('left',-contentWidth);
	
	contentWrapper.animate({'left':contentWidth},slideDuration,function(){
		currentContent.remove();
		newContent.css('left',0);
		contentWrapper.css('left',0);
		currentContent = newContent;
	});
}

function fadeOutToWhiteThenFadeIn(newContent)
{
	var contentWidth = $('body').width();
	newContent.css('opacity',0);
	
	var first=true;

	var scripts = newContent.find('script');
	
	$('#contentWrapper').append(newContent);
	
	currentContent.animate({opacity:0},fadeDuration,'easeInCirc',function(){
		newContent.animate({opacity:1},fadeDuration,'easeOutCirc',function(){
			if(first)
			{
				currentContent.remove();
				currentContent=newContent;
			}
			first=false;
		});
	})
}

function fadeOutFadeIn(newContent)
{
	var contentWidth = $('body').width();
	newContent.fadeTo(0,0);
	
	var first=true;
	
	$('#contentWrapper').append(newContent);
	
	currentContent.animate({opacity:0},fadeDuration,'linear');
	newContent.animate({opacity:1},fadeDuration,'linear',function(){
		if(first)
		{
			currentContent.remove();
			currentContent=newContent;
		}
		first=false;
	});
}

var i=0;
var modulo = 4;

function testAnimation()
{
	var newContent = testContent.clone();
	switch(i)
	{
	case 0:
		insertRightAndAnimate(newContent);
		break;
	case 1:
		insertLeftAndAnimate(newContent);
		break;
	case 2:
		fadeOutToWhiteThenFadeIn(newContent);
		break;
	case 3:
		fadeOutFadeIn(newContent);
		break;
	}
	i = (i+1)%modulo;
}

function animateLapin()
{
	var lapin = $('#lapin');
	lapin.animate()
}

/*
 * Login
 */

function frankizLogout(path)
{
	var img = $('<img style="visibility:hidden;"/>');
	img.error(function(){
		window.location.href = path;
	}).load(function(){
		window.location.href = path;
	}).attr('src',"https://www.frankiz.net/exit");
}

/*
 * Navigation
 */

var lastState = null;
var isPushingState = false;

function removeOldCounter()
{
	var counter = currentContent.find("#compteurs");
	if(counter.length > 0)
	{
		counter.remove();
	}
}

function handlePageChangeSuccess(path,json,shouldPush)
{
	removeOldCounter();
	
	if(typeof(shouldPush)==='undefined') shouldPush = true;
	
	if('css' in json)
	{
		$('head').append('<link rel="stylesheet" href="'+json['css']+'" type="text/css" />');
	}
	if('js' in json)
	{
		$.getScript(json['js']);
	}
	
	var newContent = $(json['content']);
	
	currentContent.find('#eventButton').remove();
	
	var transition = 'fadeOutFadeIn';
	if('transition' in json)
	{
		transition = json['transition'];
	}
	eval(transition)(newContent);
	
	windowResizeHandler();
	prepareLoginForm();
	$('form').unbind('submit');
	$('form').submit(handleFormSubmit);
	
	if(shouldPush)
	{
		isPushingState = true;
		History.pushState(null,json['title'],serverUrl + json['path']);
		isPushingState = false;
	}
}

function handleStateChange()
{
	var state = History.getState();
	if(!isPushingState && state != lastState)
	{
		var settings = {
			async:true,
			dataType: 'json',
			data:{lastPath:pathFromState(lastState)}
		}
		$.ajax(state.url,settings).done(function(data){handlePageChangeSuccess(state.path,data,false);}).fail(function(data){handlePageChangeFailure(state.path,data);}).always(function(){
			lastState = state;
		});
	}
	else
	{
		lastState = state;
	}
}

function handlePageChangeFailure(json)
{
	
}

function goToPage(path,back)
{
	var lastPath = pathFromState(History.getState());
	if(path!=lastPath)
	{
		if(typeof(back)==='undefined') back = false;
		
		var url = serverUrl+path;
		var settings = {
			async:true,
			dataType: 'json',
			data:{lastPath:lastPath}
		}
		$.ajax(url,settings).done(function(data){handlePageChangeSuccess(path,data);}).fail(function(data){handlePageChangeFailure(path,data);});
	}
}

function pathFromState(state)
{
	var path = state.hash;
	var re = new RegExp("^"+serverUrl+"([^\?]*)\?.*$");
	var matches = re.exec(path);
	return matches[1];
}

/*
 * Login form
 */



function showLoginBox()
{
	$('#external_login').unbind('click');
	$('#external_login').click(hideLoginBox);
	var box = $('#login_form');
	box.animate({right:"15px"},300,function(){
		$('#username').focus();
	})
}

function hideLoginBox()
{
	$('#external_login').unbind('click');
	$('#external_login').click(showLoginBox);
	var box = $('#login_form');
	var width = box.width();
	box.animate({right:(-width-10)+"px"},300);
}

function prepareLoginForm()
{
	var box = $('#login_form');
	var width = box.width();
	box.css('right',-width);

	$('#external_login').click(showLoginBox);
	$('#login_form .warningButton').click(hideLoginBox);
}

/*
 * Nav
 */

function handleNavButtonClick(button,pagePath)
{
	if(!$(button).hasClass('current'))
	{
		$('nav .current').removeClass('current');
		$(button).addClass('current');
		goToPage(pagePath);
	}
}

function handlePasswordBoxReception(json)
{
	if('css' in json)
	{
		$('head').append('<link rel="stylesheet" href="'+json['css']+'" type="text/css" />');
	}
	if('js' in json)
	{
		$.getScript(json['js']);
	}
	var box = $(json['content']);
	
	box.fadeTo(0,0);
	
	$('body').append(box);
	
	box.fadeTo(fadeDuration,1);
}

var wasCanceled = false;

function handleFailure(data)
{
	if(wasCanceled)
	{
		wasCanceled = false;
		return;
	}
	alert('Impossible de communiquer avec le serveur. Vérifiez votre connection internet ou réessayez plus tard.');
}

function showForgottenPasswordBox()
{
	var url = serverUrl+'/connexion/resetpassword';
	var settings = {
		async:true,
		dataType:'json'
	}
	$.ajax(url,settings).done(function(data){handlePasswordBoxReception(data);}).fail(function(data){handlePasswordBoxFailure();});
}



function hideAlertBox()
{
	var box = $('.alert_box_wrapper');
	box.fadeTo(fadeDuration,0,function(){
		box.remove();
	});
}

/*
 * Placeholders
 */

//This adds 'placeholder' to the items listed in the jQuery .support object. 
jQuery(function() {
   jQuery.support.placeholder = false;
   test = document.createElement('input');
   if('placeholder' in test) jQuery.support.placeholder = true;
});
// This adds placeholder support to browsers that wouldn't otherwise support it. 
$(function() {
   if(!$.support.placeholder) { 
      var active = document.activeElement;
      $(':text').focus(function () {
         if ($(this).attr('placeholder') != '' && $(this).val() == $(this).attr('placeholder')) {
            $(this).val('').removeClass('hasPlaceholder');
         }
      }).blur(function () {
         if ($(this).attr('placeholder') != '' && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))) {
            $(this).val($(this).attr('placeholder')).addClass('hasPlaceholder');
         }
      });
      $(':text').blur();
      $(active).focus();
      $('form:eq(0)').submit(function () {
         $(':text.hasPlaceholder').val('');
      });
   }
});

function handleFormSubmit(event)
{
	var form = $(event.target);
	return false;
}

var isMiniGameDisplay = false;

function toggleMiniGame()
{
	if(isMiniGameDisplay)
	{
		$('canvas').css('display','block');
		$('canvas').fadeTo(fadeDuration,1);
		$('#game_wrapper').fadeTo(fadeDuration,0,function(){
			$('#game_wrapper').remove();
			isMiniGameDisplay = false;
		});
	}
	else
	{
		$('#sound')[0].play()
		var wrapper = $('<div id="game_wrapper"><div id="game"><embed src="<?php echo Server::getServerRoot(); ?>swf/solipskier.swf" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></div></div>');
		wrapper.fadeTo(0,0);
		$('body').append(wrapper);
		wrapper.fadeTo(fadeDuration,1);
		$('canvas').fadeTo(fadeDuration,0,function(){
			$('canvas').css('display','none');
		});
		isMiniGameDisplay = true;
	}
}

function handleRabbitClick()
{
	toggleMiniGame();
}

function redirectToRoot()
{
	window.location.href=serverFullUrl;
}

function formToData(form)
{
	var data_raw = form.serializeArray();
	
	var data = new Object();
	
	for(var i=0 ; i<data_raw.length ; i++)
	{
		data[data_raw[i]['name']] = data_raw[i]['value'];
	}
	
	if (typeof CryptoJS != 'undefined')
	{
		form.find('.sha').val('true');
		data['digest'] = ""+CryptoJS.SHA256(data['password']);
		data['sha'] = 'true';
		delete data['password'];
	}
	
	return data;
}

function createAccountFormToData(form)
{
	var data = formToData(form);
	
	if(data['sha']=='true')
	{
		delete data['passwordConfirm'];
	}
	
	return data;
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
	currentContent = $('#contentWrapper > .content');
	lastState = History.getState();
	$(window).bind('statechange',handleStateChange);
	$('form').unbind('submit');
	$('form').submit(handleFormSubmit);
	$('#login_form form').unbind('submit');
	$('#login_form form').submit(submitExternalLoginForm);
	$('#lapin').click(handleRabbitClick);
});