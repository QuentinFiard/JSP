<?php
use pages\events\weekend\inscription\ConfigurationPage;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/pages/events/weekend/inscription/ConfigurationPage.php';
global $currentPage;
global $user;
?>
<div class="alert_box_wrapper" id="forgottenPasswordBox">
	<div class="alert_box reservation_status">
		<div class="title">Liste d'attente</div>
		<div class="content_wrapper">
			<div class="subtitle">
				Il ne reste malheureusement plus de places disponibles pour le weekend JSP. Tu es cependant inscrit(e) en liste d'attente et nous te contacterons dès qu'une place se libère ! Nous ferons passer en priorité les personnes qui nous auront transmis leurs chèques, donc n'oublie pas de nous les apporter au plus vite.
			</div>
			<input class="primaryButton" type="button" value="Personnaliser l'inscription" onclick="hideAlertBox();goToPage('<?php echo ConfigurationPage::getPage()->getPath(); ?>');" />
		</div>
	</div>
</div>