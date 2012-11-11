<?php

namespace nav;

require_once ('classes/nav/EventPage.php');
require_once ('classes/utilities/Server.php');

use \utilities\Server;

class AdherentKesOnlyPage extends EventPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->isAdherentKes())
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
	}
	
}

?>