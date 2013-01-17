<?php

namespace arrival;

use structures\Skiset;

use structures\events\WeekendJSP;

use database\Database;
$dir=dirname(__FILE__);
$dir=dirname($dir);
$dir=dirname($dir);
set_include_path(get_include_path() . PATH_SEPARATOR . $dir);

ini_set("memory_limit","1G");

require_once 'classes/database/Database.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/Building.php';
require_once 'classes/structures/Bus.php';
require_once 'classes/structures/Skiset.php';

class MinUserHeap extends \SplHeap
{
	protected function compare($elem1, $elem2)
    {
        // Lowest nb of users first
        return -($elem1["nbOfUsers"]-$elem2["nbOfUsers"]);
    }

    public function addUsersToMin($users)
    {
        $top = $this->extract();
        $users = array_merge($top['users'],$users);
        $this->insert($top['value'],$users);
    }

    public function top()
    {
        $top = parent::top();
        return $top['value'];
    }

    public function nbOfUsersForTop()
    {
        $top = parent::top();
        return $top['nbOfUsers'];
    }

	public function insert($value,$users=array())
    {
        parent::insert(array(
            'value'=>$value,
            'users'=>$users,
            'nbOfUsers'=>count($users)
        ));
    }
}

const CANCELED = 1;
const TRAIN = 2;

function hoursToLabel($hours)
{
    $h = floor($hours);
    $m = 60*($hours-$h);
    if($m<10)
    {
        $m = '0'.$m;
    }
    if($m==0)
    {
        return $h.'h';
    }
    return $h.'h'.$m;
}

class Generator {

    const colorMin = 1;
    const colorMax = 3;

    private $nbOfUsers = 0;

    private $outputFlag;

    private $maxPerInterval = 50;
    private $interval = 0.5;

    private $currentTime = 8;
    private $nbOfUsersForCurrentInterval = 0;

    private $timeForUser = array();

    private $skiset = array(
        325,
        434,
        266,
        458,
        63,
        561,
        386,
        619,
        477,
        72,
        571,
        584,
        314,
        549,
        467,
        35,
        579,
        460,
        45,
        124,
        516,
        271,
        123,
        597,
        696,
        130,
        312,
        54,
        297,
        558,
        276,
        105,
        469,
        121,
        270,
        336,
        241,
        482,
        658,
        651,
        196,
        635,
        71,
        489,
        178,
        168,
        250,
        286,
        389,
        391,
    );

    private $colors;
    private $colorForRoom = array();

    private $allUsersHaveRentalInRoom = array();
    private $noUsersHasRentalInRoom = array();

    private $event;

    public function __construct($outputFlag=true)
    {
        $this->outputFlag = $outputFlag;

        $this->event = WeekendJSP::shared();

        $skisets = $this->event->getSkisets();

        /*$this->skisets = new MinUserHeap();
        foreach($skisets as $skiset)
        {
            $this->skisets->insert($skiset);
        }*/

        $this->colors = new MinUserHeap();
        for($i=Generator::colorMin ; $i<=Generator::colorMax ; $i++)
        {
            $this->colors->insert($i);
        }
    }

    public function merge($pdfs,$output)
    {
        $cmd  = "/usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=";
        $cmd .= $output;
        foreach($pdfs as $pdf)
        {
            $cmd .= ' '.$pdf;
        }
        $res = shell_exec($cmd);

        foreach($pdfs as $pdf)
        {
            unlink($pdf);
        }
    }

    public function mergeByTwo($pdfs,$output)
    {
        $n = count($pdfs);
        $half = floor(($n+1)/2);

        $pages = array();

        for($i=0 ; $i<$half ; $i++)
        {
            $cmd  = "/usr/local/texlive/2012/bin/x86_64-darwin/pdfnup --quiet --nup 2x1 --landscape --outfile half_page_".$i.'.pdf '.$pdfs[$i];
            if($i+$half<$n)
            {
                $cmd .= ' '.$pdfs[$i+$half];
            }
            shell_exec($cmd);
            unlink($pdfs[$i]);
            if($i+$half<$n)
            {
                unlink($pdfs[$i+$half]);
            }
            $pages[] = "half_page_".$i.'.pdf';
        }

        $this->merge($pages, $output);
    }

