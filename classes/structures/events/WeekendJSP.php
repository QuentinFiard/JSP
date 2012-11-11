<?php

namespace structures\events;

require_once ('classes/structures/Event.php');
require_once ('classes/pages/events/WeekendPage.php');

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
		return \pages\events\WeekendPage::getPage();
	}


}

?>