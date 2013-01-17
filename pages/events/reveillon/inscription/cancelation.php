<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
global $user;
?>
<div class="alert_box_wrapper" id="cancelReservationBox">
	<div class="alert_box">
		<div class="title">Annuler l'inscription</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Tu es sur le point d'annuler ton inscription à la semaine du réveillon. Si tu décidais de t'inscrire de nouveau, tu risquerais de passer derrière tous les nouveaux inscrits sur la liste d'attente. Es-tu sûr(e) de vouloir annuler ton inscription<?php if(!$user->hasToPayForEvent($this->getEvent())) {?> (nous déchirerions tes chèques le cas échéant)<?php } ?> ?
				</div>
				<form method="post" action="<?php echo $currentPage->getPath(); ?>">
					<input type="hidden" name="confirmCancelation" value="true" />
					<input class="warningButton" type="button" value="Annuler" onclick="hideAlertBox();" />
					<input class="primaryButton" type="submit" value="Confirmer" />
				</form>
			</div>
		</div>
	</div>
</div>