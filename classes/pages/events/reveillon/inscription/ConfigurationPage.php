<?php

namespace pages\events\reveillon\inscription;

use pages\events\reveillon\inscription\configuration\UpdatePersonalDataPage;

use utilities\Miscellaneous;

use pages\events\reveillon\inscription\configuration\SuccessPage;
use pages\events\reveillon\inscription\configuration\RoomsPage;

require_once 'classes/pages/events/inscription/ConfigurationPage.php';
require_once 'classes/pages/events/reveillon/inscription/configuration/RoomsPage.php';
require_once 'classes/pages/events/reveillon/inscription/configuration/SuccessPage.php';
require_once 'classes/pages/events/reveillon/inscription/configuration/UpdatePersonalDataPage.php';

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
		parent::__construct("configuration","JSP - Configuration de l'inscription à la semaine du réveillon");

		$this->addChild(RoomsPage::getPage());
		$this->addChild(SuccessPage::getPage());
		$this->addChild(UpdatePersonalDataPage::getPage());
	}

}

?>