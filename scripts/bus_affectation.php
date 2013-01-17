<?php

use structures\Bus;

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
require_once 'classes/structures/Bus.php';

/**
 * We build a heap of bus ordered on the number of places left per bus
 * This balances the number of places left per bus at the end of the affectation
 * but requires PHP >= 5.3.0
 */

class BusHeap extends \SplHeap
{
	protected function compare($bus1, $bus2)
    {
        return $bus1->getNbOfPlacesLeft()-$bus2->getNbOfPlacesLeft();
    }

}

class BusAffectation extends Script
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

        $buses = $this->event->getBuses();
        $usersLeft = $this->event->getUsersWithNoBus();

        foreach($usersLeft as $user)
        {
            echo $user->getEmail()."\n";
        }

        shuffle($usersLeft);

        // We insert the buses inside a heap to sort them based on number of places left

        $heap = new BusHeap();

        foreach($buses as $bus)
        {
            if($bus->getNbOfPlacesLeft()>0)
            {
                $heap->insert($bus);
                echo "Bus ".$bus->getBusNumber()." : ".$bus->getNbOfPlacesLeft()." places libres\n";
            }
        }

        foreach($usersLeft as $user)
        {
            $bus = $heap->extract();
            $user->setBusForEvent($bus,$this->event);
            echo 'Affecting '.$user->getFullName().' to bus n°'.$bus->getBusNumber()."\n";
            $bus = Bus::busWithBusId($bus->getBusId());
            if($bus->getNbOfPlacesLeft()>0)
            {
                $heap->insert($bus);
            }
        }

        Database::shared()->unlockTables();
    }
}

$script = new BusAffectation(WeekendJSP::shared());
$script->execute();