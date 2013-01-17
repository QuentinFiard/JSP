<?php

namespace structures;

require_once 'classes/database/Database.php';

use database\Database;

class Skiset {
	private $skisetId;
	private $eventId;
	private $name;
	private $textDescription;

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

	public static function skisetWithSkisetId($skisetId)
	{
		return Database::shared()->getSkisetWithSkisetId($skisetId);
	}

	public static function skisetForUserAndEvent($skisetId)
	{
		return Database::shared()->getSkisetForUserAndEvent($skisetId);
	}

	public function getSkisetId() {
		return $this->skisetId;
	}

	public function getName() {
		return $this->name;
	}

	public function getTextDescription()
    {
        return $this->textDescription;
    }


}

?>