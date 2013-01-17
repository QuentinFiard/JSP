<?php
use structures\events\WeekendJSP;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/events/WeekendJSP.php';

global $currentPage;
global $user;
$event = $this->getEvent();

$options = $user->getOptionsForEvent($event);

$subvention_name = "none";
$subvention_value = 0;
$subvention_option = null;

foreach($options as $option)
{
	$pos = strpos($option->getName(),'subvention');
	if(!($pos===false))
	{
		$subvention_option = $option;
		$subvention_name = $option->getName();
		$subvention_value = $option->getPriceForUser($user);
	}
}
?>
<div class="alert_box_wrapper" id="saveLocationBox">
	<div class="alert_box">
		<div class="title">Choix d'une option de location</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Tes options ont été enregistrées avec succès. Tu dois maintenant régler par chèque à l'ordre du Binet JSP un total de <?php echo $event->priceForUser($user); ?> €<?php if($subvention_option) {?> accompagné d'un chèque de <?php echo -$subvention_value; ?> € qui ne sera pas encaissé si tu bénéficies bien des subventions demandées<?php } ?>, et une caution de <?php echo $event->cautionForUser($user); ?> € à l'ordre de <?php 
							if($event==WeekendJSP::shared())
							{
								?>'Madame Vacances'<?php
							} else {
								?>Belhambra<?php
							} ?>.
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="window.location.reload(true);"/>
			</div>
		</div>
	</div>
</div>