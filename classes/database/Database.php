<?php

namespace database {

use structures\Building;

use structures\Skiset;

use exceptions\BusIsFull;

use exceptions\UserHasBusAlready;

use structures\Bus;

use exceptions\UserHasRoomAlready;

use exceptions\RoomIsFull;

use structures\events\SemaineReveillon;

use exceptions\InvalidCaution;

use exceptions\InvalidPrice;

use exceptions\UserHasAlreadyPaid;

use exceptions\NoReservationForUser;

use exceptions\OptionHasUsers;

use structures\Option;

use exceptions\NoSuchUser;

use utilities\Miscellaneous;

use exceptions\EmailAlreadyExists;

use structures\ExternalUser;

use structures\User;

use structures\FrankizUser;
use structures\Event;
use structures\Room;

require_once 'classes/structures/Event.php';
require_once 'classes/structures/User.php';
require_once 'classes/structures/Room.php';
require_once 'classes/structures/Bus.php';
require_once 'classes/structures/Option.php';
require_once 'classes/structures/FrankizUser.php';
require_once 'classes/structures/ExternalUser.php';
require_once 'classes/structures/SecurityLevel.php';
require_once 'classes/structures/Skiset.php';
require_once 'classes/structures/Building.php';

require_once 'classes/exceptions/EmailAlreadyExists.php';
require_once 'classes/exceptions/NoSuchUser.php';
require_once 'classes/exceptions/OptionHasUsers.php';
require_once 'classes/exceptions/InvalidPrice.php';
require_once 'classes/exceptions/InvalidCaution.php';
require_once 'classes/exceptions/NoReservationForUser.php';
require_once 'classes/exceptions/UserHasAlreadyPaid.php';
require_once 'classes/exceptions/RoomIsFull.php';
require_once 'classes/exceptions/BusIsFull.php';
require_once 'classes/exceptions/UserHasRoomAlready.php';
require_once 'classes/exceptions/UserHasBusAlready.php';

require_once 'classes/structures/Event.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/events/WeekendJSP.php';

require_once 'classes/utilities/Miscellaneous.php';

require_once 'classes/SensitiveData.php';

/*
 */

class Database {
    static private $shared_ = null;

    private $conn;

    private function __construct()
    {
        try {
            $this->conn = new \PDO('mysql:host=localhost;dbname=jsp', 'jsp', \SensitiveData::$MySQLPassword);
            $this->conn->exec("SET NAMES utf8");
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public static function init()
    {
        if(!isset(self::$shared_))
        {
            self::$shared_ = new Database();
        }
    }

    public static function shared()
    {
        if(!isset(self::$shared_))
        {
            self::init();
        }
        return self::$shared_;
    }

    /*
     * User
     */

    public function hasUserWithUserId($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE userId=?");
        $stmt->execute(array($userId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function getUserWithUserId($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN FrankizUser WHERE userId=?");
        $stmt->execute(array($userId));

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new FrankizUser($row);
        }

        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE userId=?");
        $stmt->execute(array($userId));

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new ExternalUser($row);
        }
        return null;
    }

    public function getReservationForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Reservation WHERE eventId=? AND userId=? AND `cancelationDate` IS NULL");
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row;
        }
        return null;
    }

    public function getReservationWithReservationId($reservationId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Reservation WHERE reservationId=?");
        $stmt->execute(array($reservationId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row;
        }
        return null;
    }

    public function isUserOnMainListForEvent($user,$event)
    {
        $reservation = $this->getReservationForUserAndEvent($user, $event);
        if(!$reservation)
        {
            throw new NoReservationForUser();
        }
        if($reservation['mainList']!=null)
        {
            return true;
        }
        $stmt = $this->conn->prepare("SELECT COUNT(*) as `index`  FROM `Reservation` WHERE `eventId`=? AND (`date`<? OR `mainList` IS NOT NULL) AND `cancelationDate` IS NULL");
        $stmt->execute(array($event->getEventId(),$reservation['date']));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            $index = $row['index'];
            if($index < $event->getNbOfPlaces())
            {
                return true;
            }
        }
        return false;
    }

    public function existsReservationForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Reservation WHERE eventId=? AND userId=? AND `cancelationDate` IS NULL");
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function addReservationForUserAndEvent($user,$event)
    {
        $date = microtime(true);
        $stmt = $this->conn->prepare("INSERT INTO `Reservation` (`eventId`,`userId`,`date`) VALUES (?,?,?)");
        $stmt->execute(array($event->getEventId(),$user->getUserId(),$date));
    }

    public function hasUserPaidForEvent($user,$event)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Reservation WHERE eventId=? AND userId=? AND `cancelationDate` IS NULL");
        $stmt->execute(array($event->getEventId(),$user->getUserId()));

        if($stmt->fetch(\PDO::FETCH_ASSOC))
        {
            return true;
        }
        return false;
    }

    public function saveUser($user)
    {
        $first = true;
        $bindings = array();
        $keys = User::getProperties();
        $cmd="";
        if($user->getUserId()!=null)
        {
            $cmd = "UPDATE User SET ";
            foreach($keys as $key)
            {
                if($key!='userId')
                {
                    if(!$first)
                    {
                        $cmd .= ', ';
                    }
                    $cmd .= $key.'=?';
                    $bindings[] = $user->getProperty($key);
                    $first = false;
                }
            }
            $cmd .= ' WHERE userId=?';
            $bindings[] = $user->getProperty('userId');
        }
        else
        {
            $cmd = "INSERT INTO User (";
            $values = '';
            foreach($keys as $key)
            {
                if(!$first)
                {
                    $cmd .= ',';
                    $values .= ',';
                }
                $cmd .= $key;
                $values .= '?';
                $bindings[] = $user->getProperty($key);
                $first = false;
            }

            $cmd .= ') VALUES ('.$values.')';
        }

        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);
        if($user->getUserId()==null)
        {
            $user->setUserId($this->conn->lastInsertId());
        }
    }

