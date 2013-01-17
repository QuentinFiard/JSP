<?php use structures\events\SemaineReveillon;
header('Content-type:application/javascript; charset=utf-8'); ?>
<?php require_once '../jquery.js';
chdir('../../');?>

window.name=null;
throw new Error();

var users = [
<?php

require_once 'classes/structures/events/SemaineReveillon.php';
$event = SemaineReveillon::shared();
$users = $event->getUsers();
$filteredUsers = array();

$ignored = array('quentin.fiard@polytechnique.edu',
'gauthier.petetin@polytechnique.edu',
'thomas.brichard@polytechnique.edu',
'morgane.barthod@polytechnique.edu',
'hassane.al-sakka@polytechnique.edu',
'cyril.allard@polytechnique.edu',
'boris.azimi@polytechnique.edu',
'adrien.bilal@polytechnique.edu',
'yoann.buratti@polytechnique.edu',
'pierre-victor.chaumier@polytechnique.edu',
'matthieu.de-cointet-de-fillain@polytechnique.edu',
'romain.faugeroux@polytechnique.edu',
'pierre-antoine.gigos@polytechnique.edu',
'alexandre.houle@polytechnique.edu',
'verenarrabiata@gmail.com',
'pierre.lerat@polytechnique.edu',
'gautier.maigret@gmail.com',
'marie.mouries@polytechnique.edu',
'clement.perrot@polytechnique.edu',
'jean.rabault@polytechnique.edu',
'gaetan.rougevin-baville@polytechnique.edu',
'guillaume.ruty@polytechnique.edu',
'xavier-thomas.starkloff@polytechnique.edu',
'david.baruchel@polytechnique.edu',
'francois.wyon@gmail.com',
'gauthierpetetin@gmail.com',
'frederic.aoustin@polytechnique.edu',
'yburatti@gmail.com',
'victor.debray@polytechnique.edu',
'alexandre.jung@polytechnique.edu',
'pierre.berolatti@wanadoo.fr',
'arthur.argenson@polytechnique.edu',
'romain.bey@polytechnique.edu',
'pierre-louis.candau-tilh@polytechnique.edu',
'robin.goix@free.fr',
'paul.aylward@polytechnique.edu',
'paul.beaumont@polytechnique.edu',
'pierre.berolatti@polytechnique.edu',
'charlesbonnard@gmail.com',
'cyril.borely@polytechnique.edu',
'ariane.cotte@polytechnique.edu',
'simon.bouteille@polytechnique.edu',
'jean.caille@polytechnique.edu',
'el-hadi.caoui@polytechnique.edu',
'louis.chatelet@gmail.com',
'hadrien.de-march@polytechnique.edu',
'frederic.aoustin@polytechnique.edu',
'yburatti@gmail.com',
'victor.debray@polytechnique.edu',
'alexandre.jung@polytechnique.edu',
'pierre.berolatti@wanadoo.fr',
'arthur.argenson@polytechnique.edu',
'romain.bey@polytechnique.edu',
'pierre-louis.candau-tilh@polytechnique.edu',
'robin.goix@free.fr',
'paul.aylward@polytechnique.edu',
'paul.beaumont@polytechnique.edu',
'pierre.berolatti@polytechnique.edu',
'charlesbonnard@gmail.com',
'cyril.borely@polytechnique.edu',
'el-hadi.caoui@polytechnique.edu',
'mahmoud.ezzaki@polytechnique.edu',
'simon.bouteille@polytechnique.edu',
'ariane.cotte@polytechnique.edu',
'jean.caille@polytechnique.edu',
'charlotte.constans@polytechnique.edu',
'louis.chatelet@gmail.com',
'hadrien.de-march@polytechnique.edu',
'clement.choukroun@polytechnique.edu',
'bastien.conan@polytechnique.edu',
'pablovalles@gmail.com',
'chantal.ding@polytechnique.edu',
'gautier.dreyfus@polytechnique.edu',
'valentin.waeselynck@polytechnique.edu',
'hugo.fayolle@polytechnique.edu',
'thomas.flamme@polytechnique.edu',
'lucas.garcia@polytechnique.edu',
'hugo.ghiron@polytechnique.edu',
'robin.goix@polytechnique.edu',
'rafarafita@gmail.com',
'olivier.gras@polytechnique.edu',
'bechara.hage-meany@polytechnique.edu',
'olivier.howaizi@polytechnique.edu',
'quentin.iprex-garcia@polytechnique.edu',
'arnaud.janvier@polytechnique.edu',
'justine.lamberger@hec.edu',
'adrien.laroche@polytechnique.edu',
'raphael.cyna@polytechnique.edu',
'sarah.le-net@polytechnique.edu',
'adrien.leredde@polytechnique.edu',
'william.lochet@gmail.com',
'pierre.magnan@polytechnique.edu',
'tristan.martin@polytechnique.edu',
'cyrilallard@hotmail.fr',
'alexis.mathieu@polytechnique.edu',
'roman.moscoviz@polytechnique.edu',
'pauline.metivier@polytechnique.edu',
'yann.nicolas@polytechnique.edu',
'julien.panis-lie@polytechnique.edu',
'marion.paolini@polytechnique.edu',
'pierre-yves.perrin@polytechnique.edu',
'alexis.pibrac@polytechnique.edu',
'felix.pignard@polytechnique.edu',
'antoine.ponsard@polytechnique.edu',
'remi.poncot@polytechnique.edu',
'jonathan.revault@essec.edu',
'solenne.rezvoy@polytechnique.edu',
'ludovica.rizzo@polytechnique.edu',
'remi.roblot@polytechnique.edu',
'kevin.rousseau@polytechnique.edu',
'philippe.rovani@polytechnique.edu',
'charles.royal@polytechnique.edu',
'coralie.ruffenach@polytechnique.edu',
'charlotte.saglier@polytechnique.edu',
'jacques.samain@polytechnique.edu',
'nathan.skrzypczak@polytechnique.edu',
'plr77@free.fr',
'tristan.sylvain@polytechnique.edu',
'raphael_cyna@hotmail.com',
'philippinevillemain@hotmail.fr',
'frederic.aoustin@polytechnique.edu',
'yburatti@gmail.com',
'victor.debray@polytechnique.edu',
'alexandre.jung@polytechnique.edu',
'pierre.berolatti@wanadoo.fr',
'arthur.argenson@polytechnique.edu',
'romain.bey@polytechnique.edu',
'pierre-louis.candau-tilh@polytechnique.edu',
'robin.goix@free.fr',
'paul.aylward@polytechnique.edu',
'paul.beaumont@polytechnique.edu',
'pierre.berolatti@polytechnique.edu',
'charlesbonnard@gmail.com',
'cyril.borely@polytechnique.edu',
'el-hadi.caoui@polytechnique.edu',
'mahmoud.ezzaki@polytechnique.edu',
'simon.bouteille@polytechnique.edu',
'ariane.cotte@polytechnique.edu',
'jean.caille@polytechnique.edu',
'charlotte.constans@polytechnique.edu',
'louis.chatelet@gmail.com',
'hadrien.de-march@polytechnique.edu',
'clement.choukroun@polytechnique.edu',
'bastien.conan@polytechnique.edu',
'pablovalles@gmail.com',
'chantal.ding@polytechnique.edu',
'gautier.dreyfus@polytechnique.edu',
'valentin.waeselynck@polytechnique.edu',
'hugo.fayolle@polytechnique.edu',
'thomas.flamme@polytechnique.edu',
'lucas.garcia@polytechnique.edu',
'hugo.ghiron@polytechnique.edu',
'robin.goix@polytechnique.edu',
'rafarafita@gmail.com',
'olivier.gras@polytechnique.edu',
'bechara.hage-meany@polytechnique.edu',
'olivier.howaizi@polytechnique.edu',
'quentin.iprex-garcia@polytechnique.edu',
'arnaud.janvier@polytechnique.edu',
'justine.lamberger@hec.edu',
'adrien.laroche@polytechnique.edu',
'raphael.cyna@polytechnique.edu',
'sarah.le-net@polytechnique.edu',
'adrien.leredde@polytechnique.edu',
'william.lochet@gmail.com',
'pierre.magnan@polytechnique.edu',
'tristan.martin@polytechnique.edu',
'cyrilallard@hotmail.fr',
'alexis.mathieu@polytechnique.edu',
'roman.moscoviz@polytechnique.edu',
'pauline.metivier@polytechnique.edu',
'yann.nicolas@polytechnique.edu',
'julien.panis-lie@polytechnique.edu',
'marion.paolini@polytechnique.edu',
'pierre-yves.perrin@polytechnique.edu',
'alexis.pibrac@polytechnique.edu',
'felix.pignard@polytechnique.edu',
'antoine.ponsard@polytechnique.edu',
'remi.poncot@polytechnique.edu',
'jonathan.revault@essec.edu',
'solenne.rezvoy@polytechnique.edu',
'ludovica.rizzo@polytechnique.edu',
'remi.roblot@polytechnique.edu',
'kevin.rousseau@polytechnique.edu',
'philippe.rovani@polytechnique.edu',
'charles.royal@polytechnique.edu',
'coralie.ruffenach@polytechnique.edu',
'charlotte.saglier@polytechnique.edu',
'jacques.samain@polytechnique.edu',
'nathan.skrzypczak@polytechnique.edu',
'plr77@free.fr',
'tristan.sylvain@polytechnique.edu',
'raphael_cyna@hotmail.com',
'philippinevillemain@hotmail.fr',
'raphael.cyna@polytechnique.edu',
'vivien.londe@polytechnique.edu',
'marc-olivier.renou@polytechnique.edu',
'vincent.sauzay@polytechnique.edu',
'adrien.tantin@polytechnique.edu',);

echo "/*\n";

foreach($users as $user)
{
	$reservation = $user->getReservationForEvent($event);
	if($user->isOnMainListForEvent($event) && $reservation['cashed']!=null && !in_array($user->getEmail(), $ignored))
	{
		$filteredUsers[] = $user;
	}
	if($user->isOnMainListForEvent($event))
	{
		echo $user->getReservationForEvent($event)['reservationId'].','.$user->getLastname().','.$user->getFirstname().','.$event->priceForUser($user)."\n";
	}
}


echo "\n*/\n";

$filteredUsers = array_reverse($filteredUsers);

foreach($filteredUsers as $user)
{
?>
{
	firstname:'<?php echo $user->getFirstname(); ?>',
	lastname:'<?php echo ucfirst($user->getLastname()); ?>',
	initials:'<?php echo strtoupper(substr($user->getFirstname(),0,1).substr($user->getLastname(),0,1));  ?>',
	birthday:[<?php 
		$startDate = mktime(0,0,0,1,1,1988);
		$endDate = mktime(23,59,0,12,31,1992);
		
		$birthDate = rand($startDate, $endDate);
		
		echo date('"j","m","Y"',$birthDate);
	?>],
	wasAdded:false,
	address:'Boulevard des Marechaux',
	houseNumber:'11',
	postalCode:'91120',
	city:'Palaiseau',
	homePhoneNumber:'0633563483',
	emailAddress:'<?php echo $user->getEmail(); ?>',
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
	roomId:<?php 
		$room = $user->getRoomForEvent($event);
		echo $room->getRoomId();
	?>
},
<?php
}
?>
];

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
	var bar = $('#Coordonnées');
	return (bar.length>0);
}

