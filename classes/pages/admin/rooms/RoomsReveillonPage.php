<?php

namespace pages\admin\rooms;

use structures\events\SemaineReveillon;

require_once 'classes/pages/admin/RoomsPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';

use \pages\admin\RoomsPage;

class RoomsReveillonPage extends RoomsPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new RoomsReveillonPage();
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
		parent::__construct("reveillon","JSP - Page d'administration des chambres pour la semaine du réveillon",SemaineReveillon::shared());
	}
}

?>