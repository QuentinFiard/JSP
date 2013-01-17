<?php

namespace pages\events\reveillon\inscription\cancelation;

use nav\AfterReservationPage;

require_once ('classes/nav/AfterReservationPage.php');

class SuccessPage extends AfterReservationPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new SuccessPage();
		}
		return self::$page;
	}
	
	function getEvent() {
		return $this->getParent()->getEvent();
	}

	public function __construct()
	{
		parent::__construct("success","JSP - Inscription annulée avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>