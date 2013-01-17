<?php

namespace structures\events;

use structures\FrankizUser;

use database\Database;

require_once ('classes/structures/Event.php');
require_once 'classes/database/Database.php';
require_once 'classes/structures/FrankizUser.php';

use structures\Event;

class WeekendJSP extends Event {
	static private $shared = null;
	
	protected function __construct()
	{
		parent::__construct(2);
	}
	
	static public function shared()
	{
		if(self::$shared==null)
		{
			self::$shared = new WeekendJSP();
		}
		return self::$shared;
	}
	
	public function getPage()
	{
		require_once ('classes/pages/events/WeekendPage.php');
		return \pages\events\WeekendPage::getPage();
	}
	
	public function getNameWithPrefixA() {
		return 'au weekend JSP';
	}
	
	public function getNameWithPrefixPour() {
		return 'pour le weekend JSP';
	}
	
	public function isGagnantPlace($user) {
		return !$user->isExt() && Database::shared()->isFrankizUserGagnantPlace($user);
	}
}

?>