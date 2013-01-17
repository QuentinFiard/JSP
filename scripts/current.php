<?php
use structures\events\WeekendJSP;

use database\Database;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/database/Database.php';

$event = WeekendJSP::shared();
$users = $event->getUsers();
$filteredUsers = array();

$ignored = array('jacques.de-chalendar@polytechnique.edu');

$date = microtime(true);

foreach($users as $user)
{
	$reservation = $user->getReservationForEvent($event);
	$offset = $date-$reservation['date'];
	$jours = $offset / 3600*24;
	if($user->isOnMainListForEvent($event) && $user->hasToPayForEvent($event) && !$user->isExt() && !in_array($user->getEmail(), $ignored) && $jours>7)
	{
		$filteredUsers[] = $user;
	}
}

$count = count($filteredUsers);

function sendWarningEmailToUser($user)
{
	$object = "[JSP] Avertissement avant annulation de ton inscription au weekend JSP";
	$message  = "Salut ".$user->getFirstname().',<br/><br/>';
	$message .= "Nous n'avons toujours pas reçu ton paiement pour le weekend JSP. Tu t'étais engagé au moment de ton inscription à nous donner tes chèques sous une semaine, et la campagne Kès t'a accordé un petit délai avant que nous te relancions, mais voilà, il faut bien que tu paies un jour ! Tu as donc jusqu'à mardi midi (dans notre grande générosité nous ne mettons pas la deadline lundi midi pour que ceux qui ne rentreraient que lundi après-midi de weekend puissent venir aux perms) pour nous donner tes chèques, sans quoi ton inscription sera annulée et ta place donnée à une personne sur liste d'attente.<br/><br/>";
	$message .= "Si tu souhaites te désinscrire, tu peux le faire dès à présent sur <a href=\"http://jsp.binets.fr/events/weekend/\">la page récapitulative de ton inscription</a>. Sinon on espère te voir lundi ou mardi au Bob de 12h30 à 13h30 aux désormais traditionnelles perms BoB des JSP. (Tu peux également déposer tes chèques dans le casier des JSP à la Kès si tu n'es pas disponible entre midi et deux).<br/><br/>";
	$message .= 'Bonne soirée,<br/><br/>';
	$message .= 'Quentin<br>';
	$message .= 'JSP 2013 - Respo inscriptions';
	$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$message = rtrim(chunk_split(base64_encode($message)));
	mail($user->getEmail(),$object,$message,$headers );
}

$user = Database::shared()->getUserWithUserId(8);

sendWarningEmailToUser($user);

exit;

?>

<div class="content admin" id="adminContent">
<h2>Liste des adresses mails des participants au weekend JSP qui sont sur liste principale et qui n'ont pas encore payé</h2><br/>
<h3>Nb de personnes concernées : <?php echo $count; ?></h3><br/><br/>
<ul>
<?php 
foreach($filteredUsers as $user)
{
	?><li><?php echo $user->getEmail(); ?> : <?php /*sendWarningEmailToUser($user);*/ echo 'Sent'; ?></li><?php
}
?>
</ul>
</div>