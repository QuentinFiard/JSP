<?php
use structures\events\WeekendJSP;

use utilities\Server;
require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/WeekendJSP.php';

global $user;
?>
<div class="content" id="inscriptionContent">
	<input id="eventButton" type="hidden" value="buttonEvent2" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background4.jpg" />
	<div class="wrapper">
		<div class="title">Inscription au weekend JSP</div>
		<div class="content">
			<h2>Rappel des informations essentielles</h2>
			<div class="summary first">
				<div class="title">Prix de base :</div>
				<div class="value"><?php echo WeekendJSP::shared()->priceForUser($user) ?> €</div>
			</div>
			<div class="summary">
				<div class="title">Caution :</div>
				<div class="value"><?php echo WeekendJSP::shared()->cautionForUser($user) ?> €</div>
			</div>
			<h2>Les conditions</h2>
			<p>Une fois que tu auras cliqué "S'inscrire", ton inscription sera confirmée. Nous t'indiquerons si tu es inscrit(e) sur liste principale ou sur liste d'attente suivant les places disponibles.</p>
			<p>Tu pourras ensuite personnaliser ton inscription, choisir ta chambre et tes options de location.</p>
			<p><b>Dans tous les cas, que tu sois inscrit(e) sur liste principale ou sur liste d'attente, transmets nous tes chèques (paiement + caution) le plus rapidement possible !</b> Tu peux nous les donner le midi au BôB si tu es à l'X, nous les déposer dans la boîte aux lettres des JSP à la Kès, ou nous les envoyer par courrier (pense à écrire ton nom au dos) à l'adresse :</p>
			<div class="address">
				Binet JSP<br/>
				Kès des élèves<br/>
				Ecole Polytechnique<br/>
				Route de Saclay<br/>
				91128 Palaiseau Cedex
			</div>
			Les inscrits sur liste principale ont <b><?php if($user->isX()) {?>une<?php } else {?>2<?php }?> semaine<?php if(!$user->isX()) {?>s<?php } ?></b> pour nous transmettre leur paiement sans quoi leur inscription sera annulée. Les inscrits sur liste d'attente seront contactés dans l'ordre d'inscription <b>parmi ceux dont le paiement a été reçu</b>.
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();
						?>">
				<input type="hidden" name="confirmReservation" value="true" />
				<input id="conditions_agreement" required="required" type="checkbox" name="conditions_agreement" value="true"/><label for="conditions_agreement">Je reconnais avoir pris connaissance de ces conditions et je les accepte.</label>
				<input class="primaryButton" type="submit" value="S'inscrire" />
			</form>
		</div>
	</div>
</div>