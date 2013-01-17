<?php

use structures\Room;

use structures\events\WeekendJSP;

use database\Database;

$dir=dirname(__FILE__);
$dir=dirname($dir);
chdir($dir);
set_include_path(get_include_path() . PATH_SEPARATOR . $dir);
var_dump(getcwd());

require_once 'classes/Script.php';
require_once 'classes/database/Database.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/Room.php';

/**
 * We build a heap of rooms ordered on the number of places left per room
 * This balances the number of places left per room at the end of the affectation
 * but requires PHP >= 5.3.0 
 */

class RoomHeap extends \SplHeap
{
	protected function compare($room1, $room2)
    {
        return $room1->getNbOfPlacesLeft()-$room2->getNbOfPlacesLeft();
    }

}

class RoomAffectation extends Script
{
    private $event;
    
    public function __construct($event)
    {
        parent::__construct();
        $this->event = $event;
    }
    
	public function execute()
    {
        Database::shared()->lockAllTables();
        
        $rooms = $this->event->getRooms();
        $usersLeft = $this->event->getUsersWithNoRoom();
        
        foreach($usersLeft as $user)
        {
            echo $user->getEmail()."\n";
        }
        
        shuffle($usersLeft);
        
        // We insert the rooms inside a heap to sort them based on number of places left
        
        $heap = new RoomHeap();
        
        foreach($rooms as $room)
        {
            if($room->getNbOfPlacesLeft()>0)
            {
                $heap->insert($room);
                echo "Room ".$room->getRoomNumber()." : ".$room->getNbOfPlacesLeft()." places libres\n";
            }
        }
        
        foreach($usersLeft as $user)
        {
            $room = $heap->extract();
            $user->setRoomForEvent($room,$this->event);
            echo 'Affecting '.$user->getFullName().' to room n°'.$room->getRoomNumber()."\n";
            $room = Room::roomWithRoomId($room->getRoomId());
            if($room->getNbOfPlacesLeft()>0)
            {
                $heap->insert($room);
            }
        }
        
        Database::shared()->unlockTables();
    }
}

$script = new RoomAffectation(WeekendJSP::shared());
$script->execute();