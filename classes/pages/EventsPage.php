<?php

namespace pages;

require_once 'classes/nav/RegisteredOnlyPage.php';

use nav\RegisteredOnlyPage;

require_once 'classes/pages/events/ReveillonPage.php';
require_once 'classes/pages/events/WeekendPage.php';

class EventsPage extends RegisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new EventsPage();
		}
		return self::$page;
	}

	public function __construct()
	{
		parent::__construct("events","JSP - Évênements organisés");

		$this->addChild(\pages\events\ReveillonPage::getPage());
		$this->addChild(\pages\events\WeekendPage::getPage());
	}
}

?>