<?php

namespace pages;

use nav\RegisteredOnlyPage;

require_once 'classes/nav/RegisteredOnlyPage.php';

use nav\LeafPage;

class LogoutPage extends RegisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new LogoutPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("logout","JSP - Logout");
	}
	
	public function includeContent()
	{
		include $this->getPageContentPath();
	}
}

?>