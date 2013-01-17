<?php

namespace pages\events\weekend\inscription\configuration;

use pages\events\weekend\inscription\configuration\chambres\SuccessPage;

require_once 'classes/pages/events/inscription/configuration/RoomsPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/chambres/SuccessPage.php';

class RoomsPage extends \pages\events\inscription\configuration\RoomsPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new RoomsPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("chambres","JSP - Choix de la chambre");
		
		$this->addChild(SuccessPage::getPage());
	}

}

?>