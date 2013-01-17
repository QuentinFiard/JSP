<?php

namespace pages\events\weekend\inscription;

use nav\AfterReservationPage;

use pages\events\weekend\inscription\cancelation\SuccessPage;

require_once 'classes/nav/AfterReservationPage.php';
require_once 'classes/pages/events/weekend/inscription/cancelation/SuccessPage.php';

class CancelationPage extends AfterReservationPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new CancelationPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("cancelation","JSP - Annulation de l'inscription");

		$this->addChild(SuccessPage::getPage());
	}

	protected function getPageScriptPath() {
		return 'js/events/cancelation.js';
	}
	
	function getEvent() {
		return $this->getParent()->getEvent();
	}
	
	public function handleAjaxRequest() {
		$res = array();
		$res['success'] = false;
		if(isset($_POST['confirmCancelation']))
		{
			global $user;
			$event = $this->getEvent();
			
			$reservation = $user->getReservationForEvent($event);
			if(isset($reservation['cashed']))
			{
				$res['cashed'] = true;
				return $res;
			}
			
			$user->cancelReservationForEvent($event);
			$event->sendAnnulationEmailToUser($user);
			
			return $this->childWithName('success')->handleAjaxRequest();
		}
		return parent::handleAjaxRequest();
	}

}

?>