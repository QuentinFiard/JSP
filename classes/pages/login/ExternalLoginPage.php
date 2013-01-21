<?php

namespace pages\login;

use nav\Page;

use utilities\Miscellaneous;

use structures\Session;

use database\Database;

use utilities\FormValidator;

require_once 'classes/nav/Page.php';
require_once 'classes/utilities/FormValidator.php';
require_once 'classes/database/Database.php';
require_once 'classes/structures/Session.php';

require_once 'classes/utilities/Miscellaneous.php';

use nav\LeafPage;

class ExternalLoginPage extends Page {
	private static $page = null;

	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ExternalLoginPage();
		}
		return self::$page;
	}

	public function __construct()
	{
		parent::__construct("ext","External login");
	}

	public function handleAjaxRequest() {
		$res = array();
		$res['success'] = false;
		if(isset($_POST['login']))
		{
			if(!isset($_POST['mail']) || !isset($_POST['sha'])
					 || ($_POST['sha']!='true' && !isset($_POST['password']))
					 || ($_POST['sha']=='true' && !isset($_POST['digest'])))
			{
				return $res;
			}

			$fields = array('mail' => 'email');

			$validator = new FormValidator($fields,array_keys($fields));

			if(!$validator->validate($_POST))
			{
				$res['wrong_email_format']=true;
				return $res;
			}

			$email = $_POST['mail'];
			$email = strtolower($email);

			$digest = null;

			if($_POST['sha']!='true')
			{
				$digest = hash('sha256',$_POST['password'],true);
			}
			else
			{
				$digest = $_POST['digest'];
				if(!Miscellaneous::isValidDigest($digest))
				{
					return $res;
				}
				$digest = Miscellaneous::hex2bin($digest);
			}

			$user = Database::shared()->getExternalUserWithEmailAndPassword($email,$digest);

			if(!$user)
			{
				$res['no_such_user'] = true;
				return $res;
			}

			Session::setValueForKey('userId', $user->getUserId());

			$res['success']=true;

			return $res;
		}
		return $res;
	}

}

?>