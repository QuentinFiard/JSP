<?php
use exceptions\NoSuchUser;

use database\Database;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/database/Database.php';
require_once 'classes/exceptions/NoSuchUser.php';
global $currentPage;

?>
<div class="content" id="homeContent">
	<img draggable="false" id="background" src="img/home_background.jpg" />
</div>
<div class="alert_box_wrapper" id="mailConfirmationBox" style="display:none;">
	<div class="alert_box">
		<div class="title">Confirmation de votre adresse mail</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
<?php 
	try{
		Database::shared()->validateExternalUserWithConfirmationId($_GET['user']);
?>
		 <b style="color:#3a0;">Votre adresse mail a été confirmée avec succès !</b> Nous vous remercions d'avoir effectué cette opération et vous souhaitons une bonne visite sur notre site.
<?php 
	}
	catch(NoSuchUser $e)
	{
?>
		<b style="color:#a00;">Votre adresse mail n'a pas pu être confirmée.</b> Le lien cliqué semble invalide, merci de nous <a href="mailto:jsp@binets.polytechnique.fr?Subject=<?php echo urlencode("Problème de validation d'une adresse mail"); ?>">contacter</a> si le compte associé n'est pas déjà activé.
<?php } ?>
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="redirectToRoot();" />
			</div>
		</div>
	</div>
</div>