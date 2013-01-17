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

$(document).ready(function(){
	prepareLoginForm();
	prepareHomeLabelAnimation();
	if($('nav .current') != $('#buttonHomePage'))
	{
		$('nav .current').removeClass('current');
		$('#buttonHomePage').addClass('current');
	}
	$('#login_form form').unbind('submit');
	$('#login_form form').submit(submitExternalLoginForm);
});