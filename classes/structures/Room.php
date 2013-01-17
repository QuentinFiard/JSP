<?php

namespace structures;

require_once 'classes/database/Database.php';

require_once 'classes/structures/User.php';

use database\Database;

class Room {
    private $roomId;
    private $event;
    private $eventId;
    private $name;
    private $nbOfPlaces;
    private $nbOfMembers;
    private $roomNumber;
    private $roomLeader;
    private $leader;
    private $members = null;
    private $keyImmediatelyAvailable;

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
        return array("roomId","eventId","name","nbOfPlaces","roomNumber","roomLeader",'keyImmediatelyAvailable');
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

    public function getEventId() {
        return $this->eventId;
    }

    public function getEvent() {
        return $this->event;
    }

    public function getRoomLeaderId() {
        return $this->roomLeader;
    }

    public function getRoomLeader() {
        if(!isset($this->leader) && isset($this->roomLeader))
        {
            $this->leader = User::userWithUserId($this->roomLeader);
        }
        return $this->leader;
    }

    public function getName() {
        return $this->name;
    }

    public function hasName()
    {
        return isset($this->name) && !empty($this->name);
    }

    public function setName($name)
    {
        $this->name = $name;
        Database::shared()->setNameForRoom($name,$this);
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
        if(isset($this->nbOfMembers))
        {
            return $this->nbOfMembers;
        }
        if(isset($this->members))
        {
            $this->nbOfMembers = count($this->members);
        }
        else
        {
            $this->nbOfMembers = Database::shared()->getNbOfMembersForRoom($this);
        }
        return $this->nbOfMembers;
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

    public function setRoomLeader($user)
    {
        $this->leader = $user;
        $this->roomLeader = $user->getUserId();
    }

    public function save()
    {
        Database::shared()->saveRoom($this);
    }

    public function getNbOfPlacesLeft()
    {
        return $this->getNbOfPlaces()-$this->getNbOfMembers();
    }

    public function getKeyImmediatelyAvailable()
    {
        return $this->keyImmediatelyAvailable;
    }

}

?>