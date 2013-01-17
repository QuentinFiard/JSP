<?php

namespace pages\events\weekend\inscription;

use pages\events\weekend\inscription\configuration\UpdatePersonalDataPage;

use utilities\Miscellaneous;

use pages\events\weekend\inscription\configuration\SuccessPage;
use pages\events\weekend\inscription\configuration\RoomsPage;
use pages\events\weekend\inscription\configuration\BusPage;

require_once 'classes/pages/events/inscription/ConfigurationPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/RoomsPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/BusPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/SuccessPage.php';
require_once 'classes/pages/events/weekend/inscription/configuration/UpdatePersonalDataPage.php';

class ConfigurationPage extends \pages\events\inscription\ConfigurationPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ConfigurationPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("configuration","JSP - Configuration de l'inscription au weekend JSP");

		$this->addChild(RoomsPage::getPage());
		$this->addChild(BusPage::getPage());
		$this->addChild(SuccessPage::getPage());
		$this->addChild(UpdatePersonalDataPage::getPage());
	}

}

?>