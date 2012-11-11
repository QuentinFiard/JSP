<?php

namespace pages\events\reveillon;

require_once ('classes/pages/events/CountDownPage.php');

class CountDownPage extends \pages\events\CountDownPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new CountDownPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("countdown","JSP - Compte à rebours avant l'ouverture des inscriptions");
	}
}

?>