<?php use structures\events\WeekendJSP;

use structures\events\SemaineReveillon;
header('Content-type:application/javascript; charset=utf-8'); ?>
<?php require_once '../jquery.js';
chdir('../../');?>

var users = [
<?php

require_once 'classes/structures/events/WeekendJSP.php';
$event = WeekendJSP::shared();
$users = $event->getUsers();
$filteredUsers = array();

$ignored = array(310,440,530,480,175,428,645,57,334,253,205,380,66,207,451,637,154,353,340,439,372,159,
325,
618,
376,
423,
479,
264,
449,
531,
190,
269,
454,
605,
588,
76,
575,
510,
462,
565,
362,
638,
192,
425,
617,
523,
434,
416,
348,
528,
388,
112,
324,
266,
55,
136,
229,
257,
259,
458,
707,
441,
546,
521,
359,
262,
456,
272,
609,
641,
63,
561,
73,
386,
298,
626,
497,
344,
606,
670,
368,
619,
164,
385,
96,
419,
403,
171,
398,
604,
328,
455,
477,
407,
72,
309,
193,
607,
509,
375,
379,
506,
311,
198,
473,
230,
110,
268,
356,
623,
381,
571,
587,
475,
508,
392,
128,
394,
393,
682,
450,
437,
335,
613,
79,
544,
210,
65,
444,
417,
566,
584,
487,
697,
245,
103,
288,
274,
191,
166,
314,
338,
84,
589,
302,
470,
452,
408,
374,
612,
157,
120,
201,
543,
153,
549,
100,
422,
545,
40,
378,
467,
307,
35,
303,
552,
59,
29,
222,
332,
350,
517,
579,
104,
139,
215,
82,
339,
287,
163,
602,
631,
525,
646,
460,
493,
45,
512,
354,
660,
124,
553,
650,
647,
526,
698,
632,
212,
346,
113,
85,
453,
652,
200,
373,
290,
653,
516,
349,
608,
127,
329,
284,
195,
504,
179,
271,
596,
123,
151,
341,
616,
551,
448,
320,
502,
577,
597,
484,
481,
567,
169,
129,
93,
134,
225,
405,
364,
518,
539,
92,
540,
279,
628,
568,
233,
696,
649,
624,
370,
427,
418,
554,
347,
615,
443,
390,
130,
395,
360,
255,
294,
496,
537,
285,
426,
559,
457,
232,
406,
206,
319,
304,
312,
160,
318,
556,
507,
708,
464,
472,
573,
301,
627,
54,
447,
515,
43,
383,
213,
142,
231,
297,
313,
442,
558,
221,
603,
431,
115,
236,
358,
542,
491,
595,
58,
436,
107,
61,
214,
88,
563,
654,
705,
599,
98,
488,
503,
276,
538,
105,
469,
424,
371,
345,
582,
70,
662,
369,
251,
547,
69,
610,
121,
574,
321,
672,
321,
672,
152,
265,
590,
234,
410,
293,
433,
256,
594,
527,
585,
342,
578,
75,
331,
270,
94,
357,
281,
459,
336,
505,
241,
280,
238,
305,
400,
170,
482,
317,
183,
679,
177,
246,
524,
658,
122,
421,
651,
87,
483,
674,
576,
420,
695,
572,
101,
343,
41,
315,
569,
661,
411,
263,
671,
562,
196,
224,
248,
635,
289,
396,
404,
351,
211,
611,
474,
116,
217,
247,
432,
71,
663,
489,
377,
430,
184,
519,
178,
490,
463,
86,
168,
250,
366,
550,
399,
286,
187,
292,
306,
235,
476,
446,
249,
118,
34,
495,
435,
384,
630,
209,
47,
361,
564,
389,
275,
409,
664,
485,
501,
471,
53,
438,
648,
657,
38,
111,
535,
391,
261,
445,
31,
514,
557,
522,
570,
655,
614,
644,
);

echo "/* ReservationId,Lastname,Firstname,Price \n";

foreach($users as $user)
{
	$reservation = $user->getReservationForEvent($event);
	if(!in_array($reservation['reservationId'], $ignored))
	{
		$filteredUsers[] = $user;
		echo $user->getReservationForEvent($event)['reservationId'].','.$user->getLastname().','.$user->getFirstname().','.$event->priceForUser($user)."\n";
	}
}


echo "\n*/\n";

$filteredUsers = array_reverse($filteredUsers);

foreach($filteredUsers as $user)
{
?>
{
	firstname:"<?php echo $user->getFirstname(); ?>",
	lastname:"<?php echo ucfirst($user->getLastname()); ?>",
	initials:"<?php echo strtoupper(substr($user->getFirstname(),0,1).substr($user->getLastname(),0,1));  ?>",
	birthday:[<?php 
		$startDate = mktime(0,0,0,1,1,1988);
		$endDate = mktime(23,59,0,12,31,1992);
		
		$birthDate = rand($startDate, $endDate);
		
		echo date('"j","m","Y"',$birthDate);
	?>],
	wasAdded:false,
	address:"Boulevard des Marechaux",
	houseNumber:'11',
	postalCode:'91120',
	city:'Palaiseau',
	homePhoneNumber:'0633563483',
	emailAddress:"<?php echo $user->getEmail(); ?>",
	options:[<?php 
		$options = $user->getOptionsForEvent($event);
		$first = true;
		foreach($options as $option)
		{
			if(!$first)
			{
				echo ',';
			}
			echo $option->getOptionId();
			$first = false;
		}
	?>],
	/*roomId:<?php 
		$room = $user->getRoomForEvent($event);
		if($room)
		{
			echo $room->getRoomId();
		}
		else
		{
			echo 'null';
		}
	?>*/
},
<?php
}
?>
];

