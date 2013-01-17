<?php

namespace pages\connexion\exterieurs;

use pages\connexion\exterieurs\creercompte\SuccessPage;

use nav\UnregisteredOnlyPage;

use exceptions\EmailAlreadyExists;

use utilities\Miscellaneous;

use database\Database;

use utilities\FormValidator;

require_once ('classes/nav/UnregisteredOnlyPage.php');

require_once 'classes/utilities/FormValidator.php';
require_once 'classes/database/Database.php';

require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/exceptions/EmailAlreadyExists.php';

require_once 'classes/pages/connexion/exterieurs/creercompte/SuccessPage.php';
require_once 'securimage/securimage.php';

class CreateAccountPage extends  UnregisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new CreateAccountPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("creercompte","JSP - Création d'un nouveau compte");
		
		$this->addChild(SuccessPage::getPage());
	}
	
	public function isCreateNewAccountPage()
	{
		return true;
	}
	
	
	
	protected function getPageContentPath() {
		return $this->getParent()->getPageContentPath();
	}

	protected function getPageScriptPath() {
		return $this->getParent()->getPageScriptPath();
	}

	protected function getPageStylePath() {
		return $this->getParent()->getPageStylePath();
	}

	public function handleAjaxRequest() {
		if(isset($_POST['createAccount']))
		{
			$res = array();
			$res['success'] = false;
			
			$securimage = new \Securimage();
			if ($securimage->check($_POST['captcha_code']) == false) {
				$res['wrong_captcha'] = true;
				return $res;
			}
			
			if(!isset($_POST['mail']) || !isset($_POST['sha'])
					|| ($_POST['sha']!='true' && (!isset($_POST['password']) || !isset($_POST['passwordConfirm'])))
					|| ($_POST['sha']=='true' && !isset($_POST['digest']))
					|| !isset($_POST['lastname'])
					|| !isset($_POST['firstname']))
			{
				$res['missing_fields']=true;
				return $res;
			}
			
			$fields = array('mail' => 'email');
			
			$validator = new FormValidator($fields,array_keys($fields));
			
			if(!$validator->validate($_POST))
			{
				$res['wrong_email_format']=true;
				return $res;
			}
			
			$fields = array('firstname' => 'name',
					'lastname' => 'name');
			
			$validator = new FormValidator($fields,array_keys($fields));
			
			if(!$validator->validate($_POST))
			{
				$res['invalid_names']=true;
				return $res;
			}
			
			if($_POST['sha']!='true' && $_POST['password']!=$_POST['passwordConfirm'])
			{
				$res['password_match_error']=true;
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
			
			try {
				Database::shared()->addExternalUserWithEmailPasswordAndData($email,$digest,$_POST);
			} catch (EmailAlreadyExists $e) {
				$res['email_already_exists']=true;
				return $res;
			}
			
			$user = Database::shared()->getExternalUserWithEmail($email);


			if(isset($_POST['isCadreX']))
			{
				$user->sendValidationEmailForCadre();
			}
			else
			{
				$user->sendValidationEmail();
			}
			
			
			return $this->childWithName('success')->handleAjaxRequest();
		}
		else
		{
			return parent::handleAjaxRequest();
		}
	}

}

?>