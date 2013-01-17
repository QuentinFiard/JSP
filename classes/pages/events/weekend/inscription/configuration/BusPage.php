<?php

namespace pages\events\weekend\inscription\configuration;

use pages\events\weekend\inscription\configuration\bus\SuccessPage;

require_once 'classes/pages/events/inscription/configuration/BusPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/bus/SuccessPage.php';

class BusPage extends \pages\events\inscription\configuration\BusPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new BusPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("bus","JSP - Choix du bus");
		
		$this->addChild(SuccessPage::getPage());
	}

}

?>