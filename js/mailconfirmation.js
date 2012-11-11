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
	var box = $('.alert_box_wrapper');
	box.detach();
	$('body').append(box);
	box.css('display','block');
	correctWindowSize();
	prepareLoginForm();
})