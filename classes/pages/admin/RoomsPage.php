<?php

namespace pages\admin;

use structures\Room;

require_once 'classes/nav/AdminOnlyPage.php';

use nav\AdminOnlyPage;

require_once ('classes/utilities/Server.php');
use \utilities\Server;

class RoomsPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	protected $event;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new RoomsPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(),1));
		exit();
	}
	
	public function handleAjaxRequest()
	{
		$selectedRoomId = null;
		if(isset($_GET['selectedRoomId']))
		{
			$selectedRoomId = $_GET['selectedRoomId'];
		}
		if(isset($_POST['selectedRoomId']))
		{
			$selectedRoomId = $_POST['selectedRoomId'];
		}
		global $selectedRoom;
		if($selectedRoomId!=null)
		{
			$selectedRoom = Room::roomWithRoomId($selectedRoomId);
		}
		if(isset($_POST['modifyRoom']) && isset($selectedRoom))
		{
			if(!isset($_POST['name']) || empty($_POST['name']))
			{
				$selectedRoom->setName(null);
			}
			else
			{
				$selectedRoom->setName($_POST['name']);
			}
			if(isset($_POST['nbOfPlaces']))
			{
				$nbOfPlaces = (int)$_POST['nbOfPlaces'];
				if($nbOfPlaces>=$selectedRoom->getNbOfMembers())
				{
					$selectedRoom->setNbOfPlaces($nbOfPlaces);
				}
			}
			if(isset($_POST['roomNumber']))
			{
				$selectedRoom->setRoomNumber($_POST['roomNumber']);
			}
			$selectedRoom->save();
		}
		if(isset($_POST['addRooms']) && isset($_POST['nbOfPlaces']) && isset($_POST['nbOfRooms'])
				 && !empty($_POST['nbOfPlaces']) && !empty($_POST['nbOfRooms']))
		{
			$this->event->addRooms($_POST['nbOfPlaces'],$_POST['nbOfRooms']);
		}
		else if(isset($_POST['removeRooms']) && isset($_POST['nbOfPlaces']) && isset($_POST['nbOfRooms'])
				 && !empty($_POST['nbOfPlaces']) && !empty($_POST['nbOfRooms']))
		{
			$this->event->removeRooms($_POST['nbOfPlaces'],$_POST['nbOfRooms']);
		}
		return parent::handleAjaxRequest();
	}
	
	public function __construct($path=null,$title=null,$event=null)
	{
		if(!isset($path))
		{
			parent::__construct("rooms","JSP - Page d'administration des chambres");
	
			require_once ('classes/pages/admin/rooms/RoomsReveillonPage.php');
			require_once ('classes/pages/admin/rooms/RoomsWeekendPage.php');
	
			$this->addChild(\pages\admin\rooms\RoomsReveillonPage::getPage());
			$this->addChild(\pages\admin\rooms\RoomsWeekendPage::getPage());
		}
		else
		{
			parent::__construct($path,$title);
			$this->event = $event;
		}
	}
}

?>