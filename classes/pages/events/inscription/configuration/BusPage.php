<?php

namespace pages\events\inscription\configuration;

use exceptions\UserHasBusAlready;

use exceptions\BusIsFull;

use exceptions\NoSuchBus;

use exceptions\NotAllowed;

use exceptions\Failed;

use structures\User;

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

require_once 'classes/exceptions/NoSuchBus.php';
require_once 'classes/exceptions/BusIsFull.php';
require_once 'classes/exceptions/UserHasBusAlready.php';
require_once 'classes/exceptions/Failed.php';
require_once 'classes/exceptions/NotAllowed.php';

class BusPage extends AfterReservationPage {

	protected function getPageScriptPath() {
		return 'js/events/buses.js';
	}

	protected function getPageStylePath() {
		return 'css/events/buses.css';
	}

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration/bus.php';
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

		if(!$event->getAreBusesReady() && !$user->isAdmin())
		{
			return $res;
		}

		if(isset($_POST['setBus']))
		{

			$fields = array('busId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			try {
				try{
					Database::shared()->lockBusTables();
					Database::shared()->beginTransaction();

					$newBus = $event->getBusWithBusId($_POST['busId']);
					if(!$newBus)
					{
						throw new NoSuchBus();
					}

					$user->dropBusForEvent($event);
					$user->setBusForEvent($newBus,$event);

					Database::shared()->commit();
					Database::shared()->unlockBusTables();

					return $this->childWithName('success')->handleAjaxRequest();
				}
				catch(\Exception $e)
				{
					Database::shared()->rollBack();
					Database::shared()->unlockBusTables();
					throw $e;
				}
			}
			catch(NoSuchBus $e)
			{
				$res['no_such_bus']=true;
				return $res;
			}
			catch(BusIsFull $e)
			{
				$res['bus_is_full']=true;
				return $res;
			}
		}
		if(isset($_POST['unsetBus']))
		{

			$fields = array('busId' => 'positiveNumber');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['invalid'] = true;
				return $res;
			}

			$user->dropBusForEvent($event);

			return $this->childWithName('success')->handleAjaxRequest();
		}
		if(isset($_POST['setBusForOtherUser']))
		{
			$fields = array('busId' => 'positiveNumber',
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
					Database::shared()->lockBusTables();
					Database::shared()->beginTransaction();

					if($otherUser->hasBusForEvent($event))
					{
						throw new UserHasBusAlready();
					}

					$newBus = $event->getBusWithBusId($_POST['busId']);
					if(!$newBus)
					{
						throw new NoSuchBus();
					}

					$bus = $user->getBusForEvent($event);
					if($bus!=$newBus)
					{
						throw new NotAllowed();
					}

					$otherUser->setBusForEvent($newBus,$event);

					Database::shared()->commit();
					Database::shared()->unlockBusTables();

					$res['success'] = true;
					return $res;
				}
				catch(\Exception $e)
				{
					Database::shared()->rollBack();
					Database::shared()->unlockBusTables();
					throw $e;
				}
			}
			catch(NotAllowed $e)
			{
				$res['not_allowed']=true;
				return $res;
			}
			catch(NoSuchBus $e)
			{
				$res['no_such_bus']=true;
				return $res;
			}
			catch(BusIsFull $e)
			{
				$res['bus_is_full']=true;
				return $res;
			}
			catch(UserHasBusAlready $e)
			{
				$res['user_has_bus']=true;
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
		if(!$this->getEvent()->getAreBusesReady() || !$user->isOnMainListForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath()));
			exit();
		}
	}

}

?>