    public function getFrankizUsersWithFilters($data)
    {
        $cmd  = "SELECT User.*,FrankizUser.* FROM User NATURAL JOIN FrankizUser NATURAL JOIN Reservation";
        $filter = " WHERE `Reservation`.`cancelationDate` IS NULL ";
        $bindings = array();
        $hasFilter = true;
        if(isset($data['userId']) && !empty($data['userId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`userId`=?";
            $bindings[] = $data['userId'];
            $hasFilter = true;
        }
        if(isset($data['reservationId']) && !empty($data['reservationId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`Reservation`.`reservationId`=?";
            $bindings[] = $data['reservationId'];
            $hasFilter = true;
        }
        if(isset($data['eventId']) && !empty($data['eventId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`Reservation`.`eventId`=?";
            $bindings[] = $data['eventId'];
            $hasFilter = true;
        }
        if(isset($data['lastname']) && !empty($data['lastname']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`lastname` LIKE ?";
            $bindings[] = '%'.$data['lastname'].'%';
            $hasFilter = true;
        }
        if(isset($data['firstname']) && !empty($data['firstname']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`firstname` LIKE ?";
            $bindings[] = '%'.$data['firstname'].'%';
            $hasFilter = true;
        }
        if(isset($data['email']) && !empty($data['email']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`email` LIKE ?";
            $bindings[] = '%'.$data['email'].'%';
            $hasFilter = true;
        }
        if($hasFilter)
        {
            $cmd .= $filter;
        }
        $cmd .= " LIMIT 1000;";

        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);

        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res[] = new FrankizUser($row);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getExternalUsersWithFilters($data)
    {
        $cmd  = "SELECT User.*,ExternalUser.* FROM User NATURAL JOIN ExternalUser NATURAL JOIN Reservation";
        $filter = " WHERE `Reservation`.`cancelationDate` IS NULL ";
        $bindings = array();
        $hasFilter = true;
        if(isset($data['userId']) && !empty($data['userId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`userId`=?";
            $bindings[] = $data['userId'];
            $hasFilter = true;
        }
        if(isset($data['reservationId']) && !empty($data['reservationId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`Reservation`.`reservationId`=?";
            $bindings[] = $data['reservationId'];
            $hasFilter = true;
        }
        if(isset($data['eventId']) && !empty($data['eventId']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`Reservation`.`eventId`=?";
            $bindings[] = $data['eventId'];
            $hasFilter = true;
        }
        if(isset($data['lastname']) && !empty($data['lastname']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`lastname` LIKE ?";
            $bindings[] = '%'.$data['lastname'].'%';
            $hasFilter = true;
        }
        if(isset($data['firstname']) && !empty($data['firstname']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`firstname` LIKE ?";
            $bindings[] = '%'.$data['firstname'].'%';
            $hasFilter = true;
        }
        if(isset($data['email']) && !empty($data['email']))
        {
            if($hasFilter)
            {
                $filter .= ' AND ';
            }
            $filter .= "`User`.`email` LIKE ?";
            $bindings[] = '%'.$data['email'].'%';
            $hasFilter = true;
        }
        if($hasFilter)
        {
            $cmd .= $filter;
        }
        $cmd .= " LIMIT 1000;";

        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);

        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res[] = new ExternalUser($row);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getUsersWithFilters($data)
    {
        $res = $this->getFrankizUsersWithFilters($data);
        $res = array_merge($res,$this->getExternalUsersWithFilters($data));
        usort($res, array("\\structures\\User", "cmp"));
        return $res;
    }

    public function getFrankizUsersWithNoRoomForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,FrankizUser.*
                                          FROM
                                            (`User` NATURAL JOIN `FrankizUser` NATURAL JOIN `Reservation`)
                                            LEFT JOIN `RoomForReservation`
                                                ON `Reservation`.reservationId=`RoomForReservation`.reservationId
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `RoomForReservation`.`reservationId` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new FrankizUser($row);

            if($u->isOnMainListForEvent($event))
            {
                $res[] = $u;
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getExternalUsersWithNoRoomForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,ExternalUser.*
                                          FROM
                                            (`User` NATURAL JOIN `ExternalUser` NATURAL JOIN `Reservation`)
                                            LEFT JOIN `RoomForReservation`
                                                ON `Reservation`.reservationId=`RoomForReservation`.reservationId
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `RoomForReservation`.`reservationId` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new ExternalUser($row);

            if($u->isOnMainListForEvent($event))
            {
                $res[] = $u;
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getUsersWithNoRoomForEvent($event)
    {
        $res = $this->getFrankizUsersWithNoRoomForEvent($event);
        $res = array_merge($res,$this->getExternalUsersWithNoRoomForEvent($event));
        usort($res, array("\\structures\\User", "cmp"));
        return $res;
    }

    public function getFrankizUsersWithNoBusForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,FrankizUser.*
                                          FROM
                                            (`User` NATURAL JOIN `FrankizUser` NATURAL JOIN `Reservation`)
                                            LEFT JOIN `BusForReservation`
                                                ON `Reservation`.reservationId=`BusForReservation`.reservationId
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `BusForReservation`.`reservationId` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new FrankizUser($row);

            if($u->isOnMainListForEvent($event))
            {
                $res[] = $u;
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getExternalUsersWithNoBusForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,ExternalUser.*
                                          FROM
                                            (`User` NATURAL JOIN `ExternalUser` NATURAL JOIN `Reservation`)
                                            LEFT JOIN `BusForReservation`
                                                ON `Reservation`.reservationId=`BusForReservation`.reservationId
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `BusForReservation`.`reservationId` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new ExternalUser($row);

            if($u->isOnMainListForEvent($event))
            {
                $res[] = $u;
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getUsersWithNoBusForEvent($event)
    {
        $res = $this->getFrankizUsersWithNoBusForEvent($event);
        $res = array_merge($res,$this->getExternalUsersWithNoBusForEvent($event));
        usort($res, array("\\structures\\User", "cmp"));
        return $res;
    }

    public function getFrankizUsersForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,FrankizUser.*
                                          FROM
                                            `User`
                                            NATURAL JOIN `FrankizUser`
                                            NATURAL JOIN `Reservation`
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new FrankizUser($row);

            $res[] = $u;

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getExternalUsersForEvent($event)
    {
        $stmt = $this->conn->prepare(  "SELECT User.*,ExternalUser.*
                                          FROM
                                            `User`
                                            NATURAL JOIN `ExternalUser`
                                            NATURAL JOIN `Reservation`
                                        WHERE
                                            `Reservation`.`cancelationDate` IS NULL
                                        AND `Reservation`.eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $u = new ExternalUser($row);

            $res[] = $u;

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getUsersForEvent($event)
    {
        $res = $this->getFrankizUsersForEvent($event);
        $res = array_merge($res,$this->getExternalUsersForEvent($event));
        usort($res, array("\\structures\\User", "cmp"));
        return $res;
    }

    /*
     * External User
     */

    public function getExternalUserWithEmailAndPassword($email,$password)
    {
        $stmt = $this->conn->prepare("SELECT salt FROM User NATURAL JOIN ExternalUser WHERE email=? AND validated=1");
        $stmt->execute(array($email));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            $salt = $row['salt'];

            $digest = hash('sha512',$salt.$email.$password,true);

            $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE email=? AND digest=? AND validated=1");
            $stmt->execute(array($email,$digest));
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if($row)
            {
                return new ExternalUser($row);
            }
            return null;
        }
        return null;
    }

    public function getExternalUserWithEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE email=?");
        $stmt->execute(array($email));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new ExternalUser($row);
        }
        return null;
    }

    public function resetPasswordForExternalUser($user)
    {
        $newPassword = Miscellaneous::generateRandomPassword(10);

        $passwordHash = hash('sha256',$newPassword,true);

        $stmt = $this->conn->prepare("SELECT salt FROM ExternalUser WHERE userId=?");
        $stmt->execute(array($user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$row)
        {
            throw new NoSuchUser();
        }

        $salt = $row['salt'];

        $digest = hash('sha512',$salt.$user->getEmail().$passwordHash,true);

        $stmt = $this->conn->prepare("UPDATE ExternalUser SET digest=? WHERE userId=?");
        $stmt->execute(array($digest,$user->getUserId()));

        return $newPassword;
    }

    public function setPasswordForExternalUser($user,$password)
    {
        $stmt = $this->conn->prepare("SELECT salt FROM ExternalUser WHERE userId=?");
        $stmt->execute(array($user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$row)
        {
            throw new NoSuchUser();
        }

        $salt = $row['salt'];

        $digest = hash('sha512',$salt.$user->getEmail().$password,true);

        $stmt = $this->conn->prepare("UPDATE ExternalUser SET digest=? WHERE userId=?");
        $stmt->execute(array($digest,$user->getUserId()));
    }

    public function validateExternalUserWithConfirmationId($confirmationId)
    {
        // substr(bin2hex($this->digest), 0, 32);

        $key = Miscellaneous::hex2bin($confirmationId).'%';

        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE salt LIKE ?");
        $stmt->execute(array($key));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$row)
        {
            throw new NoSuchUser();
        }

        $stmt = $this->conn->prepare("UPDATE ExternalUser SET validated=1 WHERE salt LIKE ?");
        $stmt->execute(array($key));
    }

    public function validateExternalUserWithConfirmationIdAsCadreX($confirmationId)
    {
        // substr(bin2hex($this->digest), 0, 32);

        $key = Miscellaneous::hex2bin($confirmationId).'%';

        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE salt LIKE ? AND isCadreX=0");
        $stmt->execute(array($key));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(!$row)
        {
            throw new NoSuchUser();
        }

        $stmt = $this->conn->prepare("UPDATE ExternalUser SET isCadreX=1 WHERE salt LIKE ?");
        $stmt->execute(array($key));
    }

    public function getUserWithConfirmationId($confirmationId)
    {
        // substr(bin2hex($this->digest), 0, 32);

        $key = Miscellaneous::hex2bin($confirmationId).'%';

        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE salt LIKE ?");
        $stmt->execute(array($key));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new ExternalUser($row);
        }

        return null;

    }

    public function validateExternalUser($user)
    {
        $stmt = $this->conn->prepare("UPDATE ExternalUser SET validated=1 WHERE userId=?");
        $stmt->execute(array($user->getUserId()));
    }

    public function addExternalUserWithEmailPasswordAndData($email,$password,$data)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN ExternalUser WHERE email=?");
        $stmt->execute(array($email));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            throw new EmailAlreadyExists();
        }

        $salt = openssl_random_pseudo_bytes('32');
        $digest = hash('sha512',$salt.$email.$password,true);

        $this->conn->beginTransaction();

        $stmt = $this->conn->prepare("INSERT INTO User (email,lastname,firstname) VALUES (?,?,?)");
        $stmt->execute(array($email,$data['lastname'],$data['firstname']));

        $userId = $this->conn->lastInsertId();

        $stmt = $this->conn->prepare("INSERT INTO ExternalUser (userId,salt,digest) VALUES (?,?,?)");
        $stmt->execute(array($userId,$salt,$digest));

        $this->conn->commit();
    }

    /*
     * FrankizUser
     */

    public function getFrankizUserWithUID($uid)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User NATURAL JOIN FrankizUser WHERE uid=?");
        $stmt->execute(array($uid));

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new FrankizUser($row);
        }
        return null;
    }

    public function saveFrankizUser($user)
    {
        $this->saveUser($user);

        $cmd = "REPLACE INTO FrankizUser (";
        $values = '';
        $bindings = array();
        $first = true;
        $keys = FrankizUser::getProperties();
        foreach($keys as $key)
        {
            if(!$first)
            {
                $cmd .= ',';
                $values .= ',';
            }
            $cmd .= $key;
            $values .= '?';
            if($key=='securityLevel')
            {
                $bindings[] = $user->getProperty('securityLevel')->getSecurityLevel();
            }
            else
            {
                $bindings[] = $user->getProperty($key);
            }
            $first = false;
        }
        $cmd .= ') VALUES ('.$values.')';
        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);
    }

    /*
     * Event
     */

    public function getRowForEventWithId($eventId)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Event WHERE eventId=?');
        $stmt->execute(array($eventId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row;
        }
        return null;
    }

    public function getRoomsForEvent($event)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM Room WHERE eventId=? ORDER BY nbOfPlaces DESC, roomId ASC');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $room = new Room($event,$row);
            $res[] = $room;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getIncompleteRoomsForEvent($event)
    {
        $res = array();
        $stmt = $this->conn->prepare('
            SELECT Room.*,nbOfMembers
            FROM
                (
                    SELECT
                        roomId,
                        COUNT(DISTINCT reservationId) as nbOfMembers
                    FROM
                        Room
                        LEFT JOIN RoomForReservation
                        ON Room.roomId=RoomForReservation.roomId
                    WHERE
                        eventId=?
                        AND cancelationDate IS NULL
                    GROUP BY roomId
                ) as tmp
                NATURAL JOIN Room
            WHERE nbOfPlaces>nbOfMembers;');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $room = new Room($event,$row);
            $res[] = $room;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getBusesForEvent($event)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM Bus WHERE eventId=? ORDER BY busNumber ASC');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $bus = new Bus($event,$row);
            $res[] = $bus;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    /*
     * Room
     */

    public function getMembersForRoom($room)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM (User LEFT JOIN FrankizUser ON User.userId=FrankizUser.userId LEFT JOIN ExternalUser ON User.userId=ExternalUser.userId) INNER JOIN Reservation ON User.userId=Reservation.userId NATURAL JOIN RoomForReservation WHERE roomId=?  AND `Reservation`.`cancelationDate` IS NULL ORDER BY `User`.lastname ASC, `User`.firstname ASC');
        $stmt->execute(array($room->getRoomId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $user = new User($row);
            $res[] = $user;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getNbOfMembersForRoom($room)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) as nbOfMembers FROM User NATURAL JOIN Reservation NATURAL JOIN RoomForReservation WHERE roomId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($room->getRoomId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfMembers'];
        }
        return 0;
    }

    public function saveRoom($room)
    {
        $first = true;
        $bindings = array();
        $keys = Room::getProperties();
        $cmd="";
        if($room->getRoomId()!=null)
        {
            $cmd = "UPDATE Room SET ";
            foreach($keys as $key)
            {
                if($key!='roomId')
                {
                    if(!$first)
                    {
                        $cmd .= ', ';
                    }
                    $cmd .= $key.'=?';
                    $bindings[] = $room->getProperty($key);
                    $first = false;
                }
            }
            $cmd .= ' WHERE roomId=?';
            $bindings[] = $room->getProperty('roomId');
        }
        else
        {
            $cmd = "INSERT INTO Room (";
            $values = '';
            foreach($keys as $key)
            {
                if(!$first)
                {
                    $cmd .= ',';
                    $values .= ',';
                }
                $cmd .= $key;
                $values .= '?';
                $bindings[] = $room->getProperty($key);
                $first = false;
            }
            $cmd .= ') VALUES ('.$values.')';
        }
        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);
        if($room->getRoomId()==null)
        {
            $room->setRoomId($this->conn->lastInsertId());
        }
    }

    public function getRoomWithRoomId($roomId)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Room WHERE roomId=?');
        $stmt->execute(array($roomId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Room(Event::eventWithEventId($row['eventId']),$row);
        }
        return null;
    }

    public function getRoomReportForEvent($event)
    {
        $res = array();

        $res['rooms']=array();
        $stmt = $this->conn->prepare('SELECT nbOfPlaces,COUNT(*) as nbOfRooms FROM Room WHERE eventId=? GROUP BY nbOfPlaces ORDER BY nbOfPlaces DESC;');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res['rooms'][$row['nbOfPlaces']] = (int)$row['nbOfRooms'];
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        $res['used']=array();
        $res['unused']=array();

        foreach(array_keys($res['rooms']) as $n)
        {
            $res['used'][$n] = 0;
            $res['unused'][$n] = $res['rooms'][$n];
        }

        $stmt = $this->conn->prepare('SELECT nbOfPlaces,COUNT(*) as nbOfRooms FROM Room NATURAL JOIN Reservation NATURAL JOIN RoomForReservation WHERE eventId=?  AND `Reservation`.`cancelationDate` IS NULL GROUP BY nbOfPlaces ORDER BY nbOfPlaces DESC;');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res['used'][$row['nbOfPlaces']] = (int)$row['nbOfRooms'];
            $res['unused'][$row['nbOfPlaces']] -= (int)$row['nbOfRooms'];
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        return $res;
    }

    public function addRoomsForEvent($event,$nbOfPlaces,$nbOfRooms)
    {
        $this->conn->exec('LOCK TABLE Room;');
        $stmt = $this->conn->prepare('SELECT MAX(roomNumber) as lastRoomNumber FROM Room WHERE eventId=?;');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $roomNumber = 1;
        if($row)
        {
            $roomNumber = $row['lastRoomNumber']+1;
        }
        $stmt = $this->conn->prepare('INSERT INTO Room (eventId,nbOfPlaces,roomNumber) VALUES (?,?,?);');
        for($room=0 ; $room<$nbOfRooms ; $room++)
        {
            $stmt->execute(array($event->getEventId(),$nbOfPlaces,$roomNumber));
            $roomNumber++;
        }
        $this->conn->exec('UNLOCK TABLES;');
    }

    public function dropRoomForUserAndEvent($user,$event)
    {
        $reservation = $user->getReservationForEvent($event);
        $stmt = $this->conn->prepare('DELETE FROM `RoomForReservation` WHERE `reservationId`=?');
        $stmt->execute(array($reservation['reservationId']));
    }

    public function setRoomForUserAndEvent($room,$user,$event)
    {
        if($user->hasRoomForEvent($event))
        {
            throw new UserHasRoomAlready();
        }

        $nbOfMembers = $room->getNbOfMembers();
        $nbOfPlaces = $room->getNbOfPlaces();

        if($nbOfPlaces<=$nbOfMembers)
        {
            throw new RoomIsFull();
        }

        $reservation = $user->getReservationForEvent($event);

        $stmt = $this->conn->prepare('INSERT INTO `RoomForReservation`(`roomId`,`reservationId`) VALUES (?,?);');
        $stmt->execute(array($room->getRoomId(),$reservation['reservationId']));
    }

    public function removeRoomsForEvent($event,$nbOfPlaces,$nbOfRooms)
    {
        $stmt = $this->conn->prepare('DELETE FROM Room WHERE roomId IN
                (
                    SELECT roomId FROM
                    (SELECT Room.roomId FROM Room LEFT JOIN (Reservation NATURAL JOIN RoomForReservation) ON Room.roomId=RoomForReservation.roomId
                    WHERE Room.eventId=? AND Room.nbOfPlaces=? AND (RoomForReservation.roomId IS NULL OR `Reservation`.`cancelationDate` IS NOT NULL)
                    ORDER BY roomNumber DESC) as tmpTable
                ) LIMIT '.(int)$nbOfRooms);
        $stmt->execute(array($event->getEventId(),$nbOfPlaces));
    }

    public function getRoomForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare('SELECT Room.* FROM Reservation NATURAL JOIN RoomForReservation NATURAL JOIN Room WHERE eventId=? AND userId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Room($event,$row);
        }
        return null;
    }

    public function existsRoomForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Reservation NATURAL JOIN RoomForReservation WHERE eventId=? AND userId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function setNameForRoom($name,$room)
    {
        $stmt = $this->conn->prepare('UPDATE Room SET name=? WHERE roomId=?');
        $stmt->execute(array($name,$room->getRoomId()));
    }

    /*
     * Bus
     */

    public function getMembersForBus($bus)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM (User LEFT JOIN FrankizUser ON User.userId=FrankizUser.userId LEFT JOIN ExternalUser ON User.userId=ExternalUser.userId) INNER JOIN Reservation ON User.userId=Reservation.userId NATURAL JOIN BusForReservation WHERE busId=?  AND `Reservation`.`cancelationDate` IS NULL ORDER BY `User`.lastname ASC, `User`.firstname ASC');
        $stmt->execute(array($bus->getBusId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $user = new User($row);
            $res[] = $user;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getNbOfMembersForBus($bus)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) as nbOfMembers FROM User NATURAL JOIN Reservation NATURAL JOIN BusForReservation WHERE busId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($bus->getBusId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfMembers'];
        }
        return 0;
    }

    public function saveBus($bus)
    {
        $first = true;
        $bindings = array();
        $keys = Bus::getProperties();
        $cmd="";
        if($bus->getBusId()!=null)
        {
            $cmd = "UPDATE Bus SET ";
            foreach($keys as $key)
            {
                if($key!='busId')
                {
                    if(!$first)
                    {
                        $cmd .= ', ';
                    }
                    $cmd .= $key.'=?';
                    $bindings[] = $bus->getProperty($key);
                    $first = false;
                }
            }
            $cmd .= ' WHERE busId=?';
            $bindings[] = $bus->getProperty('busId');
        }
        else
        {
            $cmd = "INSERT INTO Bus (";
            $values = '';
            foreach($keys as $key)
            {
                if(!$first)
                {
                    $cmd .= ',';
                    $values .= ',';
                }
                $cmd .= $key;
                $values .= '?';
                $bindings[] = $bus->getProperty($key);
                $first = false;
            }
            $cmd .= ') VALUES ('.$values.')';
        }
        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);
        if($bus->getBusId()==null)
        {
            $bus->setBusId($this->conn->lastInsertId());
        }
    }

    public function getBusWithBusId($busId)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Bus WHERE busId=?');
        $stmt->execute(array($busId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Bus(Event::eventWithEventId($row['eventId']),$row);
        }
        return null;
    }

    public function setBusForUserAndEvent($bus,$user,$event)
    {
        if($user->hasBusForEvent($event))
        {
            throw new UserHasBusAlready();
        }

        $nbOfMembers = $bus->getNbOfMembers();
        $nbOfPlaces = $bus->getNbOfPlaces();

        if($nbOfPlaces<=$nbOfMembers)
        {
            throw new BusIsFull();
        }

        $reservation = $user->getReservationForEvent($event);

        $stmt = $this->conn->prepare('INSERT INTO `BusForReservation`(`busId`,`reservationId`) VALUES (?,?);');
        $stmt->execute(array($bus->getBusId(),$reservation['reservationId']));
    }

    public function dropBusForUserAndEvent($user,$event)
    {
        $reservation = $user->getReservationForEvent($event);
        $stmt = $this->conn->prepare('DELETE FROM `BusForReservation` WHERE `reservationId`=?');
        $stmt->execute(array($reservation['reservationId']));
    }

    public function getBusForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare('SELECT Bus.* FROM Reservation NATURAL JOIN BusForReservation NATURAL JOIN Bus WHERE eventId=? AND userId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Bus($event,$row);
        }
        return null;
    }

    public function existsBusForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Reservation NATURAL JOIN BusForReservation WHERE eventId=? AND userId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    /*
     * Event
     */

    public function getNbOfPlacesForEvent($event)
    {
        $stmt = $this->conn->prepare('SELECT SUM(nbOfPlaces) as nbOfPlacesForEvent FROM Room WHERE eventId=?');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfPlacesForEvent'];
        }
        return 0;
    }

    public function getNbOfUserWithReservationForEvent($event)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) as nbOfUsers FROM User NATURAL JOIN Reservation WHERE eventId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfUsers'];
        }
        return 0;
    }

    /*
     * Configuration
     */

    public function hasConfigurationField($field)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Configuration WHERE field=?');
        $stmt->execute(array($field));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function getConfigurationFieldAsString($field)
    {
        $stmt = $this->conn->prepare('SELECT CONVERT(value USING utf8) as value FROM Configuration WHERE field=?');
        $stmt->execute(array($field));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['value'];
        }
        return null;
    }

    public function getConfigurationFieldAsDouble($field)
    {
        $stmt = $this->conn->prepare('SELECT CAST(value AS DECIMAL) as value FROM Configuration WHERE field=?');
        $stmt->execute(array($field));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return (float)$row['value'];
        }
        return null;
    }

    public function getConfigurationFieldAsInt($field)
    {
        $stmt = $this->conn->prepare('SELECT CAST(value AS INTEGER) as value FROM Configuration WHERE field=?');
        $stmt->execute(array($field));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return (int)$row['value'];
        }
        return null;
    }

    /*
     * Options
     */

    public function optionWithOptionId($optionId)
    {
        $stmt = $this->conn->prepare('SELECT * FROM `Option` WHERE optionId=?');
        $stmt->execute(array($optionId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Option(Event::eventWithEventId($row['eventId']),$row);
        }
        return null;
    }

    public function saveOption($option)
    {
        $exists = ($option->getOptionId()!=null);
        if($exists)
        {
            $this->conn->exec('LOCK TABLES `Option` WRITE, `OptionForReservation` READ;');
            if($this->existsUsersForOption($option))
            {
                $this->conn->exec('UNLOCK TABLES;');
                throw new OptionHasUsers();
            }
        }
        $first = true;
        $bindings = array();
        $keys = Option::getProperties();
        $cmd="";
        if($exists)
        {
            $cmd = "UPDATE `Option` SET ";
            foreach($keys as $key)
            {
                if($key!='optionId')
                {
                    if(!$first)
                    {
                        $cmd .= ', ';
                    }
                    $cmd .= $key.'=?';
                    $bindings[] = $option->getProperty($key);
                    $first = false;
                }
            }
            $cmd .= ' WHERE optionId=?';
            $bindings[] = $option->getProperty('optionId');
        }
        else
        {
            $cmd = "INSERT INTO `Option` (";
            $values = '';
            foreach($keys as $key)
            {
                if($key!='optionId')
                {
                    if(!$first)
                    {
                        $cmd .= ',';
                        $values .= ',';
                    }
                    $cmd .= $key;
                    $values .= '?';
                    $bindings[] = $option->getProperty($key);
                    $first = false;
                }
            }
            $cmd .= ') VALUES ('.$values.')';
        }
        $stmt = $this->conn->prepare($cmd);
        $stmt->execute($bindings);
        if($exists)
        {
            $this->conn->exec('UNLOCK TABLES;');
        }
        else
        {
            $option->setOptionId($this->conn->lastInsertId());
        }
    }

    public function existsUsersForOption($option)
    {
        $stmt = $this->conn->prepare('SELECT * FROM `OptionForReservation` WHERE optionId=?');
        $stmt->execute(array($option->getOptionId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function userHasOption($user,$option)
    {
        $stmt = $this->conn->prepare('SELECT * FROM `OptionForReservation` NATURAL JOIN `Reservation` WHERE optionId=? AND userId=?');
        $stmt->execute(array($option->getOptionId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function dropOption($option)
    {
        $this->conn->exec('LOCK TABLES `Option` WRITE, `OptionForReservation` READ;');
        if($this->existsUsersForOption($option))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new OptionHasUsers();
        }
        $stmt = $this->conn->prepare('DELETE FROM `Option` WHERE optionId=?');
        $stmt->execute(array($option->getOptionId()));
        $this->conn->exec('UNLOCK TABLES;');
    }

    public function optionsForEvent($event)
    {
        $res = array();

        $stmt = $this->conn->prepare('SELECT * FROM `Option` WHERE eventId=? ORDER BY name');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res[] = new Option($event,$row);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getOptionWithNameForEvent($name,$event)
    {
        $stmt = $this->conn->prepare('SELECT * FROM `Option` WHERE eventId=? AND name=?');
        $stmt->execute(array($event->getEventId(),$name));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Option($event,$row);
        }
        return null;
    }

    public function getOptionWithNameLikeForUserAndEvent($name,$user,$event)
    {
        $stmt = $this->conn->prepare('SELECT `Option`.* FROM `Option` NATURAL JOIN `OptionForReservation` NATURAL JOIN `Reservation` WHERE eventId=? AND userId=? AND `Option`.name LIKE ?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId(),'%'.$name.'%'));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Option($event,$row);
        }
        return null;
    }

    public function getOptionsForUserAndEvent($user,$event)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT `Option`.* FROM `Option` NATURAL JOIN `OptionForReservation` NATURAL JOIN `Reservation` WHERE eventId=? AND userId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res[] = new Option($event,$row);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function removeOptionsForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare('DELETE FROM `OptionForReservation` USING `OptionForReservation` NATURAL JOIN `Reservation` WHERE `userId`=? AND `eventId`=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($user->getUserId(),$event->getEventId()));
    }

    public function addOptionForUser($option,$user)
    {
        $event = Event::eventWithEventId($option->getEventId());
        $reservation = $user->getReservationForEvent($event);
        $stmt = $this->conn->prepare('INSERT IGNORE INTO `OptionForReservation` (`optionId`,`reservationId`) VALUES (?,?) ');
        $stmt->execute(array($option->getOptionId(),$reservation['reservationId']));
    }

    public function dropOptionForUser($option,$user)
    {
        $event = Event::eventWithEventId($option->getEventId());
        $reservation = $user->getReservationForEvent($event);
        $stmt = $this->conn->prepare('DELETE FROM `OptionForReservation` WHERE `optionId`=? AND `reservationId`=?');
        $stmt->execute(array($option->getOptionId(),$reservation['reservationId']));
    }

    public function confirmPaymentForEventUserPriceAndCaution($event,$user,$price,$caution)
    {
        $this->conn->exec('LOCK TABLES `Reservation` WRITE, `Option` READ, `OptionForReservation` READ, `User` READ;');
        if(!$this->hasUserWithUserId($user->getUserId()))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new NoSuchUser();
        }
        if(!$user->hasReservationForEvent($event))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new NoReservationForUser();
        }
        if(!$user->hasToPayForEvent($event))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new UserHasAlreadyPaid();
        }
        if($price != $event->priceForUser($user))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new InvalidPrice();
        }
        if($caution != $event->cautionForUser($user))
        {
            $this->conn->exec('UNLOCK TABLES;');
            throw new InvalidCaution();
        }

        $stmt = $this->conn->prepare('UPDATE `Reservation` SET hasPaid=1 WHERE userId=? AND eventId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($user->getUserId(),$event->getEventId()));

        $this->conn->exec('UNLOCK TABLES;');
    }

    public function switchUserToMainListForEvent($user,$event)
    {
        $reservation = $user->getReservationForEvent($event);
        if(!$reservation)
        {
            throw new NoReservationForUser();
        }
        $date = microtime(true);
        $stmt = $this->conn->prepare('UPDATE Reservation SET mainList=? WHERE reservationId=?');
        $stmt->execute(array($date,$reservation['reservationId']));
    }

    public function cancelReservationForUserAndEvent($user,$event)
    {
        try {
            $this->conn->exec("LOCK TABLES `User` READ, `FrankizUser` READ, `ExternalUser` READ, `Reservation` WRITE;");
            $this->conn->beginTransaction();

            $isOnMainList = $user->isOnMainListForEvent($event);
            $nextInQueue = null;
            if($isOnMainList)
            {
                $nextInQueue = $this->getFirstUserInWaitingListForEvent($event);
            }

            $date = microtime(true);
            $reservation = $user->getReservationForEvent($event);
            if(!$reservation)
            {
                throw new NoReservationForUser();
            }

            $stmt = $this->conn->prepare('UPDATE Reservation SET `Reservation`.`cancelationDate`=? WHERE `reservationId`=?');
            $stmt->execute(array($date,$reservation['reservationId']));

            if($nextInQueue)
            {
                $nextInQueue->switchToMainListForEvent($event);
            }

            $this->conn->commit();
            $this->conn->exec("UNLOCK TABLES;");
        }
        catch(\Exception $e)
        {
            $this->conn->rollBack();
            $this->conn->exec("UNLOCK TABLES;");
            throw $e;
        }
    }

    public function addShotgunReveillon($uid)
    {
        $this->conn->beginTransaction();

        $shotgun_user = $this->getFrankizUserWithUID($uid);
        if(!$shotgun_user)
        {
            $stmt = $this->conn->prepare("INSERT INTO `User`(`firstname`, `lastname`, `email`) VALUES ('shotgun','shotgun','shotgun');");
            $stmt->execute();
            $userId = $this->conn->lastInsertId();
            $stmt = $this->conn->prepare("INSERT INTO `FrankizUser`(`userId`, `uid`, `securityLevel`, `hruid`, `isX_`, `class`) VALUES (?,?,0,'shotgun',0,'shotgun');");
            $stmt->execute(array($userId,$uid));
        }
        else
        {
            $userId = $shotgun_user->getUserId();
        }
        $event = SemaineReveillon::shared();
        $date = microtime(true);
        $stmt = $this->conn->prepare("INSERT INTO `Reservation`(`userId`,`eventId`,`date`) VALUES (?,?,?);");
        $stmt->execute(array($userId,$event->getEventId(),$date));
        $this->conn->commit();
    }

    public function isFrankizUserNonCotisant($user)
    {
        $stmt = $this->conn->prepare("SELECT * FROM NonCotisant WHERE uid=?");
        $stmt->execute(array($user->getUID()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function isFrankizUserCotisant($user)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Cotisant WHERE uid=?");
        $stmt->execute(array($user->getUID()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function isExternalUserCotisant($user)
    {
        $stmt = $this->conn->prepare("SELECT * FROM ExternalCotisant WHERE userId=?");
        $stmt->execute(array($user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function isFrankizUserGagnantPlace($user)
    {
        $stmt = $this->conn->prepare("SELECT * FROM GagnantPlace WHERE uid=?");
        $stmt->execute(array($user->getUID()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return true;
        }
        return false;
    }

    public function nbOfUserPreviouslyOnWaitingListNowOnMainListForEvent($event)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as `nbOfUsers` FROM `Reservation` WHERE `eventId`=? AND `mainList` IS NOT NULL AND `cancelationDate` IS NULL");
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfUsers'];
        }
        return 0;
    }

    public function getPositionInWaitingListForUserAndEvent($user,$event)
    {
        $reservation = $user->getReservationForEvent($event);
        /*
         * First, we determine the number of people that were on the waiting list but that have been called back since.
         */
        $nbOfUserPreviouslyOnWaitingListNowOnMainList = $this->nbOfUserPreviouslyOnWaitingListNowOnMainListForEvent($event);

        /*
         * As such, the maximum number of people who have not been called back and who are on the main list is defined as
         */
        $maxNbOfUsersOriginallyOnMainList = $event->getNbOfPlaces()-$nbOfUserPreviouslyOnWaitingListNowOnMainList;

        $stmt = $this->conn->prepare(  "SELECT
                                            COUNT(*) as `index`
                                        FROM
                                            (
                                                SELECT
                                                    `User`.`userId`,`Reservation`.`hasPaid`,`Reservation`.`date`
                                                FROM
                                                    `User`
                                                    NATURAL JOIN `Reservation`
                                                WHERE
                                                    `eventId`=:eventId
                                                    AND `mainList` IS NULL
                                                    AND `cancelationDate` IS NULL
                                                ORDER BY `Reservation`.`date` ASC
                                                LIMIT :offset,100000000000
                                            ) as `tmp`
                                        WHERE
                                            `tmp`.`hasPaid` > :hasPaid OR
                                            (`tmp`.`hasPaid` = :hasPaid AND `tmp`.`date` < :date);");
        $hasPaid = !$user->hasToPayForEvent($event);
        $eventId = $event->getEventId();
        $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $maxNbOfUsersOriginallyOnMainList, \PDO::PARAM_INT);
        $stmt->bindParam(':hasPaid', $hasPaid, \PDO::PARAM_BOOL);
        $stmt->bindParam(':date', $reservation['date']);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row)
        {
            return $row['index'];
        }
        return null;
    }

    public function getNbOfReservationsInWaitingListEvent($event)
    {
        /*
         * First, we determine the number of people that were on the waiting list but that have been called back since.
         */
        $nbOfUserPreviouslyOnWaitingListNowOnMainList = $this->nbOfUserPreviouslyOnWaitingListNowOnMainListForEvent($event);
        /*
         * As such, the maximum number of people who have not been called back and who are on the main list is defined as
         */
        $maxNbOfUsersOriginallyOnMainList = $event->getNbOfPlaces()-$nbOfUserPreviouslyOnWaitingListNowOnMainList;

        $stmt = $this->conn->prepare(  "SELECT
                                            COUNT(`Reservation`.`reservationId`) as 'nbOfReservations'
                                        FROM
                                            `User`
                                            NATURAL JOIN `Reservation`
                                        WHERE
                                            `eventId`=:eventId
                                            AND `mainList` IS NULL
                                            AND `cancelationDate` IS NULL" );

        $eventId = $event->getEventId();
        $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row)
        {
            return $row['nbOfReservations']-$maxNbOfUsersOriginallyOnMainList;
        }
        return null;
    }

    public function getUserAtPositionInReservationListForEvent($waitingListOffset,$position,$event)
    {
        $stmt = $this->conn->prepare(  "SELECT
                                            `tmp`.`userId`
                                        FROM
                                            (
                                                SELECT
                                                    `User`.`userId`,`Reservation`.`hasPaid`,`Reservation`.`date`
                                                FROM
                                                    `User`
                                                    NATURAL JOIN `Reservation`
                                                WHERE
                                                    `eventId`=:eventId
                                                    AND `mainList` IS NULL
                                                    AND `cancelationDate` IS NULL
                                                ORDER BY `Reservation`.`date` ASC
                                                LIMIT :offset,1000000000000
                                            ) as `tmp`
                                        ORDER BY
                                            `tmp`.`hasPaid` DESC,
                                            `tmp`.`date` ASC
                                        LIMIT :position,1;");
        $eventId = $event->getEventId();
        $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $waitingListOffset, \PDO::PARAM_INT);
        $stmt->bindParam(':position', $position, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row)
        {
            $userId = $row['userId'];
            return $this->getUserWithUserId($userId);
        }
        return null;
    }

    public function getFirstUserInWaitingListForEvent($event)
    {
        /*
         * This is a bit tricky, so let's comment it a bit
         */

        /*
         * First, we determine the number of people that were on the waiting list but that have been called back since.
         */
        $nbOfUserPreviouslyOnWaitingListNowOnMainList = $this->nbOfUserPreviouslyOnWaitingListNowOnMainListForEvent($event);

        /*
         * As such, the maximum number of people who have not been called back and who are on the main list is defined as
         */
        $maxNbOfUsersOriginallyOnMainList = $event->getNbOfPlaces()-$nbOfUserPreviouslyOnWaitingListNowOnMainList;

        /*
         * We can now write the select statement that defines the waiting list
         */
        return $this->getUserAtPositionInReservationListForEvent($maxNbOfUsersOriginallyOnMainList,0,$event);
    }

    public function lockRoomTables()
    {
        $this->conn->exec('LOCK TABLES Room READ, Reservation READ, RoomForReservation WRITE;');
    }

    public function lockBusTables()
    {
        $this->conn->exec('LOCK TABLES Bus READ, Reservation READ, BusForReservation WRITE;');
    }

    public function lockAddReservationTables()
    {
        $this->conn->exec('LOCK TABLES Reservation WRITE, Room READ;');
    }

    public function lockAllTables()
    {
         $this->conn->exec('FLUSH TABLES WITH READ LOCK');
    }

    public function unlockTables()
    {
        $this->conn->exec('UNLOCK TABLES;');
    }

    public function rollBack()
    {
        $this->conn->rollBack();
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function unlockRoomTables()
    {
        $this->unlockTables();
    }

    public function unlockBusTables()
    {
        $this->unlockTables();
    }

    public function addToNumberOfPlacesForEvent($event,$toAdd)
    {
        $stmt = $this->conn->prepare("UPDATE Event SET nbOfPlaces=nbOfPlaces+? WHERE eventId=?");
        $stmt->execute(array($toAdd,$event->getEventId()));
    }

    /*
     * Skisets
     */

    public function getSkisetWithSkisetId($skisetId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Skiset WHERE skisetId=?");
        $stmt->execute(array($skisetId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Skiset($row);
        }
        return null;
    }

    public function getSkisetsForEvent($event)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM Skiset WHERE eventId=?');
        $stmt->execute(array($event->getEventId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $skiset = new Skiset($row);
            $res[] = $skiset;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    /*
     * Buildings
     */

    public function getBuildingWithBuildingId($buildingId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Building WHERE buildingId=?");
        $stmt->execute(array($buildingId));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Building($row);
        }
        return null;
    }

    public function getBuildingForUserAndEvent($user,$event)
    {
        $stmt = $this->conn->prepare("SELECT Building.* FROM Building NATURAL JOIN BuildingForRoom NATURAL JOIN RoomForReservation NATURAL JOIN Reservation WHERE eventId=? AND userId=?");
        $stmt->execute(array($event->getEventId(),$user->getUserId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return new Building($row);
        }
        return null;
    }

    public function getBuildingsForEvent($event)
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT Building.* FROM Building NATURAL JOIN BuildingForRoom NATURAL JOIN RoomForReservation NATURAL JOIN Reservation WHERE eventId=?");
        $stmt->execute(array($event->getEventId()));
        $res = array();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $res[] = new Building($row);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getMembersForBuilding($building)
    {
        $res = array();
        $stmt = $this->conn->prepare('SELECT * FROM (User LEFT JOIN FrankizUser ON User.userId=FrankizUser.userId LEFT JOIN ExternalUser ON User.userId=ExternalUser.userId) INNER JOIN Reservation ON User.userId=Reservation.userId NATURAL JOIN RoomForReservation NATURAL JOIN BuildingForRoom WHERE buildingId=?  AND `Reservation`.`cancelationDate` IS NULL ORDER BY `User`.lastname ASC, `User`.firstname ASC');
        $stmt->execute(array($building->getBuildingId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        while($row)
        {
            $user = new User($row);
            $res[] = $user;
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $res;
    }

    public function getNbOfMembersForBuilding($building)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) as nbOfMembers FROM User NATURAL JOIN Reservation NATURAL JOIN RoomForReservation NATURAL JOIN BuildingForRoom WHERE buildingId=?  AND `Reservation`.`cancelationDate` IS NULL');
        $stmt->execute(array($building->getBuildingId()));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($row)
        {
            return $row['nbOfMembers'];
        }
        return 0;
    }
}

Database::init();

}

?>