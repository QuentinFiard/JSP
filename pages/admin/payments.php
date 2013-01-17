<?php
use structures\events\WeekendJSP;

use structures\events\SemaineReveillon;

use structures\Event;

use structures\Option;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once('classes/structures/events/SemaineReveillon.php');
require_once('classes/structures/events/WeekendJSP.php');
?>

<div class="content admin" id="adminContent">
	<section id="mainSection">
		<div class="title">Gestion des paiements</div>
		<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();
						?>" id="filters">
			<input type="hidden" name="getUsers" value="true" />
			<table>
				<tr>
					<th>Évênement</th>
					<th>Numéro de reservation</th>
					<th>Prénom</th>
					<th>Nom</th>
					<th>Adresse mail</th>
				</tr>
				<tr>
					<td>
						<select name="eventId">
							<option value="<?php echo SemaineReveillon::shared()->getEventId()?>">Semaine du réveillon</option>
							<option value="<?php echo WeekendJSP::shared()->getEventId()?>">Weekend JSP</option>
						</select>
					</td>
					<td>
						<input autocomplete="off" type="text" name="reservationId" placeholder="reservationId" />
					</td>
					<td>
						<input autocomplete="off" type="text" name="firstname" placeholder="Prénom" />
					</td>
					<td>
						<input autocomplete="off" type="text" name="lastname" placeholder="Nom" />
					</td>
					<td>
						<input autocomplete="off" type="text" name="email" placeholder="Adresse mail" />
					</td>
				</tr>
			</table>
		</form>
	</section>
</div>