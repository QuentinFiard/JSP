<?php

namespace pages\events\weekend;

use pages\events\AdherentKesOnlyCountDownPage;

require_once ('classes/pages/events/AdherentKesOnlyCountDownPage.php');

class CountDownPage extends AdherentKesOnlyCountDownPage {
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