    public function stepsForUser($user)
    {
        $room = $user->getRoomForEvent($this->event);
        $roomLeader = $room->getRoomLeader();
        $steps = '\newcommand{\steps}{';

        $step = '\step{';
        if($room->getKeyImmediatelyAvailable())
        {
            if($user->getUserId() == $roomLeader->getUserId())
            {
                $step .= 'Viens nous voir pour récupérer les clés de la chambre ';
                $step .= $room->getRoomNumber();
                $step .= ", nous donner ton numéro de téléphone pour que l'on puisse contacter ta chambre pendant le weekend,";
                $step .= " récupérer les packs bouffe et les forfaits de ta chambre.";
            }
            else
            {
                $step .= 'Contacte ';
                $step .= $roomLeader->getFullName();
                $step .= " (ton chef de chambre) pour récupérer les clés de ta chambre, ton pack bouffe et ton forfait.";
            }
        }
        else
        {
            $step .= "Dépose tes bagages à la bagagerie.";
        }
        $step .= "}\n";
        $steps .= $step;

        $step = '\step{';
        if($room->getKeyImmediatelyAvailable())
        {
            $step .= "Dépose tes bagages dans ta chambre.";
        }
        else
        {
            if($user->getUserId() == $roomLeader->getUserId())
            {
                $step .= "Viens nous voir pour récupérer les packs bouffe et forfaits de ta chambre, et nous donner ton numéro de téléphone pour que l'on puisse contacter ta chambre pendant le weekend.";
            }
            else
            {
                $step .= 'Contacte ';
                $step .= $roomLeader->getFullName();
                $step .= " (ton chef de chambre) pour récupérer ton pack bouffe et ton forfait.";
            }
        }
        $step .= "}\n";
        $steps .= $step;

        $rental = $user->getRentalForEvent($this->event);

        if($rental!=null)
        {
            $step = '\step{';
            $step .= 'Rends-toi ';
            $reservation = $user->getReservationForEvent($this->event);
            if(in_array($reservation['reservationId'], $this->skiset))
            {
                $skiset = Skiset::skisetWithSkisetId(1);
                $step .= $skiset->getTextDescription();
                $step .= ' situé dans la galerie marchande de la grande barre en face des pistes pour y récupérer ';
                $step .= $rental->getTextDescription();
                $step .= '.';
            }
            else
            {
                $skiset = Skiset::skisetWithSkisetId(2);
                $time = null;
                if(array_key_exists($user->getUserId(), $this->timeForUser))
                {
                    $time = $this->timeForUser[$user->getUserId()];
                }
                else
                {
                    if($this->nbOfUsersForCurrentInterval+1>$this->maxPerInterval)
                    {
                        $this->currentTime += $this->interval;
                        $this->nbOfUsersForCurrentInterval = 0;
                    }

                    $this->nbOfUsersForCurrentInterval++;
                    $time = hoursToLabel($this->currentTime);
                    $this->timeForUser[$user->getUserId()] = $time;
                }
                $step .= ' {\bf à partir de '.$time.'} ';
                $step .= $skiset->getTextDescription();
                $step .= ' situé dans la galerie marchande de la grande barre en face des pistes pour y récupérer ';
                $step .= $rental->getTextDescription();
                $step .= '.';
            }

            $step .= "}\n";
            $steps .= $step;
        }

        $step = '\step{';
        $step .= 'Va skier :-).';
        $step .= "}\n";
        $steps .= $step;

        if(!$room->getKeyImmediatelyAvailable())
        {
            $step = '\step{';
            $step .= 'Quand tu as fini de skier, viens nous voir pour récupérer les clés de';
            if($room->getRoomNumber()!=null)
            {
                $step .= ' la chambre ';
                $step .= $room->getRoomNumber();
            }
            else
            {
                $step .= ' ta chambre ';
            }
            $step .= " (elles devraient être disponibles aux alentours de 17h).";
            $step .= "}\n";
            $steps .= $step;
        }

        $steps .= "}\n";

        return $steps;
    }

    public function createDataFileForUser($user,$groupMode,$i,$n)
    {
        $room = $user->getRoomForEvent($this->event);
        $roomLeader = $room->getRoomLeader();

        $bus = $user->getBusForEvent($this->event);

        $color = null;
        if(array_key_exists($room->getRoomId(), $this->colorForRoom))
        {
            $color = $this->colorForRoom[$room->getRoomId()];
        }
        else
        {
            $color = $this->colors->top();

            $roommates = $room->getMembers();

            $this->colors->addUsersToMin($roommates);

            $this->colorForRoom[$room->getRoomId()] = $color;
        }

        if($this->outputFlag)
        {
            $data = fopen('data.tex', 'w');
            fwrite($data,'\newcommand{\bracelet}{'.$color.'}'."\n");
            fwrite($data,'\newcommand{\name}{'.$user->getFullName().'}'."\n");
            if($user->getUserId() == $roomLeader->getUserId())
            {
                fwrite($data,'\newcommand{\isChefChambre}{1}'."\n");
            }
            else
            {
                fwrite($data,'\newcommand{\isChefChambre}{0}'."\n");
            }


            fwrite($data,'\newcommand{\busNumber}{'.$bus->getBusNumber().'}'."\n");
            fwrite($data,'\newcommand{\nbOfPlaces}{'.$bus->getNbOfPlaces().'}'."\n");
            fwrite($data,'\newcommand{\bus}{'.$bus->getName().'}'."\n");

            fwrite($data,'\newcommand{\batiment}{n°3}'."\n");
            fwrite($data,'\newcommand{\chambre}{n°278}'."\n");

            fwrite($data,$this->stepsForUser($user));

            fwrite($data,'% Group'."\n");
            fwrite($data,'% 0 : no group'."\n");
            fwrite($data,'% 1 : group by bus'."\n");
            fwrite($data,'\newcommand{\group}{'.$groupMode.'}'."\n");

            fwrite($data,'\newcommand{\userIndex}{'.$i.'}'."\n");
            fwrite($data,'\newcommand{\nbOfUsers}{'.$n.'}'."\n");
            fclose($data);
        }
        else
        {
            $this->stepsForUser($user);
        }

    }

