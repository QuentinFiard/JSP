<?php

namespace structures {

use utilities\Server;

use structures\events\WeekendJSP;

use structures\events\SemaineReveillon;

require_once 'classes/database/Database.php';
require_once 'classes/structures/Room.php';

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
    private $areBusesReady;
    private $reservationStart;
    private $reservationEnd;
    private $nbOfPlaces;

    private $rooms = null;
    private $buses = null;
    private $buildings = null;

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
                    if($key=='start' || $key=='end' || $key=='reservationStart' || $key=='reservationEnd')
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
        $res = 0;
        if($user->isAdherentKes())
        {
            $res += $this->price_x;
        }
        else
        {
            $res += $this->price_ext;
        }
        $options = $user->getOptionsForEvent($this);
        foreach($options as $option)
        {
            $res += $option->getPriceForUser($user);
        }
        return $res;
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

    public function getBuses() {
        if(!isset($this->buses))
        {
            $this->buses = Database::shared()->getBusesForEvent($this);
        }
        return $this->buses;
    }

    public function getBuildings() {
        if(!isset($this->buildings))
        {
            $this->buildings = Database::shared()->getBuildingsForEvent($this);
        }
        return $this->buildings;
    }

    public function getRoomWithRoomId($roomId) {
        $room = Room::roomWithRoomId($roomId);
        if($room->getEventId()!=$this->eventId)
        {
            return null;
        }
        return $room;
    }

    public function getBusWithBusId($busId) {
        $bus = Bus::busWithBusId($busId);
        if($bus->getEventId()!=$this->eventId)
        {
            return null;
        }
        return $bus;
    }

    public function getOptions() {
        return Database::shared()->optionsForEvent($this);
    }

