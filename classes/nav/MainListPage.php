<?php

namespace nav;

use utilities\Server;

use nav\AfterReservationPage;

require_once ('classes/nav/AfterReservationPage.php');
require_once 'classes/utilities/Server.php';

abstract class MainListPage extends AfterReservationPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->isOnMainListForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(), 1));
			exit();
		}
	}

}

?>