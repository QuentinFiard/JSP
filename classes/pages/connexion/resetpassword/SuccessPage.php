<?php

namespace pages\connexion;

use nav\UnregisteredOnlyPage;

require_once ('classes/nav/UnregisteredOnlyPage.php');

class SuccessPage extends UnregisteredOnlyPage {
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
		parent::__construct("success","JSP - Mot de passe réinitialisé avec succès");
	}
	
	public function handleAjaxRequest() {
		$res = parent::handleAjaxRequest();
		$res['success']=true;
		return $res;
	}

}

?>