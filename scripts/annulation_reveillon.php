<?php
use database\Database;

use structures\events\SemaineReveillon;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/database/Database.php';

$event = SemaineReveillon::shared();
$users = $event->getUsers();
$filteredUsers = array();

$ignored = array('xavier.saint-georges-chaumet@polytechnique.edu');

foreach($users as $user)
{
	if($user->isOnMainListForEvent($event) && $user->hasToPayForEvent($event) /* && !$user->isMember() && !$user->isExt() && !in_array($user->getEmail(), $ignored)*/)
	{
		$filteredUsers[] = $user;
	}
}

$count = count($filteredUsers);

function sendCancelationEmailToUser($user)
{
	$object = "[JSP] Annulation de ton inscription à la semaine du réveillon";
	$message  = "Bonjour ".$user->getFirstname().',<br/><br/>';
	$message .= "Comme nous n'avons toujours pas reçu ton paiement pour la semaine du réveillon et comme tu n'avais que jusqu'à vendredi pour le faire, ton inscription vient d'être automatiquement annulée.<br/><br/>";
	$message .= "Si tu souhaites malgré tout participer à l'évènement, tu peux commencer une nouvelle inscription sur <a href=\"http://jsp.binets.fr/\">le site</a>.<br/><br/>";
	$message .= 'Amicalement,<br/><br/>';
	$message .= 'Quentin<br>';
	$message .= 'JSP 2013 - Respo inscriptions';
	$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($user->getEmail(),$object,$message,$headers );
}

$user = Database::shared()->getUserWithUserId(8);

//sendCancelationEmailToUser($user);

?>

<div class="content admin" id="adminContent">
<h2>Liste des adresses mails des participants à la semaine du réveillon qui sont sur liste principale et qui n'ont pas encore payé</h2><br/>
<h3>Nb de personnes concernées : <?php echo $count; ?></h3><br/><br/>
<ul>
<?php 
foreach($filteredUsers as $user)
{
	//$user->cancelReservationForEvent($event);
	//sendCancelationEmailToUser($user);
	?><li><?php echo $user->getEmail(); ?> : <?php echo 'Sent'; ?></li><?php
}
?>
</ul>
</div>