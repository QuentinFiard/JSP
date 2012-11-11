<?php
use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/pages/connexion/ExterieursPage.php';
require_once 'classes/pages/admin/rooms/RoomsReveillonPage.php';
require_once 'classes/pages/admin/rooms/RoomsWeekendPage.php';
require_once 'classes/pages/admin/eventoptions/ReveillonPage.php';
require_once 'classes/pages/admin/eventoptions/WeekendPage.php';
require_once 'classes/pages/admin/UserPaymentPage.php';

use \pages\connexion\ExterieursPage;

?>

<div class="content admin" id="adminContent">
	<section id="roomsAdmin">
		<div class="title">Gestion des chambres</div>
			<div class="event">
				<a onclick="goToPage('<?php echo \pages\admin\rooms\RoomsReveillonPage::getPage()->getPath(); ?>');">Semaine du réveillon</a>
			</div>
			<div class="event">
				<a onclick="goToPage('<?php echo \pages\admin\rooms\RoomsWeekendPage::getPage()->getPath(); ?>');">Weekend JSP</a>
			</div>
	</section>
	<section id="optionsAdmin">
		<div class="title">Gestion des options d'inscription</div>
			<div class="event">
				<a onclick="goToPage('<?php echo \pages\admin\eventoptions\ReveillonPage::getPage()->getPath(); ?>');">Semaine du réveillon</a>
			</div>
			<div class="event">
				<a onclick="goToPage('<?php echo \pages\admin\eventoptions\WeekendPage::getPage()->getPath(); ?>');">Weekend JSP</a>
			</div>
	</section>
	<section id="participantsAdmin">
		<div class="title">Gestion des participants</div>
		<a onclick="goToPage('<?php echo \pages\admin\UserPaymentPage::getPage()->getPath(); ?>');">Suivi des paiements</a>
	</section>
</div>