function isConfigurePage()
{
	return (document.getElementById('Food Pack La Plagne.container') != null);
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
		var link = $("a:contains('Ajouter')");
		link.click();
		userPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,100);
}

function inputForOption(optionId)
{/*
	19	1	location_pack_ski_eco
20	1	location_pack_ski_decouverte
21	1	location_pack_ski_sensation
22	1	location_ski_eco
23	1	location_ski_decouverte
24	1	location_ski_sensation
25	1	location_chaussures_ski_eco
26	1	location_chaussures_ski_decouverte
27	1	location_chaussures_ski_sensation
28	1	location_pack_surf_decouverte
29	1	location_pack_surf_sensation
30	1	location_surf_decouverte
31	1	location_surf_sensation
32	1	location_chaussures_surf_eco
33	1	location_chaussures_surf_decouverte
34	1	location_chaussures_surf_sensation
35	1	food_pack
36	1	food_pack_sans_porc
37	1	forfait_paradiski
38	1	no_forfait

*/
	var inputs = $('input[type="radio"]')
	
	if(optionId==19)
	{
		return inputs.eq(23);
	}
	else if(optionId==20)
	{
		return inputs.eq(24);
	}
	else if(optionId==21)
	{
		return inputs.eq(25);
	}
	else if(optionId==22)
	{
		return inputs.eq(26);
	}
	else if(optionId==23)
	{
		return inputs.eq(27);
	}
	else if(optionId==24)
	{
		return inputs.eq(28);
	}
	else if(optionId==25)
	{
		return inputs.eq(33);
	}
	else if(optionId==26)
	{
		return inputs.eq(34);
	}
	else if(optionId==27)
	{
		return inputs.eq(35);
	}
	else if(optionId==28)
	{
		return inputs.eq(29);
	}
	else if(optionId==29)
	{
		return inputs.eq(30);
	}
	else if(optionId==30)
	{
		return inputs.eq(31);
	}
	else if(optionId==31)
	{
		return inputs.eq(32);
	}
	else if(optionId==32)
	{
		return inputs.eq(33);
	}
	else if(optionId==33)
	{
		return inputs.eq(34);
	}
	else if(optionId==34)
	{
		return inputs.eq(35);
	}
	else if(optionId==35)
	{
		return inputs.eq(1);
	}
	else if(optionId==36)
	{
		return inputs.eq(2);
	}
	else if(optionId==37)
	{
		return inputs.eq(19);
	}
	else if(optionId==38)
	{
		return inputs.eq(17);
	}
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
		var input = $('input[name="firstName"]');
		input.val(user['firstname']);
		
		input = $('input[name="initials"]');
		input.val(user['initials']);
		
		input = $('input[name="name"]');
		input.val(user['lastname']);
		
		input = $('input[name="birthDate_day"]');
		input.val(user['birthday'][0]);
		input = $('input[name="birthDate_month"]');
		input.val(user['birthday'][1]);
		input = $('input[name="birthDate_year"]');
		input.val(user['birthday'][2]);
		
		input = $('input[name="address"]');
		input.val(user['address']);
		input = $('input[name="houseNumber"]');
		input.val(user['houseNumber']);
		input = $('input[name="postalCode"]');
		input.val(user['postalCode']);
		input = $('input[name="city"]');
		input.val(user['city']);
		
		input = $('input[name="homePhoneNumber"]');
		input.val(user['homePhoneNumber']);
		
		input = $('input[name="emailAddress"]');
		input.val(user['emailAddress']);
		
		user['wasAdded'] = true;
		
		var submit = $(document.getElementById('participant.submit'));
		submit.click();
		addUserPageCompleted = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,100);
}

