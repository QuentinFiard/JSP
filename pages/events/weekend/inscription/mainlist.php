<?php
use pages\events\weekend\inscription\ConfigurationPage;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/pages/events/weekend/inscription/ConfigurationPage.php';
global $currentPage;
global $user;
?>
<div class="alert_box_wrapper">
	<div class="alert_box reservation_status">
		<div class="title">Liste principale</div>
		<div class="content_wrapper">
			<div class="subtitle">
				Félicitations, tu es inscrit sur liste principale pour le weekend JSP ! N'oublie pas de nous apporter ou de nous envoyer tes chèques le plus rapidement possible, si nous ne les avons pas reçus sous <b><?php if($user->isX()) {?>une<?php } else {?>2<?php }?> semaine<?php if(!$user->isX()) {?>s<?php } ?></b> nous donnerons ta place à une personne en liste d'attente.
			</div>
			<input class="primaryButton" type="button" value="Personnaliser l'inscription" onclick="hideAlertBox();goToPage('<?php echo ConfigurationPage::getPage()->getPath(); ?>');" />
		</div>
	</div>
</div>