var timeDelay = 1200;

var user = users.pop();

var lastParticipantsId = [];

var userPageRequested = false;
var addUserPageCompleted = false;
var configurePageCompleted = false;
var roomPageRequested = false;
var userRoomPageRequested = false;
var roomAttributionPageRequested = false;
var userAddedToRoom = false;
var oldParticipantCount=-1;
var resetRequest = false;

function updateData()
{
	data = new Object();
	data.userPageRequested = userPageRequested;
	data.addUserPageCompleted = addUserPageCompleted;
	data.configurePageCompleted = configurePageCompleted;
	data.roomPageRequested = roomPageRequested;
	data.userRoomPageRequested = userRoomPageRequested;
	data.roomAttributionPageRequested = roomAttributionPageRequested;
	data.userAddedToRoom = userAddedToRoom;
	data.oldParticipantCount = oldParticipantCount;
	data.resetRequest = resetRequest;
	data.users = users;
	data.user = user;
	window.name = JSON.stringify(data);
}

var data = null;
try
{
	data = JSON.parse(window.name);
	if(!data)
	{
		throw new Error();
	}
	userPageRequested = data.userPageRequested;
	addUserPageCompleted = data.addUserPageCompleted;
	configurePageCompleted = data.configurePageCompleted;
	roomPageRequested = data.roomPageRequested;
	userRoomPageRequested = data.userRoomPageRequested;
	roomAttributionPageRequested = data.roomAttributionPageRequested;
	userAddedToRoom = data.userAddedToRoom;
	oldParticipantCount = data.oldParticipantCount;
	resetRequest = data.resetRequest;
	if(data.users.length < users.length)
	{
		users = data.users;
		user = data.user;
	}
	updateData();
}
catch(e)
{
   	updateData();
}

String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};

String.prototype.ltrim=function(){return this.replace(/^\s+/,'');};

String.prototype.rtrim=function(){return this.replace(/\s+$/,'');};

String.prototype.fulltrim=function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');};

function isIntroduction()
{
	return (document.getElementById('searchForm_participants') != null);
}

function isAddUserPage()
{
	var bar = jQuery('#Coordonnées');
	return (bar.length>0);
}

function isConfigurePage()
{
	return (document.getElementById('Assurance annulation.container') != null);
}

function isRoomPage()
{
	return (document.getElementById('panel_rooms') != null) && !isRoomAttributionPage();
}

function isRoomAttributionPage()
{
	return (document.getElementById('participants') != null);
}

