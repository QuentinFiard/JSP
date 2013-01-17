<?php
use database\Database;

use structures\events\SemaineReveillon;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/database/Database.php';

$event = SemaineReveillon::shared();
$users = $event->getUsers();
$filteredUsers = array();

foreach($users as $user)
{
	if($user->isOnMainListForEvent($event) && $user->hasToPayForEvent($event) && !$user->isMember() && !$user->isExt())
	{
		$filteredUsers[] = $user;
	}
}

$count = count($filteredUsers);

function sendWarningEmailToUser($user)
{
	$object = "[JSP] Avertissement avant annulation de ton inscription à la semaine du réveillon";
	$message  = "Salut ".$user->getFirstname().',<br/><br/>';
	$message .= "Nous n'avons toujours pas reçu ton paiement pour la semaine du réveillon. Tu t'étais engagé au moment de ton inscription à nous donner tes chèques dans la semaine et tu n'as donc plus que jusqu'à demain midi pour le faire. Dans le cas où nous n'aurions toujours par reçu tes chèques demain soir, ton inscription serait annulée et ta place donnée à une personne sur liste d'attente (avoue que ce serait dommage !).<br/><br/>";
	$message .= "Si tu souhaites te désinscrire, tu peux le faire dès à présent sur <a href=\"http://jsp.binets.fr/events/reveillon/\">la page récapitulative de ton inscription à l'évènement</a>. Sinon on espère te voir demain de 12h30 à 13h30 à la désormais traditionnelle perm BoB des JSP ! (Tu peux également déposer tes chèques dans le casier des JSP à la Kès si tu n'es pas disponible entre midi et deux).<br/><br/>";
	$message .= 'Bonne soirée,<br/><br/>';
	$message .= 'Quentin<br>';
	$message .= 'JSP 2013 - Respo inscriptions';
	$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($user->getEmail(),$object,$message,$headers );
}

$user = Database::shared()->getUserWithUserId(8);

sendWarningEmailToUser($user);

exit;

?>

<div class="content admin" id="adminContent">
<h2>Liste des adresses mails des participants à la semaine du réveillon qui sont sur liste principale et qui n'ont pas encore payé</h2><br/>
<h3>Nb de personnes concernées : <?php echo $count; ?></h3><br/><br/>
<ul>
<?php 
foreach($filteredUsers as $user)
{
	?><li><?php echo $user->getEmail(); ?> : <?php sendWarningEmailToUser($user); echo 'Sent'; ?></li><?php
}
?>
</ul>
</div>