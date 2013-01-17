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

var zoom_duration = 2000;
var zoomed_event = null;
var clicked_event = null;
var zoom_pourcentage = 0.75;

function handleBackgroundClick(evt)
{
	if(!zoomed_event)
	{
		if(evt)
		{
			evt.stopPropagation();
		}
		return;
	}
	var test = true;
	if(evt)
	{
		var elem = $(evt.target);
		test = zoomed_event && (elem[0]==$('#background')[0] || elem[0] == $('#events_wrapper')[0] || elem[0] == $(window)[0] || elem[0] == $('#events')[0]);
	}
	if(test)
	{

		$(window).unbind('resize');
		
		var event = zoomed_event;
		
		var arrows = event.children('.arrow');
		var arrow_inscription = event.children('.arrow.inscription');
		var arrow_back = event.children('.arrow.back');
		
		event.click(handleEventClick);
		$('#background').removeClass('in_zoom');
		$('#events_wrapper').removeClass('in_zoom');
		event.removeClass('in_zoom');
		var content = zoomed_event.children('.content');
		var details = event.children('.details');
		details.fadeTo(zoom_duration/2,0);
		content.delay(zoom_duration/2).fadeTo(zoom_duration/2,1);
		$('canvas').fadeTo(zoom_duration,1);
		arrows.fadeTo(zoom_duration/2,0);

		arrows.unbind('click');
		
		$('body').zoomTo({targetsize:1, duration:zoom_duration, animationendcallback:function(){
			event.removeClass('in_zoom');
			details.remove();
			arrows.css('display','none')
		}});
		
		
		
		zoomed_event = null;
		
		if(evt)
		{
			evt.stopPropagation();
		}
	}
}

function goToInscriptionPage(evt)
{
	if(zoomed_event)
	{
		evt.stopPropagation();
		var event = zoomed_event;
		var path = event.children('.path').val();
		handleBackgroundClick(null);
		setTimeout(function(){goToPage(path)},zoom_duration/2);
	}
}

function handleEventDetailsSuccess(json)
{
	if(clicked_event)
	{
		var event = clicked_event;
		
		var arrows = event.children('.arrow');
		var arrow_inscription = event.children('.arrow.inscription');
		var arrow_back = event.children('.arrow.back');
		
		prepareArrow(arrows);
		
		arrows.css('display','block');
		arrows.fadeTo(0,0);
		
		var details = $(json['details']);
		details.fadeTo(0,0);
		
		var w = $(window);
		var height = w.height();
		var width = w.width();
		var ratio = width/height;
		
		var final_width = zoom_pourcentage*width;
		var box_width = event.width();
		
		var box_height = box_width*height/final_width;
		var top_margin = -box_height/2;
		var box_height_scaled = 4*box_height;
		
		if($.browser.mozilla)
		{
			top_margin += 0.5;
			box_height_scaled += 12;
		}

		details.css('height',box_height_scaled);
		details.css('margin-top',top_margin);
		
		event.append(details);
		
		event.addClass('in_zoom');
		$('#background').addClass('in_zoom');
		$('#events_wrapper').addClass('in_zoom');
		var content = event.children('.content');
		$('canvas').fadeTo(0,0);
		content.fadeTo(zoom_duration/2,0);
		details.delay(zoom_duration/2).fadeTo(zoom_duration/2,1);
		arrows.fadeTo(zoom_duration,1);
		arrow_inscription.click(goToInscriptionPage);
		arrow_back.click(function(evt){handleBackgroundClick(null);evt.stopPropagation();});
		
		event.zoomTo({targetsize:zoom_pourcentage, scalemode:"width", nativeanimation:false, duration:zoom_duration});
		zoomed_event = event;

		$(window).resize(handleBackgroundClick);
		event.unbind('click');
		
		clicked_event = null
	}
	
}

function handleEventDetailsFailure(event,json)
{
	clicked_event = null;
	alert('failure');
}

function handleEventClick(evt)
{
	var event = $(this);
	
	clicked_event = event;
	
	var path = event.children('input.path').val();
	
	var url = serverUrl+path;
	var settings = {
		async:true,
		dataType: 'json',
		data:{
			getDetails:true
		}
	}
	$.ajax(url,settings).done(function(data){handleEventDetailsSuccess(data);}).fail(function(evt,data){handleEventDetailsFailure(data);});

	evt.stopPropagation();
}

function prepareArrow(arrow)
{
	var event = $('.event').eq(0);
	var event_width = event.width();
	var width = 2*event_width*(1-zoom_pourcentage)/(2*zoom_pourcentage);
	arrow.css('width',width);
	var img_ratio = 1./2;
	var height = 0.4*width/img_ratio;
	arrow.css('height',height);
	arrow.css('margin-top',-height/4);
}

function handleStateChangeBis()
{
	handleBackgroundClick(null);
	handleStateChange();
}

$(document).ready(function(){
	if($('nav .current') != $('#buttonHomePage'))
	{
		$('nav .current').removeClass('current');
		$('#buttonHomePage').addClass('current');
	}
	$('.event').click(handleEventClick);
	$('#background').click(handleBackgroundClick);
	$('#events_wrapper').click(handleBackgroundClick);
	$('#events').click(handleBackgroundClick);
	
	$(window).unbind('statechange');
	$(window).bind('statechange',handleStateChangeBis);
});