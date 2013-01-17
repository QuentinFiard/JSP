<?php

namespace pages\moncompte;

use utilities\Miscellaneous;

use database\Database;

use utilities\FormValidator;

use pages\moncompte\changepassword\SuccessPage;

use nav\ExternalUserOnlyPage;

require_once 'classes/nav/ExternalUserOnlyPage.php';
require_once 'classes/pages/moncompte/changepassword/SuccessPage.php';
require_once 'classes/utilities/FormValidator.php';
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/database/Database.php';

class ChangePasswordPage extends ExternalUserOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ChangePasswordPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("changepassword","JSP - Changer de mot de passe");
		
		$this->addChild(SuccessPage::getPage());
	}
	
	public function handleAjaxRequest() {
		if(isset($_POST['changePassword']))
		{
			$res = array();
			$res['success']=false;
			
			if(!isset($_POST['sha']) || !isset($_POST['sha_old'])
					|| ($_POST['sha']!='true' && (!isset($_POST['password']) || !isset($_POST['passwordConfirm'])))
					|| ($_POST['sha']=='true' && !isset($_POST['digest']))
					|| ($_POST['sha_old']!='true' && !isset($_POST['old_password']))
					|| ($_POST['sha_old']=='true' && !isset($_POST['digest_old'])))
			{
				$res['missing_fields']=true;
				return $res;
			}
			
			$digest = null;
				
			if($_POST['sha']!='true')
			{
				if($_POST['password']!=$_POST['passwordConfirm'])
				{
					$res['password_match_error'] = true;
					return $res;
				}
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

			$digest_old = null;
			
			if($_POST['sha_old']!='true')
			{
				$digest_old = hash('sha256',$_POST['old_password'],true);
			}
			else
			{
				$digest_old = $_POST['digest_old'];
				if(!Miscellaneous::isValidDigest($digest_old))
				{
					return $res;
				}
				$digest_old = Miscellaneous::hex2bin($digest_old);
			}
			
			global $user;
			
			$user = Database::shared()->getExternalUserWithEmailAndPassword($user->getEmail(),$digest_old);
			
			if(!$user)
			{
				$res['invalid_password']=true;
				return $res;
			}
			
			Database::shared()->setPasswordForExternalUser($user,$digest);
				
			return $this->childWithName('success')->handleAjaxRequest();
		}
		else
		{
			return parent::handleAjaxRequest();
		}
	}
}

?>