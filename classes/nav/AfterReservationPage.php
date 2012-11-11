<?php

namespace nav;

require_once ('classes/nav/RegisteredOnlyPage.php');
require_once ('classes/utilities/Server.php');

use \nav\RegisteredOnlyPage;
use \utilities\Server;

abstract class AfterReservationPage extends RegisteredOnlyPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->hasReservationForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(), 1));
			exit();
		}
	}
	
	abstract function getEvent();
	
	function getReservation()
	{
		global $user;
		return $user->getReservationForEvent($this->getEvent());
	}
}

?>