<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
?>
<div class="alert_box_wrapper" id="createAccountSuccessBox">
	<div class="alert_box">
		<div class="title">Création d'un nouveau compte</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Votre compte a été créé avec succès. <?php if($_POST['isCadreX']) { ?>Nous avons tenu compte de votre demande d'inscription en tant que cadre de l'École polytechnique, et cette demande est en cours de validation.<?php } ?>
					<b style="color:#a00;">Avant que votre compte ne soit activé, il vous est nécessaire de confirmer votre adresse mail.</b> 
					Nous venons pour cela de vous envoyer un mail contenant le lien qui vous permettra de confirmer votre adresse. Il vous suffit de cliquer dessus et vous pourrez ensuite vous connecter sur le site et profitez des évênements organisés. À tout de suite !
				</div>
				<input class="primaryButton" type="submit" value="Confirmer" onclick="redirectToRoot();"/>
			</div>
		</div>
	</div>
</div>