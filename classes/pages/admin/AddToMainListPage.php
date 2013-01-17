<?php

namespace pages\admin;

use structures\User;

use database\Database;

use structures\Event;

require_once 'classes/nav/AdminOnlyPage.php';

use nav\AdminOnlyPage;

require_once ('classes/utilities/Server.php');
require_once 'classes/database/Database.php';
require_once 'classes/structures/User.php';

use \utilities\Server;

class AddToMainListPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new AddToMainListPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		if($_GET['reservationId'])
		{
			$reservation = Database::shared()->getReservationWithReservationId($_GET['reservationId']);
			$event = Event::eventWithEventId($reservation['eventId']);
			
			if($reservation && !$reservation['mainList'])
			{
				$user = User::userWithUserId($reservation['userId']);
				if($event->getNbOfPlacesLeft()<=0)
				{
					$event->addToNumberOfPlaces(1);
				}
				$user->switchToMainListForEvent($event);
			}
		}
		
		header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(), 1));
		exit();
	}

	public function __construct($path=null,$title=null,$event=null)
	{
		parent::__construct("addtomainlist","JSP - Ajout d'une réservation sur la liste principale");
	}
}

?>