<?php

namespace pages;

use structures\Session;

use utilities\Miscellaneous;

use utilities\FormValidator;

require_once 'classes/nav/RegisteredOnlyPage.php';
require_once 'classes/pages/moncompte/ExternalAccountPage.php';
require_once 'classes/pages/moncompte/FrankizAccountPage.php';
require_once 'classes/pages/moncompte/ChangePasswordPage.php';

require_once 'classes/utilities/FormValidator.php';
require_once 'classes/utilities/Miscellaneous.php';
require_once 'classes/structures/Session.php';

use \nav\RegisteredOnlyPage;

class MyAccountPage extends RegisteredOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new MyAccountPage();
		}
		return self::$page;
	}
	
	public function __construct()
	{
		parent::__construct("moncompte","JSP - Gérer son compte");

		$this->addChild(\pages\moncompte\ExternalAccountPage::getPage());
		$this->addChild(\pages\moncompte\FrankizAccountPage::getPage());
		$this->addChild(\pages\moncompte\ChangePasswordPage::getPage());
	}
	
	protected function getPageContentPath() {
		global $user;
		if($user->isExt())
		{
			return $this->childWithName('ext')->getPageContentPath();
		}
		else
		{
			return $this->childWithName('frankiz')->getPageContentPath();
		}
	}

	public function handleAjaxRequest() {
		global $user;
		$res = array();
		$res['success']=false;
		
		if(isset($_POST['updateValue']))
		{
			if(isset($_POST['height']))
			{
				$fields = array('height' => 'positiveNumber');
					
				$validator = new FormValidator($fields,array_keys($fields));
					
				if(!$validator->validate($_POST))
				{
					$res['invalid_value']=true;
					return $res;
				}
				
				$height = round($_POST['height'])/100;
				if(!Miscellaneous::checkHeight($height))
				{
					$res['out_of_range']=true;
					return $res;
				}
				
				$user->setHeight($height);
				$user->save();
				Session::setValueForKey('user', $user);
				
				$res['value']=100*$height;
				$res['success']=true;
				return $res;
			}
			else if(isset($_POST['weight']))
			{
				$fields = array('weight' => 'positiveNumber');
					
				$validator = new FormValidator($fields,array_keys($fields));
					
				if(!$validator->validate($_POST))
				{
					$res['invalid_value']=true;
					return $res;
				}
			
				$weight = round($_POST['weight']);
				if(!Miscellaneous::checkWeight($weight))
				{
					$res['out_of_range']=true;
					return $res;
				}

				$user->setWeight($weight);
				$user->save();
				Session::setValueForKey('user', $user);
				
				$res['value']=$weight;
				$res['success']=true;
				return $res;
			}
			else if(isset($_POST['size']))
			{
				$fields = array('size' => 'positiveNumber');
					
				$validator = new FormValidator($fields,array_keys($fields));
					
				if(!$validator->validate($_POST))
				{
					$res['invalid_value']=true;
					return $res;
				}
					
				$size = $_POST['size'];
				if(!Miscellaneous::checkSize($size))
				{
					$res['out_of_range']=true;
					return $res;
				}
			
				$user->setSize($size);
				$user->save();
				Session::setValueForKey('user', $user);
				
				$res['value']=$size;
				$res['success']=true;
				return $res;
			}
			else
			{
				if($user->isExt())
				{
					$res = $this->childWithName('ext')->handleAjaxRequest();
				}
				else
				{
					$res = $this->childWithName('frankiz')->handleAjaxRequest();
				}
				return $res;
			}
		}
		else
		{
			$res = parent::handleAjaxRequest();
			ob_start();
			if($user->isExt())
			{
				$this->childWithName('ext')->includePageContent(true);
			}
			else
			{
				$this->childWithName('frankiz')->includePageContent(true);
			}
			$content = ob_get_clean();
			
			$res['content'] = $content;
			
			return $res;
		}
	}
}

?>