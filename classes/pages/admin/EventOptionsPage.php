<?php

namespace pages\admin;

use exceptions\OptionHasUsers;

use database\Database;

use structures\Option;

require_once 'classes/nav/AdminOnlyPage.php';

use nav\AdminOnlyPage;

require_once ('classes/utilities/Server.php');
require_once 'classes/structures/Option.php';
use \utilities\Server;

class EventOptionsPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	protected $event;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new EventOptionsPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(),1));
		exit();
	}
	
	public function handleAjaxRequest()
	{
		$res = array();
		$res['success']=false;
		if(isset($_POST['addOption']))
		{
			$_POST['eventId'] = $this->event->getEventId();
			$option = new Option($this->event, $_POST);
			
			$option->save();
			
			$res['success'] = true;
			
			return $res;
		}
		else if(isset($_POST['updateOption']))
		{
			$option = Option::optionWithOptionId($_POST['optionId']);
			$option->updateWithData($_POST);
			try {
				$option->save();
			
				$res['success'] = true;
				return $res;
			}
			catch(OptionHasUsers $e)
			{
				$res['option_has_users'] = true;
				return $res;
			}
		}
		else if(isset($_POST['dropOption']))
		{
			$option = Option::optionWithOptionId($_POST['optionId']);
			try {
				$option->drop();
				$res['success'] = true;
				return $res;
			}
			catch(OptionHasUsers $e)
			{
				$res['option_has_users'] = true;
				return $res;
			}
		}
		return parent::handleAjaxRequest();
	}
	
	public function __construct($path=null,$title=null,$event=null)
	{
		if(!isset($path))
		{
			parent::__construct("eventoptions","JSP - Page d'administration des options d'inscription");
	
			require_once ('classes/pages/admin/eventoptions/ReveillonPage.php');
			require_once ('classes/pages/admin/eventoptions/WeekendPage.php');
	
			$this->addChild(\pages\admin\eventoptions\ReveillonPage::getPage());
			$this->addChild(\pages\admin\eventoptions\WeekendPage::getPage());
		}
		else
		{
			parent::__construct($path,$title);
			$this->event = $event;
		}
	}
}

?>