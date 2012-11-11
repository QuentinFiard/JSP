<?php

namespace structures;

require_once("classes/structures/Session.php");
require_once("classes/structures/SecurityLevel.php");

require_once 'classes/database/Database.php';

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
		return Session::getValueForKey('user');
	}
	
	public static function getProperties()
	{
		return array_keys(get_class_vars(get_class()));
	}
	
	public static function getPaymentProperties()
	{
		return array('userId','firstname','lastname','email');
	}
	
	// TODO - Insert your code here
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
		return !$this->isOnMainListForEvent($event);
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
		return Database::shared()->exitsReservationForUserAndEvent($this,$event);;
	}
	
	public function save()
	{
		Database::shared()->saveUser($this);
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

}

?>