<?php

namespace pages\events;

use pages\admin\AddToMainListPage;

use nav\AdherentKesOnlyEventPage;

use structures\events\WeekendJSP;

require_once 'classes/nav/AdherentKesOnlyEventPage.php';
require_once 'classes/pages/events/weekend/CountDownPage.php';
require_once 'classes/pages/events/weekend/InscriptionPage.php';

require_once 'classes/structures/events/WeekendJSP.php';

class WeekendPage extends AdherentKesOnlyEventPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new WeekendPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("weekend","JSP - Weekend JSP",WeekendJSP::shared());

		$this->addChild(\pages\events\weekend\CountDownPage::getPage());
		$this->addChild(\pages\events\weekend\InscriptionPage::getPage());
	}
}

?>