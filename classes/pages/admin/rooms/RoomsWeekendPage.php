<?php

namespace pages\admin\rooms;

use structures\events\WeekendJSP;

require_once 'classes/pages/admin/RoomsPage.php';

require_once 'classes/structures/events/WeekendJSP.php';

use \pages\admin\RoomsPage;

class RoomsWeekendPage extends RoomsPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new RoomsWeekendPage();
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
		parent::__construct("weekend","JSP - Page d'administration des chambres pour le weekend JSP",WeekendJSP::shared());
	}
}

?>