<?php

namespace pages;

require_once 'classes/nav/AdminOnlyPage.php';

require_once 'classes/pages/admin/RoomsPage.php';
require_once 'classes/pages/admin/EventOptionsPage.php';
require_once 'classes/pages/admin/UserPaymentPage.php';
require_once 'classes/pages/admin/AddToMainListPage.php';

require_once 'classes/pages/admin/AddShotgunReveillonPage.php';
require_once 'classes/pages/admin/TestUserPage.php';

use nav\AdminOnlyPage;

class AdminPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new AdminPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("admin","JSP - Page d'administration des inscriptions");

		$this->addChild(\pages\admin\RoomsPage::getPage());
		$this->addChild(\pages\admin\EventOptionsPage::getPage());
		$this->addChild(\pages\admin\UserPaymentPage::getPage());
		$this->addChild(\pages\admin\AddShotgunReveillonPage::getPage());
		$this->addChild(\pages\admin\AddToMainListPage::getPage());
		$this->addChild(\pages\admin\TestUserPage::getPage());
	}
}

?>