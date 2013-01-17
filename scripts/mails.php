<?php
use structures\events\WeekendJSP;

use database\Database;

use structures\events\SemaineReveillon;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/database/Database.php';

global $subjects,$messages;
$subjects = array(
			'Merci la Kès !',
			'Merci la Kès !',
			'Merci la Kès !',
			'Vous allez nous manquer !',
			"Ce n'est qu'un au revoir !",
			"Dehors les fruits !",
			"Sortie de fruits mûrs",
		);

$messages = array(
			'On vous aime !',
			'Vous êtes vraiment des fruits !',
			'Merci la Kès !',
			"Alban, je t'aime !",
			"Nico, je t'aime !",
			"Michel, je t'aime !",
			"Marion, je t'aime !",
			"Jung, je t'aime !",
			"Kévin, je t'aime !",
			"Donatien, je t'aime !",
			"Manon, je t'aime !",
			"Caro, je t'aime !",
			"Yawen, je t'aime !",
			"Thomas, je t'aime !",
			"Panis, je t'aime !",
			"François, je t'aime !",
			"Guillaume, je t'aime !",
			"Tanguy, je t'aime !",
		);

function sendMail($user)
{
	global $subjects,$messages;
		$i = rand(0, count($subjects)-1);
		$j = rand(0, count($messages)-1);
	
	while((($i==5 || $i==6) && $j==1) || ($i<3 && $j==2))
	{
		$i = rand(0, count($subjects)-1);
		$j = rand(0, count($messages)-1);
	}
	
	$object =  $subjects[$i];
	$message = $messages[$j];
	
	$headers  = 'From: '.$user->getFullName().' <'.$user->getEmail().'>' . "\r\n";
	$headers .= 'Return-path: Kès <kes@binets.polytechnique.fr>' . "\r\n";
	$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
	mail('Kès <kes@binets.polytechnique.fr>',$object,$message,$headers,"-f kes@binets.polytechnique.fr");
}

//$user = Database::shared()->getUserWithUserId(8);

//sendMail($user);

$users = Database::shared()->getFrankizUsersWithFilters(array());

shuffle($users);

?>
<ul>
<?php
foreach($users as $user)
{
	//sendMail($user);
	?><li><?php echo $user->getEmail(); ?></li><?php
}
?>
</ul>