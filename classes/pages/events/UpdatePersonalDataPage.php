<?php

namespace pages\events;

use utilities\Server;

use structures\Session;

use utilities\FormValidator;

use utilities\Miscellaneous;

use nav\AfterReservationPage;

require_once ('classes/nav/AfterReservationPage.php');
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/utilities/FormValidator.php';
require_once 'classes/utilities/Server.php';

require_once 'classes/structures/Session.php';

class UpdatePersonalDataPage extends AfterReservationPage {
	private static $page = null;

	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new UpdatePersonalDataPage();
		}
		return self::$page;
	}
	
	public function checkSecurityGrant() {
		parent::checkSecurityGrant();
		
		$optionName = $_POST['location'];
		
		$event = $this->getEvent();
		$option = $event->getOptionWithName($optionName);
		
		if(!$option)
		{
			header('Location: '.Server::getServerRoot().substr($this->getParent()->getPath(),1));
			exit();
		}
	}

	protected function getPageScriptPath() {
		return 'js/events/personaldata.js';
	}

	protected function getPageContentPath() {
		return 'pages/events/personaldata.php';
	}

	protected function getPageStylePath() {
		return 'css/events/personaldata.css';
	}

	function getEvent() {
		return $this->getParent()->getEvent();
	}

	public function __construct()
	{
		parent::__construct("personaldata","JSP - Veuillez compléter les données nécessaires pour la location de matériel.");
	}
	
	public function handleAjaxRequest() {
		global $currentPage;
		$currentPage = $this;
		global $user;
		
		if($user->arePersonalDataValid())
		{
			return $this->getParent()->handleAjaxRequest();
		}
		
		if(isset($_POST['updateData']))
		{
			$res = array();
			$res['success'] = false;
			if(    !isset($_POST['weight']) || !isset($_POST['height']) || !isset($_POST['size']))
			{
				$res['missing_fields'] = true;
				return $res;
			}
			
			$fields = array('height' => 'positiveNumber',
							'weight' => 'positiveNumber',
							'size'   => 'positiveNumber');
			
			$validator = new FormValidator($fields,array_keys($fields));
			
			if(!$validator->validate($_POST))
			{
				$res['invalid_values']=true;
				return $res;
			}
			
			$height = round($_POST['height'])/100;
			$weight = round($_POST['weight']);
			$size = (float)$_POST['size'];
			
			if(    !Miscellaneous::checkHeight($height)
				|| !Miscellaneous::checkWeight($weight)
				|| !Miscellaneous::checkSize($size) )
			{
				$res['out_of_range'] = true;
				return $res;
			}
	
			$user->setHeight($height);
			$user->setWeight($weight);
			$user->setSize($size);
			$user->save();
			
			return $this->getParent()->handleAjaxRequest();
		}
		$res = parent::handleAjaxRequest();
		$res['more_data_needed']=true;
		return $res;
	}

}

?>