    public function generatePagesForUsers($users,$groupMode)
    {
        $res = array();
        $i=1;
        $n = count($users);
        foreach($users as $user)
        {
            $this->createDataFileForUser($user,$groupMode,$i,$n);
            if($this->outputFlag)
            {
                $res[] = $this->generatePage($i);
            }
            $i++;
        }
        return $res;
    }

    public function generatePage($i)
    {
        $folder=getcwd();
        chdir($folder.'/template');
        shell_exec('./render.sh template.tex');
        chdir($folder);
        $page = 'page_'.$i.'.pdf';
        rename('template/template.pdf', $page);
        return $page;
    }

    /**
     * Groups:
     * 0 : no group (all users)
     * 1 : users for one bus
     * 2 : users for one building
     */

    public function generateDocumentForBuilding($building)
    {
        $groupMode = 2;
        $data = fopen('data.tex', 'w');
        fwrite($data,'\newcommand{\group}{2}'."\n");
        fwrite($data,'\newcommand{\batiment}{'.$building->getName().'}'."\n");
        fclose($data);

        $this->generateTitle();

        $users = $building->getMembers();

        $pages = $this->generatePagesForUsers($users,$groupMode);

        array_unshift($pages, 'page_0.pdf');

        if($this->outputFlag)
        {
            $this->mergeByTwo($pages, 'building_'.$building->getBuildingId().'.pdf');
        }
    }

    public function generateListingForBus($bus)
    {
        if($this->outputFlag)
        {
            $data = fopen('data.tex', 'w');
            fwrite($data,'\newcommand{\group}{1}'."\n");
            fwrite($data,'\newcommand{\busNumber}{'.$bus->getBusNumber().'}'."\n");
            fwrite($data,'\newcommand{\nbOfPlaces}{'.$bus->getNbOfPlaces().'}'."\n");
            fwrite($data,'\newcommand{\bus}{'.$bus->getName().'}'."\n");

            $rows = '\newcommand{\rows}{';
            $users = $bus->getMembers();

            foreach($users as $user)
            {
                $reservation = $user->getReservationForEvent($this->event);
                $flags = $reservation['flags'];
                $prefix = '';
                $start = '\rowstart ';
                if($flags&CANCELED)
                {
                    $start = '\canceledrowstart ';
                    $prefix = '\canceled ';
                }
                if($flags&TRAIN)
                {
                    $start = '\trainrowstart ';
                    $prefix = '\train ';
                }

                $room = $user->getRoomForEvent($this->event);
                $roomLeader = $room->getRoomLeader();
                $leaderSign = '';
                if($roomLeader!=null && $roomLeader->getUserId()==$user->getUserId())
                {
                    $leaderSign = 'X';
                }

                $rows .= $start.$leaderSign.'&'.$prefix.$user->getLastname().'&'.$prefix.$user->getFirstname().'\rowend'."\n";
            }
            $rows .= "}\n";
            fwrite($data, $rows);

            fclose($data);

            $folder=getcwd();
            chdir($folder.'/template');
            if(file_exists('listing.aux'))
            {
                unlink('listing.aux');
            }
            shell_exec('./render.sh listing.tex');
            shell_exec('./render.sh listing.tex');
            chdir($folder);
            rename('template/listing.pdf', 'listing_bus_'.$bus->getBusNumber().'.pdf');
        }
    }

    public function generateDocumentForBus($bus)
    {
        $groupMode = 1;
        $data = fopen('data.tex', 'w');
        fwrite($data,'\newcommand{\group}{1}'."\n");
        fwrite($data,'\newcommand{\bus}{'.$bus->getName().' - '.$bus->getNbOfPlaces().' places}'."\n");
        fclose($data);

        $this->generateTitle();

        $users = $bus->getMembers();

        $pages = $this->generatePagesForUsers($users,$groupMode);

        array_unshift($pages, 'page_0.pdf');

        if($this->outputFlag)
        {
            $this->mergeByTwo($pages, 'bus_'.$bus->getBusNumber().'.pdf');
        }
    }

