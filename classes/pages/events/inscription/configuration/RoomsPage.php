<?php

namespace pages\events\inscription\configuration;

use exceptions\NotAllowed;

use exceptions\Failed;

use exceptions\UserHasRoomAlready;

use structures\User;

use exceptions\RoomIsFull;

use exceptions\NoSuchRoom;

use utilities\FormValidator;

use structures\events\WeekendJSP;

use database\Database;

use utilities\Server;

use nav\AfterReservationPage;

require_once 'classes/nav/AfterReservationPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/User.php';

require_once 'classes/utilities/Server.php';
require_once 'classes/database/Database.php';

require_once 'classes/utilities/FormValidator.php';

require_once 'classes/exceptions/NoSuchRoom.php';
require_once 'classes/exceptions/RoomIsFull.php';
require_once 'classes/exceptions/UserHasRoomAlready.php';
require_once 'classes/exceptions/Failed.php';
require_once 'classes/exceptions/NotAllowed.php';

class RoomsPage extends AfterReservationPage {

	protected function getPageScriptPath() {
		return 'js/events/rooms.js';
	}

	protected function getPageStylePath() {
		return 'css/events/rooms.css';
	}

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration/rooms.php';
	}

	public function getEvent() {
		return $this->getParent()->getEvent();
	}



	public function handleAjaxRequest() {
		global $currentPage;
		$currentPage = $this;
		global $user;
		$event = $this->getEvent();

		$res = array();
		$res['success'] = false;

		/* Fin des inscriptions */
		$res['closed']=true;
		return $res;
		/* End fin des inscriptions */

		if(!$event->getAreRoomsReady() && !$user->isAdmin())
		{
			return $res;
		}

		if(isset($_POST['setRoom']))
		{

			$fields = array('roomId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			try {
				try{
					Database::shared()->lockRoomTables();
					Database::shared()->beginTransaction();

					$newRoom = $event->getRoomWithRoomId($_POST['roomId']);
					if(!$newRoom)
					{
						throw new NoSuchRoom();
					}

					$user->dropRoomForEvent($event);
					$user->setRoomForEvent($newRoom,$event);

					Database::shared()->commit();
					Database::shared()->unlockRoomTables();

					return $this->childWithName('success')->handleAjaxRequest();
				}
				catch(\Exception $e)
				{
					Database::shared()->rollBack();
					Database::shared()->unlockRoomTables();
					throw $e;
				}
			}
			catch(NoSuchRoom $e)
			{
				$res['no_such_room']=true;
				return $res;
			}
			catch(RoomIsFull $e)
			{
				$res['room_is_full']=true;
				return $res;
			}
		}
		if(isset($_POST['unsetRoom']))
		{

			$fields = array('roomId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			$user->dropRoomForEvent($event);

			return $this->childWithName('success')->handleAjaxRequest();
		}
		if(isset($_POST['setRoomForOtherUser']))
		{
			$fields = array('roomId' => 'positiveNumber',
							'userId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			$otherUser = User::userWithUserId($_POST['userId']);
			if(!$otherUser)
			{
				$res['no_such_user'] = true;
				return $res;
			}

			try {
				try{
					Database::shared()->lockRoomTables();
					Database::shared()->beginTransaction();

					if($otherUser->hasRoomForEvent($event))
					{
						throw new UserHasRoomAlready();
					}

					$newRoom = $event->getRoomWithRoomId($_POST['roomId']);
					if(!$newRoom)
					{
						throw new NoSuchRoom();
					}

					$room = $user->getRoomForEvent($event);
					if($room!=$newRoom)
					{
						throw new NotAllowed();
					}

					$otherUser->setRoomForEvent($newRoom,$event);

					Database::shared()->commit();
					Database::shared()->unlockRoomTables();

					$res['success'] = true;
					return $res;
				}
				catch(\Exception $e)
				{
					Database::shared()->rollBack();
					Database::shared()->unlockRoomTables();
					throw $e;
				}
			}
			catch(NotAllowed $e)
			{
				$res['not_allowed']=true;
				return $res;
			}
			catch(NoSuchRoom $e)
			{
				$res['no_such_room']=true;
				return $res;
			}
			catch(RoomIsFull $e)
			{
				$res['room_is_full']=true;
				return $res;
			}
			catch(UserHasRoomAlready $e)
			{
				$res['user_has_room']=true;
				return $res;
			}
		}
		if(isset($_POST['setRoomName']))
		{
			$fields = array('roomId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			$name = null;
			if(isset($_POST['name']) && !empty($_POST['name']))
			{
				$name = $_POST['name'];
			}

			try {
				try{
					Database::shared()->lockRoomTables();
					Database::shared()->beginTransaction();

					$newRoom = $event->getRoomWithRoomId($_POST['roomId']);
					if(!$newRoom)
					{
						throw new NoSuchRoom();
					}

					$room = $user->getRoomForEvent($event);
					if($room!=$newRoom)
					{
						throw new NotAllowed();
					}

					$room->setName($name);

					Database::shared()->commit();
					Database::shared()->unlockRoomTables();

					return $this->childWithName('success')->handleAjaxRequest();
				}
				catch(\Exception $e)
				{
					Database::shared()->rollBack();
					Database::shared()->unlockRoomTables();
					throw $e;
				}
			}
			catch(NoSuchRoom $e)
			{
				$res['no_such_room']=true;
				return $res;
			}
			catch(NotAllowed $e)
			{
				$res['not_allowed']=true;
				return $res;
			}
		}
		return parent::handleAjaxRequest();
	}

	public function checkSecurityGrant() {
		global $user;
		if(isset($user) && $user->isMember())
		{
			return;
		}
		parent::checkSecurityGrant();
		if(!$this->getEvent()->getAreRoomsReady() || !$user->isOnMainListForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath()));
			exit();
		}
	}

}

?>