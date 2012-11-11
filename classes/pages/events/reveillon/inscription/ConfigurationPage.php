<?php

namespace pages\events\reveillon\inscription;

use structures\events\SemaineReveillon;

use nav\AfterReservationPage;

require_once 'classes/nav/AfterReservationPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';

class ConfigurationPage extends AfterReservationPage {
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
	}
	
	function getEvent() {
		return SemaineReveillon::shared();
	}

}

?>