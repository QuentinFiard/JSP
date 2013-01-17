<?php

namespace pages\events\inscription\configuration\bus;

use nav\AfterReservationPage;

require_once ('classes/nav/AfterReservationPage.php');

class SuccessPage extends AfterReservationPage {

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration/bus/success.php';
	}
	
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
		parent::__construct("success","JSP - Bus enregistré avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>