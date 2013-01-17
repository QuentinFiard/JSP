<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
global $user;
?>
<?php if(isset($_POST['unsetBus'])) { ?>
<div class="alert_box_wrapper" id="busSuccessBox">
	<div class="alert_box">
		<div class="title">Désinscription du bus précédent</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Tu as été correctement ôté du bus précédent.
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="window.location.reload(true);"/>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if(isset($_POST['setBus'])) { ?>
<div class="alert_box_wrapper" id="busSuccessBox">
	<div class="alert_box">
		<div class="title">Inscription dans un nouveau bus</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Ton inscription dans le bus s'est déroulée sans encombre.
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="window.location.reload(true);"/>
			</div>
		</div>
	</div>
</div>
<?php } ?>