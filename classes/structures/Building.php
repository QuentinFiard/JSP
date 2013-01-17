<?php

namespace structures;
	
require_once 'classes/database/Database.php';

use database\Database;

class Building {
	private $buildingId;
	private $name;

	public function __construct($row)
	{
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
		return array_keys(get_class_vars(get_class()));
	}
	
	public function getProperty($key)
	{
		if(!in_array($key, self::getProperties()))
		{
			return null;
		}
		return $this->$key;
	}
	
	public static function buildingWithBuildingId($buildingId)
	{
		return Database::shared()->getBuildingWithBuildingId($buildingId);
	}
	
	public static function buildingForUserAndEvent($user,$event)
	{
		return Database::shared()->getBuildingForUserAndEvent($user,$event);
	}
	
	public function getBuildingId() {
		return $this->buildingId;
	}

	public function getName() {
		return $this->name;
	}

	public function getMembers() {
		if(!isset($this->members))
		{
			$this->members = Database::shared()->getMembersForBuilding($this);
		}
		return $this->members;
	}
	
	public function getNbOfMembers()
	{
		if(isset($this->members))
		{
			return count($this->members);
		}
		return Database::shared()->getNbOfMembersForBuilding($this);
	}

}

?>