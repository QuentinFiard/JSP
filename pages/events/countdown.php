<?php
use structures\events\WeekendJSP;

use utilities\Server;
require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/events/WeekendJSP.php';
global $event;
?>
<div id="countdownContent" class="content">
	<input id="startDate" type="hidden" value="<?php echo $event->getReservationStart(); ?>" />
	<input id="now" type="hidden" value="<?php echo time(); ?>" />
	<input id="eventButton" type="hidden" value="buttonEvent<?php echo $event->getEventId(); ?>" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background2.jpg" />
	<div class="title">Les inscriptions pour <?php 
		if($event===WeekendJSP::shared())
		{
			?>le weekend JSP<?php
		} else {
			?>la semaine du réveillon<?php
		}
	?> ne sont pas encore ouvertes !</div>
	<div class="subtitle">Temps restant avant l'ouverture :</div>
	<div id="compteurs_wrapper">
		<div id="compteurs">
			<div id="jours" class="flip-counter"></div>
			<div id="heures" class="flip-counter"></div>
			<div id="minutes" class="flip-counter"></div>
			<div id="secondes" class="flip-counter"></div>
			
			<div class="legende" style="clear:left;">Jours</div>
			<div class="legende">Heures</div>
			<div class="legende">Minutes</div>
			<div class="legende" style="margin-right:0;">Secondes</div>
	    </div>
	</div>
</div>
