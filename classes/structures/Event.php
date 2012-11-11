<?php

namespace structures {
	
use structures\events\WeekendJSP;

use structures\events\SemaineReveillon;

require_once 'classes/database/Database.php';

use database\Database;

abstract class Event {
	private $eventId;
	private $name;
	private $start;
	private $end;
	private $price_x;
	private $price_ext;
	private $caution;
	private $areRoomsReady;
	private $reservationStart;
	private $nbOfPlaces;
	
	private $rooms = null;

	public static function eventWithEventId($eventId)
	{
		switch($eventId)
		{
			case 1:
				return SemaineReveillon::shared();
			case 2:
				return WeekendJSP::shared();
		}
		return null;
	}

	protected function __construct($eventId)
	{
		$this->eventId = $eventId;
		$row = Database::shared()->getRowForEventWithId($eventId);
		if($row)
		{
			$properties = self::getProperties();
			foreach($properties as $key)
			{
				if(array_key_exists($key, $row))
				{
					if($key=='start' || $key=='end' || $key=='reservationStart')
					{
						$this->$key = strtotime($row[$key]);
					}
					else
					{
						$this->$key = $row[$key];
					}
				}
				else
				{
					$this->$key = null;
				}
			}
		}
	}
	
	public static function getProperties()
	{
		return array_keys(get_class_vars(get_class()));
	}
	
	abstract public function getPage();
	
	public function getPagePath()
	{
		return $this->getPage()->getPath();
	}
	
	public function getEventId() {
		return $this->eventId;
	}
	
	public function priceForUser($user)
	{
		if($user->isAdherentKes())
		{
			return $this->price_x;
		}
		return $this->price_ext;
	}
	
	public function cautionForUser($user)
	{
		return $this->caution;
	}
	
	public function getName() {
		return $this->name;
	}

	public function getStart() {
		return $this->start;
	}

	public function getEnd() {
		return $this->end;
	}
	
	public function getRooms() {
		if(!isset($this->rooms))
		{
			$this->rooms = Database::shared()->getRoomsForEvent($this);
		}
		return $this->rooms;
	}
	
	public function getOptions() {
		return Database::shared()->optionsForEvent($this);
	}
	
	public function getRoomReport()
	{
		return Database::shared()->getRoomReportForEvent($this);
	}
	
	public function addRooms($nbOfPlaces,$nbOfRooms)
	{
		Database::shared()->addRoomsForEvent($this,$nbOfPlaces,$nbOfRooms);
	}
	
	public function removeRooms($nbOfPlaces,$nbOfRooms)
	{
		Database::shared()->removeRoomsForEvent($this,$nbOfPlaces,$nbOfRooms);
	}
	
	public function getNbOfPlaces()
	{
		if($this->areRoomsReady)
		{
			return Database::shared()->getNbOfPlacesForEvent($this);
		}
		else
		{
			return $this->nbOfPlaces;
		}
	}
	
	public function getNbOfPlacesLeft()
	{
		return $this->getNbOfPlaces()-$this->getNbOfUserWithReservation();
	}
	
	public function getNbOfUserWithReservation()
	{
		return Database::shared()->getNbOfUserWithReservationForEvent($this);
	}

	public function getAreRoomsReady() {
		return $this->areRoomsReady;
	}
	
	public function haveReservationsStarted()
	{
		if($this->reservationStart==null)
		{
			return false;
		}
		return $this->reservationStart <= time();
	}
	
	public function getReservationStart()
	{
		if($this->reservationStart==null)
		{
			return INF;
		}
		return $this->reservationStart;
	}
	
	public function getPriceAdherentKes() {
		return $this->price_x;
	}

	public function getPriceExt() {
		return $this->price_ext;
	}

}

}

?>