var startDate;

function correctWindowSize()
{
	var w = $(window);
	var height = w.height();
	var width = w.width();
	var image_height = height - 160;
	var image_ratio = 4976./2800.;
	var min_image_width = image_height*image_ratio;
	var image_width = Math.max(min_image_width,width);
	var min_image_height = (image_width/image_ratio)*3./5;
	$('body').css('min-width',min_image_width);
	$('body').css('min-height',min_image_height+160);
}

$(window).resize(correctWindowSize);

function updateCounters()
{
	var date = new Date();
    
    var timeLeft = startDate.getTime() - date.getTime();
	
	if(timeLeft<=0)
	{
		window.location.reload(true);
	}
    
    var nbSecondes = Math.floor(timeLeft/1000);
    var nbMinutes = Math.floor(nbSecondes/60);
    var nbHeures = Math.floor(nbMinutes/60);
    var nbJours = Math.floor(nbHeures/24);
    nbHeures %= 24;
    nbMinutes %= 60;
    nbSecondes %= 60;
    
    jours.setValue(nbJours);
    heures.setValue(nbHeures);
    minutes.setValue(nbMinutes);
    secondes.setValue(nbSecondes);
}

$(document).ready(function(){
	var timestamp = $('#countdownContent #startDate').val();
	var now = $('#countdownContent #now').val();

	$('nav .current').removeClass('current');
	var button = $('#'+$('#eventButton').val());
	button.addClass('current');
	
	if($('.jours'))
	
	// Initialize a new counter
    jours = new flipCounter('jours', {value:99, inc:0, pace:600, auto:false});
    heures = new flipCounter('heures', {value:99, inc:0, pace:600, auto:false});
    minutes = new flipCounter('minutes', {value:99, inc:0, pace:600, auto:false});
    secondes = new flipCounter('secondes', {value:99, inc:0, pace:600, auto:false});
    
	if(timestamp && timestamp!='INF')
	{
		startDate = new Date(timestamp*1000);
		if(now && now!='INF')
		{
			var now = new Date(now*1000);
			var offset = now.getTime()-(new Date()).getTime();
			startDate = new Date(startDate.getTime()-offset);;
		}
	    
		setInterval(updateCounters,1000);
	    
	    updateCounters();
	}
});

$(document).ready(function(){
	$('nav .current').removeClass('current');
});