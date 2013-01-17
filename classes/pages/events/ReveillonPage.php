<?php

namespace pages\events;

use pages\admin\AddToMainListPage;

use structures\events\SemaineReveillon;

require_once 'classes/nav/EventPage.php';
require_once 'classes/pages/events/reveillon/CountDownPage.php';
require_once 'classes/pages/events/reveillon/InscriptionPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';

use nav\EventPage;

class ReveillonPage extends \nav\EventPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ReveillonPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("reveillon","JSP - Semaine du réveillon",SemaineReveillon::shared());

		$this->addChild(\pages\events\reveillon\CountDownPage::getPage());
		$this->addChild(\pages\events\reveillon\InscriptionPage::getPage());
	}
}

?>