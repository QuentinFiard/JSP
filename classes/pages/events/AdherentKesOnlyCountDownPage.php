<?php

namespace pages\events;
use utilities\Server;

require_once ('classes/pages/events/CountDownPage.php');
require_once 'classes/utilities/Server.php';

class AdherentKesOnlyCountDownPage extends CountDownPage {

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