<?php

namespace pages\events\reveillon\inscription;

use structures\events\SemaineReveillon;

require_once ('classes/nav/WaitingListPage.php');
require_once 'classes/structures/events/SemaineReveillon.php';

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
		return SemaineReveillon::shared();
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success'] = true;
		return $res;
	}

}

?>