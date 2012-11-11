<?php

namespace pages\moncompte;

use structures\Session;

use utilities\Miscellaneous;

use utilities\FormValidator;

use nav\ExternalUserOnlyPage;

require_once 'classes/nav/ExternalUserOnlyPage.php';

require_once 'classes/utilities/FormValidator.php';
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/structures/Session.php';

class ExternalAccountPage extends ExternalUserOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ExternalAccountPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("ext","JSP - Gérer son compte");
	}
	
	public function handleAjaxRequest() {
		global $user;
		$res = array();
		$res['success']=false;
	
		if(isset($_POST['updateValue']))
		{
			if(isset($_POST['lastname']))
			{
				$fields = array('lastname' => 'name');
					
				$validator = new FormValidator($fields,array_keys($fields));
					
				if(!$validator->validate($_POST) || empty($_POST['lastname']))
				{
					$res['invalid_value']=true;
					return $res;
				}
	
				$lastname = $_POST['lastname'];
	
				$user->setLastName($lastname);
				$user->save();
				Session::setValueForKey('user', $user);
	
				$res['value']=$lastname;
				$res['success']=true;
				return $res;
			}
			else if(isset($_POST['firstname']))
			{
				$fields = array('firstname' => 'name');
					
				$validator = new FormValidator($fields,array_keys($fields));
					
				if(!$validator->validate($_POST) || empty($_POST['firstname']))
				{
					$res['invalid_value']=true;
					return $res;
				}
	
				$firstname = $_POST['firstname'];
	
				$user->setLastName($firstname);
				$user->save();
				Session::setValueForKey('user', $user);
	
				$res['value']=$firstname;
				$res['success']=true;
				return $res;
			}
		}
		
		return parent::handleAjaxRequest();
	}
}

?>