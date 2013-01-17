<?php

namespace pages\events\weekend\inscription;

use structures\events\WeekendJSP;

require_once ('classes/nav/MainListPage.php');
require_once 'classes/structures/events/WeekendJSP.php';

class MainListPage extends \nav\MainListPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new MainListPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("mainlist","JSP - Inscription sur liste principale");
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