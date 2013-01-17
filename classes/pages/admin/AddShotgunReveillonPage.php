<?php

namespace pages\admin;

use database\Database;

use structures\Event;

require_once 'classes/nav/AdminOnlyPage.php';

use nav\AdminOnlyPage;

require_once ('classes/utilities/Server.php');
require_once 'classes/database/Database.php';

use \utilities\Server;

class AddShotgunReveillonPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new AddShotgunReveillonPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		if($_GET['uid'])
		{
			Database::shared()->addShotgunReveillon($_GET['uid']);
		}
		
		header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(), 1));
		exit();
	}

	public function __construct($path=null,$title=null,$event=null)
	{
		parent::__construct("shotgunreveillon","JSP - Shotgun de places pour la semaine du réveillon");
	}
}

?>