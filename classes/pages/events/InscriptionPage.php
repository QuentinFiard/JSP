<?php

namespace pages\events;

use database\Database;

use utilities\Server;

use nav\AfterReservationStartPage;

require_once 'classes/nav/AfterReservationStartPage.php';

require_once 'classes/utilities/Server.php';
require_once 'classes/database/Database.php';

class InscriptionPage extends AfterReservationStartPage {
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		global $user;
		if($user->hasReservationForEvent($this->getEvent()))
		{
			header('Location: '.Server::getServerRoot().substr($this->childWithName('configuration')->getPath(), 1));
			exit();
		}
	}

	protected function getPageScriptPath() {
		return 'js/events/inscription.js';
	}

	protected function getPageStylePath() {
		return 'css/events/inscription.css';
	}
	
	public function getEvent() {
		return $this->getParent()->getEvent();
	}
	
	public function handleAjaxRequest() {
		$res = array();
		$res['success']=false;
		if(isset($_POST['confirmReservation']))
		{
			$event = $this->getEvent();
			if($event->haveReservationsStopped())
			{
				$res['reservation_closed'] = true;
				return $res;
			}
			if(!isset($_POST['conditions_agreement']))
			{
				$res['must_agree']=true;
				return $res;
			}
			
			global $user;
			
			Database::shared()->lockAddReservationTables();
			Database::shared()->addReservationForUserAndEvent($user,$event);

			$isOnMainList = $user->isOnMainListForEvent($event);
			
			Database::shared()->unlockTables();
			
			if($event->isGagnantPlace($user))
			{
				$option = $event->getOptionWithName('lotery_winner');
				$user->addOption($option);
			}
			
			if($isOnMainList)
			{
				$event->sendConfirmationEmailToUser($user,false);
				return $this->childWithName('mainlist')->handleAjaxRequest();
			}
			else
			{
				$event->sendConfirmationEmailToUser($user,true);
				return $this->childWithName('waitinglist')->handleAjaxRequest();
			}
		}
		return parent::handleAjaxRequest();
	}
	
}

?>