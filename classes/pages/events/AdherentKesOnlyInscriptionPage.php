<?php

namespace pages\events;

use utilities\Server;

use nav\AfterReservationStartPage;

require_once 'classes/nav/AfterReservationStartPage.php';

require_once 'classes/utilities/Server.php';

class AdherentKesOnlyInscriptionPage extends InscriptionPage {
	
	public function checkSecurityGrant() {
		global $user;
		if(!isset($user) || !$user->isAdherentKes())
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
		parent::checkSecurityGrant();
	}
	
}

?>