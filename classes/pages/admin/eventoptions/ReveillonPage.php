<?php

namespace pages\admin\eventoptions;

use pages\admin\EventOptionsPage;

use structures\events\SemaineReveillon;

require_once 'classes/pages/admin/EventOptionsPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';

class ReveillonPage extends EventOptionsPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ReveillonPage();
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
		parent::__construct("reveillon","JSP - Page d'administration des options pour la semaine du réveillon",SemaineReveillon::shared());
	}
}

?>