<?php

namespace pages\events\inscription\configuration\chambres;

use nav\AfterReservationPage;

require_once ('classes/nav/AfterReservationPage.php');

class SuccessPage extends AfterReservationPage {

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration/chambres/success.php';
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
		parent::__construct("success","JSP - Chambre enregistrée avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>