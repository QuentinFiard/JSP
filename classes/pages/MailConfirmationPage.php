<?php

namespace pages;

use utilities\Server;

use utilities\Miscellaneous;

require_once ('classes/nav/Page.php');
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/utilities/Server.php';

use nav\Page;

class MailConfirmationPage extends Page {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new MailConfirmationPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("mailconfirmation","JSP - Confirmation de l'adresse mail associé à votre compte");
	}
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		if(!isset($_GET['user']) || !Miscellaneous::isValidConfirmationId($_GET['user']))
		{
			header('Location: '.Server::getServerRoot());
			exit();
		}
	}

}

?>