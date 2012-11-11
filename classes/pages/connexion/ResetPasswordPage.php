<?php

namespace pages\connexion;

use nav\UnregisteredOnlyPage;

use database\Database;

use utilities\FormValidator;

require_once ('classes/nav/UnregisteredOnlyPage.php');

require_once 'securimage/securimage.php';

require_once 'classes/pages/connexion/resetpassword/SuccessPage.php';
require_once 'classes/utilities/FormValidator.php';

class ResetPasswordPage extends UnregisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new ResetPasswordPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("resetpassword","JSP - Mot de passe oublié");
		
		$this->addChild(SuccessPage::getPage());
	}
	
	public function handleAjaxRequest() {
		if(isset($_POST['confirmForgottenPassword']))
		{
			$res = array();
			$res['success']=false;
			
			$securimage = new \Securimage();
			if ($securimage->check($_POST['captcha_code']) == false) {
				$res['wrong_captcha'] = true;
				return $res;
			}
			
			$fields = array('email' => 'email');
			
			$validator = new FormValidator($fields,array_keys($fields));
			
			if(!$validator->validate($_POST))
			{
				$res['invalid_email']=true;
				return $res;
			}
			
			$email = strtolower($_POST['email']);
			
			$user = Database::shared()->getExternalUserWithEmail($email);
			
			if(!$user)
			{
				$res['no_such_user'] = true;
				return $res;
			}
			
			$user->resetPassword();
			
			return $this->childWithName('success')->handleAjaxRequest();
		}
		else
		{
			return parent::handleAjaxRequest();
		}
	}

}

?>