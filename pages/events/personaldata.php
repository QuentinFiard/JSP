<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
global $user;
$event = $this->getEvent();
?>
<div class="alert_box_wrapper" id="moreDataNeededBox">
	<div class="alert_box">
		<div class="title">Informations personnelles requises</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Nous avons besoin de plus d'informations te concernant pour t'inscrire à l'offre de location. Merci de compléter ces informations avant de continuer.
				</div>
				<form method="post" action="<?php echo $currentPage->getPath(); ?>">
					<input type="hidden" name="updateData" value="true" />
					<?php 
					foreach($_POST as $key => $value)
					{
						?><input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>" /><?php
					}
					?>
					
					<input type="hidden" name="updateLocationChoice" value="true" />
					
					<div class="field">
						<div class="name">Masse</div>
						<input class="value" type="number" name="weight" value="<?php echo htmlspecialchars($user->getWeight()); ?>" />
						<div class="unit">kg</div>
					</div>
					
					<div class="field">
						<div class="name">Taille</div>
						<input class="value" type="number" name="height" value="<?php echo htmlspecialchars(100*$user->getHeight()); ?>" />
						<div class="unit">cm</div>
					</div>
					
					<div class="field">
						<div class="name">Pointure</div>
						<input class="value" type="number" name="size" value="<?php echo htmlspecialchars($user->getSize()); ?>" />
						<div class="unit">&nbsp;</div>
					</div>
					
					
					<input class="primaryButton" type="submit" value="Confirmer" />
					<input class="warningButton" type="button" value="Annuler" onclick="hideAlertBox();"/>
				</form>
			</div>
		</div>
	</div>
</div>