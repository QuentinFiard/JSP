<?php

namespace structures;
	
require_once 'classes/database/Database.php';

use database\Database;

class Option {
	private $optionId;
	private $event;
	private $eventId;
	private $name;
	private $description;
	private $price_x;
	private $price_ext;

	public function __construct($event,$row)
	{
		$this->event = $event;
		$this->updateWithData($row,true);
	}
	
	public function updateWithData($data,$constructor=false)
	{
		$properties = self::getProperties();
		foreach($properties as $key)
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
	
	public static function optionWithOptionId($optionId)
	{
		return Database::shared()->optionWithOptionId($optionId);
	}
	
	public static function optionsForEvent($event)
	{
		return Database::shared()->optionsForEvent($event);
	}

	public static function getProperties()
	{
		return array("optionId","eventId","name","description","price_x","price_ext");
	}
	
	public function getProperty($key)
	{
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}
	
	public function getOptionId() {
		return $this->optionId;
	}

	public function getEvent() {
		return $this->event;
	}

	public function getEventId() {
		return $this->eventId;
	}

	public function getName() {
		return $this->name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getPrice_x() {
		return $this->price_x;
	}

	public function getPrice_ext() {
		return $this->price_ext;
	}

	public function setOptionId($optionId) {
		$this->optionId = $optionId;
	}

	public function setEvent($event) {
		$this->event = $event;
	}

	public function setEventId($eventId) {
		$this->eventId = $eventId;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setPrice_x($price_x) {
		$this->price_x = $price_x;
	}

	public function setPrice_ext($price_ext) {
		$this->price_ext = $price_ext;
	}

	public function save()
	{
		Database::shared()->saveOption($this);
	}
	
	public function drop()
	{
		Database::shared()->dropOption($this);
	}

}

?>