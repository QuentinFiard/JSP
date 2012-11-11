<?php

namespace pages;

require_once 'classes/nav/Page.php';
require_once 'classes/pages/connexion/ExterieursPage.php';
require_once 'classes/pages/connexion/ResetPasswordPage.php';

use nav\Page;

class ConnectionPage extends Page {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ConnectionPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("connexion","JSP - Connexion");

		$this->addChild(\pages\connexion\ExterieursPage::getPage());
		$this->addChild(\pages\connexion\ResetPasswordPage::getPage());
	}
}

?>