<?php

namespace pages\admin\eventoptions;

use structures\events\WeekendJSP;

require_once 'classes/pages/admin/EventOptionsPage.php';

require_once 'classes/structures/events/WeekendJSP.php';

use \pages\admin\EventOptionsPage;

class WeekendPage extends EventOptionsPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new WeekendPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		
	}
	
	protected function getPageContentPath() {
		global $event;
		$event = $this->event;
		return $this->getParent()->getPageContentPath();
	}

	protected function getPageScriptPath() {
		return $this->getParent()->getPageScriptPath();
	}
	
	public function __construct()
	{
		parent::__construct("weekend","JSP - Page d'administration des options pour le weekend JSP",WeekendJSP::shared());
	}
}

?>