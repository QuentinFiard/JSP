<?php

namespace nav;

require_once 'classes/pages/LoginPage.php';
require_once 'classes/pages/LogoutPage.php';
require_once 'classes/pages/EventsPage.php';
require_once 'classes/pages/ConnectionPage.php';
require_once 'classes/pages/AdminPage.php';
require_once 'classes/pages/MyAccountPage.php';
require_once 'classes/pages/MailConfirmationPage.php';

require_once 'classes/utilities/Server.php';

use \utilities\Server;

class Root extends Page {
	static private $page = null;
	
	static public function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new Root();
				
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct('/');
		
		$this->addChild(\pages\LoginPage::getPage());
		$this->addChild(\pages\LogoutPage::getPage());
		$this->addChild(\pages\EventsPage::getPage());
		$this->addChild(\pages\ConnectionPage::getPage());
		$this->addChild(\pages\AdminPage::getPage());
		$this->addChild(\pages\MyAccountPage::getPage());
		$this->addChild(\pages\MailConfirmationPage::getPage());
	}
	
	public function checkSecurityGrant() {
		global $user;
		if(isset($user) && $user->isRegistered() && !$user->isAdmin())
		{
			header('Location: '.Server::getServerRoot().'events');
			exit();
		}
	}

}

?>