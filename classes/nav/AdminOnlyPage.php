<?php

namespace nav;

require_once ('classes/nav/RegisteredOnlyPage.php');
require_once ('classes/utilities/Server.php');

use \nav\Page;
use \utilities\Server;

class AdminOnlyPage extends RegisteredOnlyPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if(!$user->isMember())
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
	}
	protected function getPageStylePath() {
		return 'css/admin.css';
	}
	
}

?>