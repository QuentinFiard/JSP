<?php

namespace structures;

use utilities\Miscellaneous;

require_once("classes/structures/Session.php");
require_once("classes/structures/SecurityLevel.php");

require_once 'classes/database/Database.php';

require_once 'classes/utilities/Miscellaneous.php';

use \database\Database;

use \structures\Session;
use \process\SecurityLevel;

class User {
	protected $userId;
	
	private $firstname;
	private $lastname;
	private $email;
	
	private $male;
	private $weight;
	private $height;
	private $size;
	
	public static function currentUser()
	{
		$userId = Session::getValueForKey('userId');
		if(!$userId)
		{
			return null;
		}
		return self::userWithUserId($userId);
	}
	
	public static function getProperties()
	{
		return array_keys(get_class_vars(get_class()));
	}
	
	public static function getPaymentProperties()
	{
		return array('userId','firstname','lastname','email');
	}
	
	public function __construct($data) {
		$this->updateWithData($data,true);
	}
	
	public static function userWithUserId($userId)
	{
		return Database::shared()->getUserWithUserId($userId);
	}
	
	public function updateWithData($data,$constructor=false)
	{
		$properties = get_class_vars(get_class());
		foreach($properties as $key => $default_value)
		{
			if(array_key_exists($key, $data))
			{
				$this->$key = $data[$key];
			}
			else if($constructor)
			{
				$this->$key = null;
			}
		}
	}
	
	public function isFrankizUser()
	{
		return false;
	}
	
	public function isX()
	{
		return false;
	}
	
	function isAdherentKes()
	{
		return false;
	}
	
	public function isExt()
	{
		return true;
	}
	
	public function displayName()
	{
		return $this->firstname;
	}
	
	public function getFullName()
	{
		return $this->firstname.' '.$this->lastname;
	}
	
	public function __toString()
	{
		$res = "<User>";
		$properties = get_class_vars(get_class());
		foreach($properties as $key => $default_value)
		{
			$res .= "<br/>    <".$key.">".$this->$key.'</'.$key.">";
		}
		$res .= "<br/></User>";
		return $res;
	}

	public function isRegistered()
	{
		return $this->userId!=null;
	}
	
	public function isMember()
	{
		return false;
	}
	
	public function isAdmin()
	{
		return false;
	}
	
	public function hasGender()
	{
		return $this->male != null;
	}
	
	public function isMale()
	{
		return $this->male;
	}
	
	public function is2010()
	{
		return false;
	}
	
	public function is2011()
	{
		return false;
	}
	
	public function getUserId() {
		return $this->userId;
	}

	public function setUserId($userId) {
		$this->userId = $userId;
	}
	
	/*
	 * Property by name access
	 */
	
	public function getProperty($key)
	{
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}
	
	/*
	 * Database
	 */
	
	public function isOnMainListForEvent($event)
	{
		return $this->hasReservationForEvent($event) && Database::shared()->isUserOnMainListForEvent($this,$event);
	}
	
	public function isOnWaitingListForEvent($event)
	{
		return $this->hasReservationForEvent($event) && !$this->isOnMainListForEvent($event);
	}
	
	public function getReservationForEvent($event)
	{
		return Database::shared()->getReservationForUserAndEvent($this,$event);
	}
	
	public function isReservationCompleteForEvent($event)
	{
		$reservation = $this->getReservationForEvent($event);
		return isset($reservation) && $reservation['hasPaid'];
	}
	
	public function hasToPayForEvent($event)
	{
		$reservation = $this->getReservationForEvent($event);
		return isset($reservation) && !$reservation['hasPaid'];
	}
	
	public function hasReservationForEvent($event)
	{
		return Database::shared()->existsReservationForUserAndEvent($this,$event);;
	}
	
	public function getRoomForEvent($event)
	{
		return Database::shared()->getRoomForUserAndEvent($this,$event);
	}
	
	public function hasRoomForEvent($event)
	{
		return Database::shared()->existsRoomForUserAndEvent($this,$event);
	}
	
	public function dropRoomForEvent($event)
	{
		Database::shared()->dropRoomForUserAndEvent($this,$event);
	}
	
	public function setRoomForEvent($room,$event)
	{
		Database::shared()->setRoomForUserAndEvent($room,$this,$event);
	}
	
	public function getBusForEvent($event)
	{
		return Database::shared()->getBusForUserAndEvent($this,$event);
	}
	
	public function hasBusForEvent($event)
	{
		return Database::shared()->existsBusForUserAndEvent($this,$event);
	}
	
	public function dropBusForEvent($event)
	{
		Database::shared()->dropBusForUserAndEvent($this,$event);
	}
	
	public function setBusForEvent($bus,$event)
	{
		Database::shared()->setBusForUserAndEvent($bus,$this,$event);
	}
	
	public function getOptionsForEvent($event)
	{
		return Database::shared()->getOptionsForUserAndEvent($this,$event);
	}
	
	public function getOptionWithNameLikeForEvent($name,$event)
	{
		return Database::shared()->getOptionWithNameLikeForUserAndEvent($name,$this,$event);
	}
	
	public function hasOption($option)
	{
		return Database::shared()->userHasOption($this,$option);
	}
	
	public function removeOptionsForEvent($event)
	{
		Database::shared()->removeOptionsForUserAndEvent($this,$event);
	}
	
	public function addOption($option)
	{
		Database::shared()->addOptionForUser($option,$this);
	}
	
	public function dropOption($option)
	{
		Database::shared()->dropOptionForUser($option,$this);
	}
	
	public function cancelReservationForEvent($event)
	{
		Database::shared()->cancelReservationForUserAndEvent($this,$event);
	}
	
	public function arePersonalDataValid()
	{
		return ($this->weight!=null && Miscellaneous::checkWeight($this->weight))
			&& ($this->height!=null && Miscellaneous::checkHeight($this->height))
			&& ($this->size!=null && Miscellaneous::checkSize($this->size));
	}
	
	public function switchToMainListForEvent($event)
	{
		Database::shared()->switchUserToMainListForEvent($this,$event);
		$event->sendSwitchToMainListEmailToUser($this);
	}
	
	public function save()
	{
		Database::shared()->saveUser($this);
	}
	
	public function isNonCotisant()
	{
		return true;
	}
	
	public function getFirstname() {
		return $this->firstname;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getMale() {
		return $this->male;
	}

	public function getWeight() {
		return $this->weight;
	}

	public function getHeight() {
		return $this->height;
	}

	public function getSize() {
		return $this->size;
	}

	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setMale($male) {
		$this->male = $male;
	}

	public function setWeight($weight) {
		$this->weight = $weight;
	}

	public function setHeight($height) {
		$this->height = $height;
	}

	public function setSize($size) {
		$this->size = $size;
	}
	
	static public function cmp($user1,$user2)
	{
		$res = strcasecmp($user1->getLastname(), $user2->getLastname());
		if($res==0)
		{
			$res = strcasecmp($user1->getFirstname(), $user2->getFirstname());
		}
		return $res;
	}

	public function isCotisant() {
		return false;
	}
	
	public function isCadreX()
	{
		return false;
	}
	
	public function hasRentalForEvent($event)
	{
	    return ($this->getRentalForEvent($event)!=null);
	}
	
	public function getRentalForEvent($event)
	{
	    $options = $this->getOptionsForEvent($event);
	    foreach($options as $option)
	    {
	        if($option->getIsRental())
	        {
	            return $option;
	        }
	    }
	    return null;
	}

}

?>