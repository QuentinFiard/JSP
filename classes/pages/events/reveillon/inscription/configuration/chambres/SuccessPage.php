<?php

namespace pages\events\reveillon\inscription\configuration\chambres;

use nav\AfterReservationPage;

require_once 'classes/pages/events/inscription/configuration/chambres/SuccessPage.php';

class SuccessPage extends \pages\events\inscription\configuration\chambres\SuccessPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new SuccessPage();
		}
		return self::$page;
	}

	public function __construct()
	{
		parent::__construct("success","JSP - Chambre enregistrée avec succès");
	}

}

?>