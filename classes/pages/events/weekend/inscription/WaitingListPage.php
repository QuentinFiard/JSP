<?php

namespace pages\events\weekend\inscription;

use structures\events\WeekendJSP;

require_once ('classes/nav/WaitingListPage.php');
require_once 'classes/structures/events/WeekendJSP.php';

class WaitingListPage extends \nav\WaitingListPage {
	private static $page = null;

	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new WaitingListPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("waitinglist","JSP - Inscription sur liste d'attente");
	}
	
	function getEvent() {
		return WeekendJSP::shared();
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success'] = true;
		return $res;
	}

}

?>