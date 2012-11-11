<?php

namespace pages\events;

use utilities\Server;

require_once ('classes/nav/RegisteredOnlyPage.php');
require_once 'classes/utilities/Server.php';

use nav\RegisteredOnlyPage;

class CountDownPage extends RegisteredOnlyPage {

	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		if($this->getParent()->getEvent()->haveReservationsStarted())
		{
			$parent = $this->getParent();
			$inscription_page = $parent->childWithName('inscription');
			header('Location: '.Server::getServerRoot().substr($inscription_page->getPath(), 1));
			exit();
		}
	}

	protected function getPageContentPath() {
		global $event;
		$event = $this->getParent()->getEvent();
		
		return 'pages/events/countdown.php';
	}
	
	protected function getPageScriptPath() {
		return 'js/events/countdown.js';
	}

	protected function getPageStylePath() {
		return 'css/events/countdown.css';
	}
	
}

?>