    public function generateTitle()
    {
        if($this->outputFlag)
        {
            $folder=getcwd();
            chdir($folder.'/template');
            shell_exec('./render.sh title.tex');
            chdir($folder);
            rename($folder.'/template/title.pdf', $folder.'/page_0.pdf');
        }
    }

    public function generateFullDocument()
    {
        $groupMode = 0;
        $data = fopen('data.tex', 'w');
        fwrite($data,'\newcommand{\group}{0}'."\n");
        fclose($data);

        $this->generateTitle();

        $users = $this->event->getUsers();

        $pages = $this->generatePagesForUsers($users,$groupMode);

        array_unshift($pages, 'page_0.pdf');

        if($this->outputFlag)
        {
            $this->merge($pages, 'global.pdf');
        }
    }

    public function prepareRoomStatistics()
    {
        $rooms = $this->event->getRooms();

        foreach($rooms as $room)
        {
            $noRental = true;
            $allRental = true;

            $users = $room->getMembers();
            foreach($users as $user)
            {
                $allRental &= $user->hasRentalForEvent($this->event);
                $noRental &= !$user->hasRentalForEvent($this->event);
            }

            $this->allUsersHaveRentalInRoom[$room->getRoomId()] = $allRental;
            $this->noUsersHasRentalInRoom[$room->getRoomId()] = $noRental;
        }
    }

    public function printRoomStatistics()
    {
        echo 'Rooms with all rental : '.array_sum($this->allUsersHaveRentalInRoom)."\n";
        echo 'Rooms with no rental : '.array_sum($this->noUsersHasRentalInRoom)."\n";
    }

    public function prepareRoomLeaders()
    {
        $rooms = $this->event->getRooms();

        foreach($rooms as $room)
        {
            $min = -1;
            $minUser = null;
            $users = $room->getMembers();
            foreach($users as $user)
            {
                $reservation = $user->getReservationForEvent($this->event);
                $flags = $reservation['flags'];
                if(!($flags & CANCELED) && !($flags & TRAIN))
                {
                    $busNumber = $user->getBusForEvent($this->event)->getBusNumber();
                    if($min == -1 || $busNumber<$min)
                    {
                        $minUser = $user;
                        $min = $busNumber;
                    }
                }
            }

            if($minUser==null)
            {
                echo "Can't affect room leader to room with roomId=".$room->getRoomId();
                die();
            }

            $room->setRoomLeader($minUser);
            $room->save();
        }
    }

    public function printNbOfUsersPerColor()
    {
        while($this->colors->valid())
        {
            echo 'Couleur '.$this->colors->top().' : '.$this->colors->nbOfUsersForTop()." personnes\n";
            $this->colors->next();
        }
    }

    public function printNbOfUsers()
    {
        echo 'Nombre de personnes : '.$this->nbOfUsers."\n";
    }

    public function printSkisets()
    {
        $users = $this->event->getUsers();
        foreach($users as $user)
        {
            $rental = $user->getRentalForEvent($this->event);
            if($rental!=null)
            {
                $room = $user->getRoomForEvent($this->event);
                $skiset = Skiset::skisetWithSkisetId($this->skisetForRoom[$room->getRoomId()]);
                echo $user->getLastname()."\t".$user->getFirstname()."\t".$skiset->getName()."\t".$rental->getDescription()."\n";
            }
        }
    }

    public function printRentals()
    {
        $users = $this->event->getUsers();
        foreach($users as $user)
        {
            $rental = $user->getRentalForEvent($this->event);
            if($rental!=null)
            {
                $reservation = $user->getReservationForEvent($this->event);
                echo $reservation['reservationId']."\t".$user->getLastname()."\t".$user->getFirstname()."\t".$rental->getDescription()."\t".$user->getSize()."\t".(100*$user->getHeight())."\t".$user->getWeight()."\n";
            }
        }
    }

    public function start()
    {
        $this->prepareRoomStatistics();
        $this->prepareRoomLeaders();
        $buses = $this->event->getBuses();
        $this->outputFlag = false;
        foreach($buses as $bus)
        {
            $this->generateListingForBus($bus);
            $this->generateDocumentForBus($bus);
        }
        $this->outputFlag = true;

        $this->generateFullDocument();
    }

}

$generator = new Generator(true);
$generator->start();

/*
$generator->printRentals();
$generator->printNbOfUsers();
$generator->printNbOfUsersPerColor();
$generator->printSkisets();
*/

?>