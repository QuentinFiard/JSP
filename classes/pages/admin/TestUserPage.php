<?php

namespace pages\admin;

use structures\Session;

use \utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/Session.php';

class TestUserPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new TestUserPage();
		}
		return self::$page;
	}
	
	public function __construct($path=null,$title=null,$event=null)
	{
		parent::__construct("testuser","JSP - Test d'un utilisateur");
	}
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		Session::setValueForKey('userId', $_GET['userId']);
		
		header('Location: '.Server::getServerRoot());
		exit();
	}

}

?>