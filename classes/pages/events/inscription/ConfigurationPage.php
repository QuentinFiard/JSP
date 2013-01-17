<?php

namespace pages\events\inscription;

use structures\events\WeekendJSP;

use database\Database;

use utilities\Server;

use nav\AfterReservationPage;

require_once 'classes/nav/AfterReservationPage.php';

require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/events/WeekendJSP.php';

require_once 'classes/utilities/Server.php';
require_once 'classes/database/Database.php';

class ConfigurationPage extends AfterReservationPage {

	protected function getPageScriptPath() {
		return 'js/events/configuration.js';
	}

	protected function getPageStylePath() {
		return 'css/events/configuration.css';
	}

	protected function getPageContentPath() {
		return 'pages/events/inscription/configuration.php';
	}
	
	public function getEvent() {
		return $this->getParent()->getEvent();
	}
	
	public function handleAjaxRequest() {
		global $currentPage;
		$currentPage = $this;
		
		$res = array();
		$res['success'] = false;
		$event = $this->getEvent();
		if(isset($_POST['updateOptions']))
		{
			global $user;
			if(!$user->hasToPayForEvent($this->getEvent()))
			{
				$res['user_has_already_paid'] = true;
				return $res;
			}

			$option_location = null;
			$has_location = false;
			$option_subvention = null;
			$has_subvention = false;
			$option_forfaits = null;
			$has_forfaits = false;
			$option_repas = null;
			$has_repas = false;
			
			$options = $user->getOptionsForEvent($this->getEvent());
			
			if(isset($_POST['location']))
			{
				$name = $_POST['location'];
					
				if($name != 'no' && !$user->arePersonalDataValid())
				{
					return $this->childWithName('personaldata')->handleAjaxRequest();
				}
				
				$pos = strpos($name,'location');
				if($name!= 'no' && ($pos===false))
				{
					$res['invalid_location_option'] = true;
					return $res;
				}
				
				if($name!= 'no')
				{
					if(!isset($_POST['rentalType']) || !in_array($_POST['rentalType'], array('ski','surf')))
					{
						$res['invalid_rental_type'] = true;
						return $res;
					}
					
					$pos = strpos($name,$_POST['rentalType']);
					if($pos===false)
					{
						$res['option_does_not_match_rental_type'] = true;
						return $res;
					}
				}
				
				$option_location = $this->getEvent()->getOptionWithName($name);
				
				if(!$option_location && $name!='no')
				{
					$res['no_such_rental'] = true;
					return $res;
				}
				
				$has_location = true;
			}
			if(isset($_POST['subvention']))
			{
				if((!$user->is2010() && !$user->is2011()) || $event->isGagnantPlace($user))
				{
					$res['user_not_granted'] = true;
					return $res;
				}
				
				$name = $_POST['subvention'];

				$pos1 = strpos($name,'2010');
				$pos2 = strpos($name,'2011');

				if($name!= 'none' && !(($pos1===false) ^ ($pos2 === false)))
				{
					$res['invalid_subvention'] = true;
					return $res;
				}
				if($name!= 'none' && (!($pos1===false) && !$user->is2010()))
				{
					$res['user_not_granted'] = true;
					return $res;
				}
				if($name!= 'none' && (!($pos2===false) && !$user->is2011()))
				{
					$res['user_not_granted'] = true;
					return $res;
				}
				
				$pos = strpos($name,'subvention');
				if($name!= 'none' && ($pos===false))
				{
					$res['invalid_subvention'] = true;
					return $res;
				}
				
				$option_subvention = $this->getEvent()->getOptionWithName($name);
				if(!$option_subvention && $name!='none')
				{
					$res['no_such_subvention'] = true;
					return $res;
				}
				
				$has_subvention = true;
			}
			if(isset($_POST['forfait']))
			{
				if($this->getEvent()==WeekendJSP::shared())
				{
					$res['invalid_event'] = true;
					return $res;
				}
				
				$name = $_POST['forfait'];
				
				$pos = strpos($name,'forfait');
				if($name!= 'default' && ($pos===false))
				{
					$res['invalid_option'] = true;
					return $res;
				}
				
				$option_forfaits = $this->getEvent()->getOptionWithName($name);
				
				if(!$option_forfaits && $name!='default')
				{
					$res['no_such_forfait'] = true;
					return $res;
				}
				
				$has_forfaits = true;
			}
			if(isset($_POST['repas']))
			{
				if($this->getEvent()==WeekendJSP::shared())
				{
					$res['invalid_event'] = true;
					return $res;
				}
				
				$name = $_POST['repas'];
				
				$pos = strpos($name,'food');
				if($name!= 'no' && ($pos===false))
				{
					$res['invalid_option'] = true;
					return $res;
				}
				
				$option_repas = $this->getEvent()->getOptionWithName($name);
				
				if(!$option_repas && $name!='no')
				{
					$res['no_such_option'] = true;
					return $res;
				}
				
				$has_repas = true;
			}
			
			foreach($options as $option)
			{
				if($has_location)
				{
					$pos = strpos($option->getName(),'location');
					if(!($pos===false))
					{
						$user->dropOption($option);
					}
				}
				if($has_subvention)
				{
					$pos = strpos($option->getName(),'subvention');
					if(!($pos===false))
					{
						$user->dropOption($option);
					}
				}
				if($has_forfaits)
				{
					$pos = strpos($option->getName(),'forfait');
					if(!($pos===false))
					{
						$user->dropOption($option);
					}
				}
				if($has_repas)
				{
					$pos = strpos($option->getName(),'food');
					if(!($pos===false))
					{
						$user->dropOption($option);
					}
				}
			}
			
			if($has_location && isset($option_location))
			{
				$user->addOption($option_location);
			}
			if($has_subvention && isset($option_subvention))
			{
				$user->addOption($option_subvention);
			}
			if($has_forfaits && isset($option_forfaits))
			{
				$user->addOption($option_forfaits);
			}
			if($has_repas && isset($option_repas))
			{
				$user->addOption($option_repas);
			}
			
			return $this->childWithName('success')->handleAjaxRequest();
		}
		return parent::handleAjaxRequest();
	}
}

?>