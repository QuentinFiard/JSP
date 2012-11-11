<?php

namespace nav;

require_once ('classes/nav/Page.php');
require_once ('classes/utilities/Server.php');

use \nav\Page;
use \utilities\Server;

class UnregisteredOnlyPage extends Page {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(isset($user) && $user->isRegistered())
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
	}
	
}

?>