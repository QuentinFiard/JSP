/*
 * Layout
 */
/*
function correctWindowSize()
{
	var body = $('body');
	var background = $('#background');
	var height = body.height();
	var width = body.width();
	var image_height = height - 160;
	var image_ratio_max = 3072./1930.;
	var image_ratio_min = 2472./1930.;
	
	var space_ratio = width/image_height;
	
	var min_image_width = image_height*image_ratio_min;
	var image_width = image_ratio_max*image_height;
	
	if(space_ratio<image_ratio_min)
	{
		body.css('min-width',min_image_width);
		width = min_image_width;
	}
	else
	{
		body.css('min-width','none');
	}
	
	if(space_ratio < image_ratio_max)
	{
		var marginleft = (image_width-width)/2;
		background.css('margin-left',-marginleft);
	}
	else
	{
		background.css('margin','0 auto');
	}
}

$(window).resize(correctWindowSize);

$(document).ready(function(){
	correctWindowSize();
})*/

$(document).ready(function(){
	if($('nav .current') != $('#buttonAdmin'))
	{
		$('nav .current').removeClass('current');
		$('#buttonAdmin').addClass('current');
	}
});