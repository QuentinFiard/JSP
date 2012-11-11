<?php

namespace pages\events\weekend;

use pages\events\AdherentKesOnlyInscriptionPage;

require_once 'classes/pages/events/AdherentKesOnlyInscriptionPage.php';

class InscriptionPage extends AdherentKesOnlyInscriptionPage {
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
	}
}

?>