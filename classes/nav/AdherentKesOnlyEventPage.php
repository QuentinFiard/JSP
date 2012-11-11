<?php

namespace nav;

use utilities\Server;

require_once ('classes/nav/EventPage.php');
require_once 'classes/utilities/Server.php';

class AdherentKesOnlyEventPage extends EventPage {
	
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