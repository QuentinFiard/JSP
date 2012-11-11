<?php

namespace pages\connexion;

use nav\UnregisteredOnlyPage;

require_once ('classes/nav/UnregisteredOnlyPage.php');

require_once 'classes/pages/connexion/exterieurs/CreateAccountPage.php';

class ExterieursPage extends UnregisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ExterieursPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("exterieurs","JSP - Connexion pour les extérieurs");
		
		$this->addChild(\pages\connexion\exterieurs\CreateAccountPage::getPage());
	}
	
	public function isCreateNewAccountPage()
	{
		return false;
	}
}

?>