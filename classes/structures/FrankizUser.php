<?php

namespace structures;

use process\SecurityLevel;

require_once ('classes/structures/User.php');
require_once 'classes/database/Database.php';

use \database\Database;

use structures\User;

class FrankizUser extends User {
	private $uid;
	private $nickname;
	private $hruid;
	private $sport;
	private $class = null;
	private $isX_ = null;
	
	protected $securityLevel;

	public function __construct($data) {
		parent::__construct($data);
	}

	public function updateWithData($data,$constructor=false)
	{
		parent::updateWithData($data,$constructor);
		$properties = get_class_vars(get_class());
		foreach($properties as $key => $default_value)
		{
			if(array_key_exists($key, $data))
			{
				if($key=='securityLevel' && is_int($data[$key]))
				{
					$this->$key = SecurityLevel::levelWithLevel($data[$key]);
				}
				else if($key=='isX_')
				{
					$this->$key = ($data[$key]=='1');
				}
				else
				{
					$this->$key = $data[$key];
				}
			}
			else if($constructor)
			{
				$this->$key = null;
			}
		}
		if(array_key_exists('promos', $data))
		{
			$this->isX_ = false;
			foreach($data['promos'] as $promo)
			{
				$matches = array();
				if (preg_match('/^([a-z_]+)([1-9][0-9]{3})$/', $promo, $matches)) {
					$year = (integer)$matches[2];
					if (!$this->class || $year > $this->class)
					{
						$this->class = $promo;
						$this->isX_ = ($matches[1]=="x");
					}
				}
			}
		}
	}
	
	public static function getProperties()
	{
		return array_keys(get_class_vars(get_class()));
	}
	
	public function isFrankizUser()
	{
		return true;
	}

	public function isRegistered()
	{
		return $this->securityLevel->isRegistered();
	}
	
	public function isMember()
	{
		return $this->securityLevel->isMember();
	}
	
	public function isAdmin()
	{
		return $this->securityLevel->isAdmin();
	}
	
	public function isX()
	{
		return $this->isX_;
	}

	public function isAdherentKes() {
		return $this->isX() && (in_array(strtolower($this->class), array('x2010','x2011')));
	}

	public function isExt() {
		return false;
	}

	public function displayName()
	{
		if(isset($this->nickname) && $this->nickname != "")
		{
			return $this->nickname;
		}
		return parent::displayName();
	}
	
	/*
	 * Property by name access
	 */
	
	public function getProperty($key)
	{
		$res = parent::getProperty($key);
		if($res != null)
		{
			return $res;
		}
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}
	
	public function save()
	{
		Database::shared()->saveFrankizUser($this);
	}
	
	public function getNickname() {
		return $this->nickname;
	}

	public function getClass() {
		return $this->class;
	}

	public function getSport() {
		return $this->sport;
	}
	
	public function is2010()
	{
		return strtolower($this->class) == 'x2010';
	}
}

?>