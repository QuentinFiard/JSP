<?php

namespace nav;

use utilities\Server;

require_once ('classes/nav/AfterReservationPage.php');
require_once 'classes/utilities/Server.php';

abstract class WaitingListPage extends \nav\AfterReservationPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->isOnWaitingListForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(), 1));
			exit();
		}
	}

}

?>