<?php

namespace pages\events\weekend\inscription\configuration\bus;

use nav\AfterReservationPage;

require_once 'classes/pages/events/inscription/configuration/bus/SuccessPage.php';

class SuccessPage extends \pages\events\inscription\configuration\bus\SuccessPage {
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
		parent::__construct("success","JSP - Bus enregistré avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>