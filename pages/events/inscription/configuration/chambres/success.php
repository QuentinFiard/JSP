<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
global $user;
?>
<?php if(isset($_POST['unsetRoom'])) { ?>
<div class="alert_box_wrapper" id="roomSuccessBox">
	<div class="alert_box">
		<div class="title">Désinscription de la chambre précédente</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Tu as été correctement ôté de la chambre précédente.
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="window.location.reload(true);"/>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if(isset($_POST['setRoom'])) { ?>
<div class="alert_box_wrapper" id="roomSuccessBox">
	<div class="alert_box">
		<div class="title">Inscription dans une nouvelle chambre</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Ton inscription dans la chambre s'est déroulée sans encombre.
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="window.location.reload(true);"/>
			</div>
		</div>
	</div>
</div>
<?php } ?>