function goToAddUserPage()
{
	if(!userPageRequested)
	{
		var link = jQuery("a:contains('Ajouter')");
		link.click();
		userPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function inputForOption(optionId)
{
	var inputs = jQuery('input[type="radio"]')
	
	if(optionId==39)
	{
		return inputs.eq(17);
	}
	else if(optionId==40)
	{
		return inputs.eq(18);
	}
	else if(optionId==41)
	{
		return inputs.eq(19);
	}
	else if(optionId==42)
	{
		return inputs.eq(20);
	}
	else if(optionId==43)
	{
		return inputs.eq(21);
	}
	else if(optionId==44)
	{
		return inputs.eq(22);
	}
	else if(optionId==45)
	{
		return inputs.eq(27);
	}
	else if(optionId==46)
	{
		return inputs.eq(28);
	}
	else if(optionId==47)
	{
		return inputs.eq(29);
	}
	else if(optionId==48)
	{
		return inputs.eq(23);
	}
	else if(optionId==49)
	{
		return inputs.eq(24);
	}
	else if(optionId==50)
	{
		return inputs.eq(25);
	}
	else if(optionId==51)
	{
		return inputs.eq(26);
	}
	else if(optionId==52)
	{
		return inputs.eq(27);
	}
	else if(optionId==53)
	{
		return inputs.eq(28);
	}
	else if(optionId==54)
	{
		return inputs.eq(29);
	}
	return null;
}

var indexForRoom = {
	1	:	[	1	,	1	],
	2	:	[	1	,	2	],
	3	:	[	1	,	3	],
	4	:	[	1	,	4	],
	5	:	[	1	,	5	],
	6	:	[	1	,	6	],
	7	:	[	1	,	7	],
	8	:	[	1	,	8	],
	9	:	[	1	,	9	],
	10	:	[	1	,	10	],
	11	:	[	1	,	11	],
	12	:	[	1	,	12	],
	13	:	[	1	,	13	],
	14	:	[	1	,	14	],
	15	:	[	1	,	15	],
	16	:	[	2	,	1	],
	17	:	[	2	,	2	],
	18	:	[	2	,	3	],
	19	:	[	2	,	4	],
	20	:	[	2	,	5	],
	21	:	[	2	,	6	],
	22	:	[	2	,	7	],
	23	:	[	2	,	8	],
	24	:	[	2	,	9	],
	25	:	[	2	,	10	],
	26	:	[	2	,	11	],
	27	:	[	2	,	12	],
};

function fillAddUserPageWithUser(user)
{
	if(!addUserPageCompleted && !user['wasAdded'])
	{
		var input = jQuery('input[name="firstName"]');
		input.val(user['firstname']);
		
		input = jQuery('input[name="initials"]');
		input.val(user['initials']);
		
		input = jQuery('input[name="name"]');
		input.val(user['lastname']);
		
		input = jQuery('input[name="birthDate_day"]');
		input.val(user['birthday'][0]);
		input = jQuery('input[name="birthDate_month"]');
		input.val(user['birthday'][1]);
		input = jQuery('input[name="birthDate_year"]');
		input.val(user['birthday'][2]);
		
		input = jQuery('input[name="address"]');
		input.val(user['address']);
		input = jQuery('input[name="houseNumber"]');
		input.val(user['houseNumber']);
		input = jQuery('input[name="postalCode"]');
		input.val(user['postalCode']);
		input = jQuery('input[name="city"]');
		input.val(user['city']);
		
		input = jQuery('input[name="homePhoneNumber"]');
		input.val(user['homePhoneNumber']);
		
		input = jQuery('input[name="emailAddress"]');
		input.val(user['emailAddress']);
		
		user['wasAdded'] = true;
		
		var submit = jQuery(document.getElementById('participant.submit'));
		submit.click();
		addUserPageCompleted = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function endConfigurePage()
{
	var submit = jQuery(document.getElementById('options.submit'));
	submit.click();
	configurePageCompleted = true;
	updateData();
	
	setTimeout(initializeAutoFillIn,timeDelay);
}

function fillConfigurePage(user)
{
	if(!configurePageCompleted)
	{
		var inputs = jQuery('input[type="radio"]');
		inputs.eq(16).click(); // Pas de location
		
		var options = user['options'];
		for(var i=0 ; i < options.length ; i++)
		{
			var input = inputForOption(options[i]);
			if(input)
			{
				input.click();
			}
		}
		
		setTimeout(endConfigurePage,timeDelay);
	}
	else
	{
		setTimeout(initializeAutoFillIn,timeDelay);
	}
}

function saveUserId()
{
	var table = jQuery('#participants');
	
	var row = table.find('tr').eq(2);
	
	/*var userId = row.children('td').eq(1).text();
	
	if(lastParticipantsId.indexOf(userId)!=-1)
	{
		alert('User id already exists');
		return false;
	}
	
	user['userId'] = userId;*/
	user['textUserId'] = user['initials']+' '+user['lastname'];
	//lastParticipantsId.push(userId);
	
	return true;
}

function goToRoomPage()
{
	alert('Error');
	if(!roomPageRequested)
	{
		var link = jQuery("a:contains('chambres')");
		link.click();
		roomPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function goToUserRoomPage(user)
{
	if(!userRoomPageRequested)
	{
		var index = indexForRoom[user['roomId']];
		
		var page = index[0];
		var currentPage = jQuery('td.navigator b').text();
		if(currentPage != page)
		{
			var link = jQuery('.navigator td a');
			for(var i=0 ; i < link.length ; i++)
			{
				if(link.eq(i).text()==page)
				{
					link = link.eq(i);
					break;
				}
			}
			link.click();
		}
		userRoomPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function goToRoomAttributionPage(user)
{
	if(!roomAttributionPageRequested)
	{
		var index = indexForRoom[user['roomId']];
		var row = index[1];
		row = jQuery('#rooms_'+row);
		var link = row.find('a');
		link.click();
		roomAttributionPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function addUserToRoom(user)
{
	if(!userAddedToRoom)
	{
		var rows = jQuery(document.getElementById('availableParticipants.body')).find('tr');
		var row = null;
		for(var i=0 ; i < rows.length ; i++)
		{
			if(rows.eq(i).children('td').eq(0).text()==user['textUserId'])
			{
				row = rows.eq(i);
				break;
			}
		}
		if(row!=null)
		{
			oldParticipantCount = jQuery('#participants tr').length;
			row.find('a').click();
		}
		
		userAddedToRoom = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,timeDelay);
}

function confirmUserAddedToRoom()
{
	var rows = jQuery('#participants tr');
	
	return (rows.length != oldParticipantCount);
}

function resetForNextUser()
{
	if(!resetRequest)
	{
		var link = jQuery("a:contains('Sommaire')");
		link.click();
		
		userPageRequested = false;
		addUserPageCompleted = false;
		configurePageCompleted = false;
		roomPageRequested = false;
		userRoomPageRequested = false;
		roomAttributionPageRequested = false;
		userAddedToRoom = false;
		oldParticipantCount=-1;
		resetRequest = true;
		
		if(users.length>0)
		{
			user = users.pop();
		}
		else
		{
			user = null;
		}
		
		updateData();
		
		window.location.href = 'https://groupes.odyc.fr/Controllers/doGroupLogin.asp?action=directLogin&username=gpc011171&password=FZRYUpgj';
		throw new Error();
	}
	else if(user != null)
	{
		setTimeout(initializeAutoFillIn,timeDelay);
	}
}

function initializeAutoFillIn()
{
	if(!user)
	{
		window.initializeAutoFillIn=function(){return false;};
		return;
	}
	if(isIntroduction())
	{
		if(!configurePageCompleted)
		{
			resetRequest = false;
			goToAddUserPage();
		}
		else
		{
			/*if(user.roomId != null)
			{
				goToRoomPage();
			}
			else
			{
				resetForNextUser();
			}*/
			resetForNextUser();
		}
	}
	else if(isAddUserPage())
	{
		fillAddUserPageWithUser(user);
	}
	else if(isConfigurePage())
	{
		fillConfigurePage(user);
	}
	else if(isRoomPage())
	{
		var index = indexForRoom[user['roomId']];
		var page = index[0];
		var currentPage = jQuery('td.navigator b').text();
		if(currentPage != page)
		{
			goToUserRoomPage(user);
		}
		else
		{
			goToRoomAttributionPage(user);
		}
	}
	else if(isRoomAttributionPage())
	{
		if(!userAddedToRoom)
		{
			addUserToRoom(user);
		}
		else
		{
			if(!confirmUserAddedToRoom())
			{
				setTimeout(initializeAutoFillIn,timeDelay);
			}
			else
			{
				resetForNextUser();
			}
		}
	}
	else
	{
		//alert('Unknown page');
	}
}

function resetAll()
{
	userPageRequested = false;
	addUserPageCompleted = false;
	configurePageCompleted = false;
	roomPageRequested = false;
	userRoomPageRequested = false;
	roomAttributionPageRequested = false;
	userAddedToRoom = false;
	oldParticipantCount=-1;
	resetRequest = false;
	window.name=null;
	//throw new Error();
}

var resetFlag = 1;

if(resetFlag)
{
	resetAll();
}

jQuery(document).ready(function(){
	if(!resetFlag)
	{
		initializeAutoFillIn();
	}
});