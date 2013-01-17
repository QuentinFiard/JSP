<?php

namespace pages\events\weekend\inscription\configuration;

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

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration/success.php';
	}

	public function __construct()
	{
		parent::__construct("success","JSP - Personnalisation de l'inscription enregistrée avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>