<?php

namespace pages;

use nav\AdminOnlyPage;

use utilities\Server;

use utilities\Miscellaneous;

require_once ('classes/nav/AdminOnlyPage.php');
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/utilities/Server.php';

use nav\Page;

class CadreConfirmationPage extends AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new CadreConfirmationPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("cadreconfirmation","JSP - Validation de l'inscription d'un cadre");
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