function endConfigurePage()
{
	var submit = $(document.getElementById('options.submit'));
	submit.click();
	configurePageCompleted = true;
	updateData();
	
	setTimeout(initializeAutoFillIn,100);
}

function fillConfigurePage(user)
{
	if(!configurePageCompleted)
	{
		var options = user['options'];
		for(var i=0 ; i < options.length ; i++)
		{
			var input = inputForOption(options[i]);
			input.click();
		}
		
		setTimeout(endConfigurePage,100);
	}
	else
	{
		setTimeout(initializeAutoFillIn,100);
	}
}

function saveUserId()
{
	var table = $('#participants');
	
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
	if(!roomPageRequested)
	{
		var link = $("a:contains('chambres')");
		link.click();
		roomPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,100);
}

function goToUserRoomPage(user)
{
	if(!userRoomPageRequested)
	{
		var index = indexForRoom[user['roomId']];
		
		var page = index[0];
		var currentPage = $('td.navigator b').text();
		if(currentPage != page)
		{
			var link = $('.navigator td a');
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
	setTimeout(initializeAutoFillIn,100);
}

function goToRoomAttributionPage(user)
{
	if(!roomAttributionPageRequested)
	{
		var index = indexForRoom[user['roomId']];
		var row = index[1];
		row = $('#rooms_'+row);
		var link = row.find('a');
		link.click();
		roomAttributionPageRequested = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,100);
}

function addUserToRoom(user)
{
	if(!userAddedToRoom)
	{
		var rows = $(document.getElementById('availableParticipants.body')).find('tr');
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
			oldParticipantCount = $('#participants tr').length;
			row.find('a').click();
		}
		
		userAddedToRoom = true;
		updateData();
	}
	setTimeout(initializeAutoFillIn,100);
}

function confirmUserAddedToRoom()
{
	var rows = $('#participants tr');
	
	return (rows.length != oldParticipantCount);
}

function resetForNextUser()
{
	if(!resetRequest)
	{
		var link = $("a:contains('Sommaire')");
		link.click();
		
		userPageRequested = false;
		addUserPageCompleted = false;
		configurePageCompleted = false;
		roomPageRequested = false;
		userRoomPageRequested = false;
		roomAttributionPageRequested = false;
		userAddedToRoom = false;
		oldParticipantCount=-1;
		resetRequest = false;
		
		if(users.length>0)
		{
			user = users.pop();
		}
		else
		{
			user = null;
		}
		
		updateData();
		
		setTimeout(function(){window.location.href = 'https://groupes.odyc.fr/Controllers/doGroupLogin.asp?action=directLogin&username=gpc011041&password=sExgz5zE'},100);
	}
	if(user != null)
	{
		setTimeout(initializeAutoFillIn,100);
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
		resetRequest = false;
		if(!configurePageCompleted)
		{
			goToAddUserPage();
		}
		else
		{
			if(!('userId' in user))
			{
				if(saveUserId())
				{
					goToRoomPage();
				}
			}
			else
			{
				goToRoomPage();
			}
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
		var currentPage = $('td.navigator b').text();
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
				setTimeout(initializeAutoFillIn,100);
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

$(document).ready(function(){
	initializeAutoFillIn();
});