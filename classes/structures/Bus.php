<?php

namespace structures;

require_once 'classes/database/Database.php';

use database\Database;

class Bus {
	private $busId;
	private $event;
	private $eventId;
	private $name;
	private $nbOfPlaces;
	private $busNumber;
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
		return array("busId","eventId","name","nbOfPlaces","busNumber");
	}

	public function getProperty($key)
	{
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}

	public static function busWithBusId($busId)
	{
		return Database::shared()->getBusWithBusId($busId);
	}

	public function getBusId() {
		return $this->busId;
	}

	public function getEventId() {
		return $this->eventId;
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

	public function getBusNumber() {
		return $this->busNumber;
	}

	public function getMembers() {
		if(!isset($this->members))
		{
			$this->members = Database::shared()->getMembersForBus($this);
		}
		return $this->members;
	}

	public function getNbOfMembers()
	{
		if(isset($this->members))
		{
			return count($this->members);
		}
		return Database::shared()->getNbOfMembersForBus($this);
	}

	public function setNbOfPlaces($nbOfPlaces) {
		$this->nbOfPlaces = $nbOfPlaces;
	}

	public function setBusNumber($busNumber) {
		$this->busNumber = $busNumber;
	}

	public function setBusId($busId) {
		$this->busId = $busId;
	}

	public function save()
	{
		Database::shared()->saveBus($this);
	}

    public function getNbOfPlacesLeft()
    {
        return $this->getNbOfPlaces()-$this->getNbOfMembers();
    }

}

?>