<?php

namespace pages\events;

use database\Database;

use utilities\Server;

use nav\AfterReservationStartPage;

require_once 'classes/nav/AfterReservationStartPage.php';

require_once 'classes/utilities/Server.php';
require_once 'classes/database/Database.php';

class RoomsPage extends AfterReservationStartPage {

	protected function getPageScriptPath() {
		return 'js/events/rooms.js';
	}

	protected function getPageStylePath() {
		return 'css/events/rooms.css';
	}
	
	public function getEvent() {
		return $this->getParent()->getEvent();
	}
}

?>