<?php

namespace nav;

require_once ('classes/nav/RegisteredOnlyPage.php');
require_once ('classes/utilities/Server.php');

use \nav\Page;
use \utilities\Server;

class ExternalUserOnlyPage extends RegisteredOnlyPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->isExt())
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
	}
	
}

?>