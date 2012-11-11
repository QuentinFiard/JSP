<?php

namespace pages\moncompte;

use nav\FrankizUserOnlyPage;

require_once 'classes/nav/FrankizUserOnlyPage.php';

class FrankizAccountPage extends FrankizUserOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new FrankizAccountPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("frankiz","JSP - Gérer son compte");
	}
}

?>