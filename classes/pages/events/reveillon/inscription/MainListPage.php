<?php

namespace pages\events\reveillon\inscription;

use structures\events\SemaineReveillon;

require_once ('classes/nav/MainListPage.php');
require_once 'classes/structures/events/SemaineReveillon.php';

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
		return SemaineReveillon::shared();
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success'] = true;
		return $res;
	}

}

?>