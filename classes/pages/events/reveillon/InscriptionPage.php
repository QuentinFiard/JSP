<?php

namespace pages\events\reveillon;

use pages\events\reveillon\inscription\ConfigurationPage;

require_once 'classes/pages/events/InscriptionPage.php';

require_once 'classes/pages/events/reveillon/inscription/ConfigurationPage.php';
require_once 'classes/pages/events/reveillon/inscription/WaitingListPage.php';
require_once 'classes/pages/events/reveillon/inscription/MainListPage.php';

class InscriptionPage extends \pages\events\InscriptionPage {
	private static $page = null;

	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new InscriptionPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("inscription","JSP - Page d'inscription à la semaine du réveillon");

		$this->addChild(\pages\events\reveillon\inscription\ConfigurationPage::getPage());
		$this->addChild(\pages\events\reveillon\inscription\WaitingListPage::getPage());
		$this->addChild(\pages\events\reveillon\inscription\MainListPage::getPage());
	}
}

?>