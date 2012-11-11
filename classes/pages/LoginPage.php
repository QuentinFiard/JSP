<?php

namespace pages;

use nav\UnregisteredOnlyPage;

require_once 'classes/nav/UnregisteredOnlyPage.php';
require_once 'classes/pages/login/ExternalLoginPage.php';

class LoginPage extends UnregisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new LoginPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("login","JSP - Login");
		
		$this->addChild(\pages\login\ExternalLoginPage::getPage());
	}
	
	public function includeContent()
	{
		include $this->getPageContentPath();
	}
}

?>