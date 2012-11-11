<?php

namespace nav;

require_once ('classes/nav/RegisteredOnlyPage.php');

use utilities\Server;
require_once 'classes/utilities/Server.php';

use nav\RegisteredOnlyPage;

abstract class AfterReservationStartPage extends RegisteredOnlyPage {
	
	abstract public function getEvent();
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		if(!$this->getEvent()->haveReservationsStarted())
		{
			$parent = $this->getParent();
			header('Location: '.Server::getServerRoot().substr($parent->getPath(), 1));
			exit();
		}
	}
}

?>