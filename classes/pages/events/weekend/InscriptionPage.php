<?php

namespace pages\events\weekend;

use pages\events\weekend\inscription\ConfigurationPage;

require_once 'classes/pages/events/InscriptionPage.php';

require_once 'classes/pages/events/weekend/inscription/CancelationPage.php';
require_once 'classes/pages/events/weekend/inscription/ConfigurationPage.php';
require_once 'classes/pages/events/weekend/inscription/WaitingListPage.php';
require_once 'classes/pages/events/weekend/inscription/MainListPage.php';

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
		parent::__construct("inscription","JSP - Page d'inscription au weekend JSP");

		$this->addChild(\pages\events\weekend\inscription\CancelationPage::getPage());
		$this->addChild(\pages\events\weekend\inscription\ConfigurationPage::getPage());
		$this->addChild(\pages\events\weekend\inscription\WaitingListPage::getPage());
		$this->addChild(\pages\events\weekend\inscription\MainListPage::getPage());
	}
}

?>