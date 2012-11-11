<?php

namespace structures;
	
require_once 'classes/database/Database.php';

use database\Database;

class Room {
	private $roomId;
	private $event;
	private $eventId;
	private $name;
	private $nbOfPlaces;
	private $roomNumber;
	private $members = null;

	public function __construct($event,$row)
	{
		$this->event = $event;
		$this->eventId = $event->getEventId();
		$properties = self::getProperties();
		foreach($properties as $key)
		{
			if(array_key_exists($key, $row))
			{
				$this->$key = $row[$key];
			}
			else
			{
				$this->$key = null;
			}
		}
	}

	public static function getProperties()
	{
		return array("roomId","eventId","name","nbOfPlaces","roomNumber");
	}
	
	public function getProperty($key)
	{
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}
	
	public static function roomWithRoomId($roomId)
	{
		return Database::shared()->getRoomWithRoomId($roomId);
	}
	
	public function getRoomId() {
		return $this->roomId;
	}

	public function getEvent() {
		return $this->event;
	}

	public function getName() {
		return $this->name;
	}

	public function getNbOfPlaces() {
		return $this->nbOfPlaces;
	}

	public function getRoomNumber() {
		return $this->roomNumber;
	}

	public function getMembers() {
		if(!isset($this->members))
		{
			$this->members = Database::shared()->getMembersForRoom($this);
		}
		return $this->members;
	}
	
	public function getNbOfMembers()
	{
		if(isset($this->members))
		{
			return count($this->members);
		}
		return Database::shared()->getNbOfMembersForRoom($this);
	}
	
	public function setName($name) {
		$this->name = $name;
	}

	public function setNbOfPlaces($nbOfPlaces) {
		$this->nbOfPlaces = $nbOfPlaces;
	}

	public function setRoomNumber($roomNumber) {
		$this->roomNumber = $roomNumber;
	}
	
	public function setRoomId($roomId) {
		$this->roomId = $roomId;
	}
	
	public function save()
	{
		Database::shared()->saveRoom($this);
	}

}

?>