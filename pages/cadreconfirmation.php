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
		<div class="title">Validation d'un cadre</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
<?php
	$selectedUser = null;
	try{
		$selectedUser = Database::shared()->getUserWithConfirmationId($_GET['user']);
		if(!$selectedUser)
		{
			throw new NoSuchUser();
		}
		Database::shared()->validateExternalUserWithConfirmationIdAsCadreX($_GET['user']);
		$selectedUser->sendCadreXConfirmationEmail();
?>
		 <b style="color:#3a0;">Merci d'avoir identifier <?php echo $selectedUser->getFullName(); ?> comme cadre, l'opération a été effectuée avec succès.</b>
<?php 
	}
	catch(NoSuchUser $e)
	{
		if($selectedUser)
		{
		?>
			<?php echo $selectedUser->getFullName(); ?> a déjà été identifié comme cadre.<?php 
		} else {
		?>
		Une erreur s'est produite, cet utilisateur n'existe pas.
<?php 
		}
	} 
?>
				</div>
				<input class="primaryButton" type="submit" value="Fermer" onclick="redirectToRoot();" />
			</div>
		</div>
	</div>
</div>