    public function getOptionWithName($name) {
        return Database::shared()->getOptionWithNameForEvent($name,$this);
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
            return Database::shared()->getNbOfPlacesForEvent($this) + $this->nbOfPlaces;
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

    public function getAreBusesReady() {
        return $this->areBusesReady;
    }

    public function haveReservationsStarted()
    {
        if($this->reservationStart==null)
        {
            return false;
        }
        return $this->reservationStart <= time();
    }

    public function haveReservationsStopped()
    {
        if($this->reservationEnd==null)
        {
            return false;
        }
        return $this->reservationEnd <= time();
    }

    public function getReservationStart()
    {
        if($this->reservationStart==null)
        {
            return INF;
        }
        return $this->reservationStart;
    }

    public function getReservationEnd()
    {
        if($this->reservationEnd==null)
        {
            return INF;
        }
        return $this->reservationEnd;
    }

    public function getPriceAdherentKes() {
        return $this->price_x;
    }

    public function getPriceExt() {
        return $this->price_ext;
    }

    public function getUsersWithNoRoom()
    {
        return Database::shared()->getUsersWithNoRoomForEvent($this);
    }

    public function getUsersWithNoBus()
    {
        return Database::shared()->getUsersWithNoBusForEvent($this);
    }

    public function getUsers()
    {
        return Database::shared()->getUsersForEvent($this);
    }

    public function getFirstUserInWaitingList()
    {
        return Database::shared()->getFirstUserInWaitingListForEvent($this);
    }

    abstract public function getNameWithPrefixA();
    abstract public function getNameWithPrefixPour();

    public function sendConfirmationEmailToUser($user,$waiting_list=true)
    {
        $config_url = "http://jsp.binets.fr".$this->getPage()->childWithName('inscription')->childWithName('configuration')->getPath();
        if($waiting_list)
        {
            $object="[JSP] Confirmation de ton inscription sur liste d'attente ".$this->getNameWithPrefixA();
            $message  = "Ton inscription sur liste d'attente ".$this->getNameWithPrefixA()." est confirmée. Afin d'accélerer le processus d'inscription, nous contacterons à chaque désistement en priorité les personnes sur liste d'attente qui nous ont déjà réglé le montant total de l'évènement (et parmi celles-ci celles qui se sont inscrites le plus tôt). Tâche donc de nous apporter tes chèques le plus rapidement possible !<br/>";
            $message .= "Pour configurer ton inscription et obtenir plus d'informations concernant le paiement de l'évènement (ordres, montants), tu peux te rendre à l'adresse <a href=\"".$config_url."\">".$config_url."</a>.<br/><br/>";
            $message .= 'En espérant te revoir très bientôt sur notre site, cordialement,<br/>';
            $message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
            $headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            mail($user->getEmail(),$object,$message,$headers );
        }
        else
        {
            $object="[JSP] Confirmation de ton inscription ".$this->getNameWithPrefixA();
            $message  = "Félicitations, ton inscription ".$this->getNameWithPrefixA()." est confirmée. Tu dois maintenant nous transmettre ton règlement par chèque sous un délai de ";
            if($user->isExt())
            {
                $message .= "deux semaines ";
            }
            else
            {
                $message .= "une semaine ";
            }
            $message .= "sans quoi nous réattribuerons ta place à une personne sur liste d'attente.<br/>";
            $message .= "Pour configurer ton inscription et obtenir plus d'informations concernant le paiement de l'évènement (ordres, montants), tu peux te rendre à l'adresse <a href=\"".$config_url."\">".$config_url."</a>.<br/><br/>";
            $message .= 'En espérant te revoir très bientôt sur notre site, cordialement,<br/>';
            $message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
            $headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            mail($user->getEmail(),$object,$message,$headers );
        }
    }

    public function sendAnnulationEmailToUser($user)
    {
        $object = "[JSP] Confirmation d'annulation de ton inscription ".$this->getNameWithPrefixA();
        $message  = "Ta demande d'annulation de ton inscription ".$this->getNameWithPrefixA()." a bien été prise en compte, et tu n'es maintenant plus inscrit à cet évènement.";
        $message .= "<br/><br/>";
        $message .= 'En espérant te revoir très bientôt sur notre site ou à l\'un de nos évènements, cordialement,<br/>';
        $message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
        $headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($user->getEmail(),$object,$message,$headers );
    }

    public function sendSwitchToMainListEmailToUser($user)
    {
        $config_url = "http://jsp.binets.fr".$this->getPage()->childWithName('inscription')->childWithName('configuration')->getPath();
        $object = "[JSP] Passage sur liste principale ".$this->getNameWithPrefixPour();
        $message  = "Félicitations, tu viens de passer sur liste principale ".$this->getNameWithPrefixPour().'.';
        if($user->hasToPayForEvent($this))
        {
            $message .= " Nous attendons toutefois toujours ton paiement pour l'évènement ! Tu trouveras à l'adresse <a href=\"".$config_url."\">".$config_url."</a> le détail de ton inscription, les montants à payer et les ordres des chèques, si nous ne recevons pas ton réglement sous quelques jours nous nous verrons dans l'obligation d'annuler ton inscription.";
        }
        else
        {
            $message .= " Comme nous avions d'ores et déjà reçu ton règlement pour l'évènement, ton inscription est maintenant terminée, et nous avons hâte de t'y retrouver sur les pistes !";
        }
        $message .= "<br/><br/>";
        $message .= 'En espérant te revoir très bientôt sur notre site ou à l\'un de nos évènements, cordialement,<br/>';
        $message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
        $headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($user->getEmail(),$object,$message,$headers );
    }

    public function getNbOfReservationsInWaitingList()
    {
        return Database::shared()->getNbOfReservationsInWaitingListEvent($this);
    }

    public function getPositionInWaitingListForUser($user)
    {
        return Database::shared()->getPositionInWaitingListForUserAndEvent($user,$this);
    }

    public function isGagnantPlace($user) {
        return false;
    }

    public function addToNumberOfPlaces($toAdd)
    {
        Database::shared()->addToNumberOfPlacesForEvent($this,1);
        $this->nbOfPlaces += $toAdd;
    }

    public function getIncompleteRooms()
    {
        return Database::shared()->getIncompleteRoomsForEvent($this);
    }

    public function getSkisets()
    {
        return Database::shared()->getSkisetsForEvent($this);
    }

}

}

?>