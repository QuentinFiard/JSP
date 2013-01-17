<?php

namespace nav;

use utilities\RequestInformation;

use utilities\Server;

require_once ('classes/nav/RegisteredOnlyPage.php');
require_once 'classes/utilities/Server.php';
require_once 'classes/utilities/RequestInformation.php';

use nav\RegisteredOnlyPage;

class EventPage extends RegisteredOnlyPage {
	private $event;


	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		
		if(!RequestInformation::isAjax() || !isset($_GET['getDetails']))
		{
			if($this->event->haveReservationsStarted())
			{
				header('Location: '.Server::getServerRoot().substr($this->childWithName('inscription')->getPath(), 1));
				exit();
			}
			else if($user->hasReservationForEvent($this->event))
			{
				header('Location: '.Server::getServerRoot().substr($this->childWithName('inscription')->childWithName('configuration')->getPath(), 1));
				exit();
			}
			else
			{
				header('Location: '.Server::getServerRoot().substr($this->childWithName('countdown')->getPath(), 1));
				exit();
			}
		}
	}

	public function __construct($name, $title = "JSP - Site d'inscription aux évênements",$event) {
		parent::__construct($name,$title);
		$this->event = $event;
	}

	protected function getEventDetailsPath()
	{
		$path = $this->getPath();
		if($path == "/")
		{
			$path = "/home";
		}
		$path = "pages".$path.'_details.php';
		return $path;
	}
	
	public function includeEventDetails()
	{
		include $this->getEventDetailsPath();
	}
	
	public function handleAjaxRequest() {
		if(isset($_GET['getDetails']))
		{
			$res = array();
				
			ob_start();
			$this->includeEventDetails();
			$content = ob_get_clean();
				
			$res['details'] = $content;
				
			return $res;
		}
		else
		{
			return parent::handleAjaxRequest();
		}
	}

	public function getEvent() {
		return $this->event;
	}
	
}

?>