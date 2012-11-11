<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
?>
<div class="alert_box_wrapper" id="forgottenPasswordBox">
	<div class="alert_box">
		<div class="title">Mot de passe oublié ?</div>
		<div class="content_wrapper">
			<div class="content">
				<div class="subtitle">
					Entrez votre adresse mail pour réinitialiser votre mot de passe.
				</div>
				<form action="<?php echo Server::getServerRoot().substr($currentPage->getPath(),1); ?>">
					<input type="hidden" name="confirmForgottenPassword" value="true" />
					<div class="field">
						<label for="forgottenPasswordEmail">Adresse mail :</label>
						<input id="forgottenPasswordEmail" type="email" name="email" required="required" placeholder="Adresse mail" />
					</div>
					<div class="field">
						<label for="captcha_code">Entrez le code suivant :</label>
						<img id="captcha" src="<?php echo Server::getServerRoot(); ?>securimage/securimage_show.php" alt="CAPTCHA Image" />
						<input id="captcha_code" type="text" name="captcha_code" size="10" maxlength="6" />
						<a id="newCaptcha" href="#" onclick="document.getElementById('captcha').src = '<?php echo Server::getServerRoot(); ?>securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
					</div>
					<input class="warningButton" type="button" value="Annuler" onclick="hideAlertBox();" />
					<input class="primaryButton" type="submit" value="Confirmer" />
				</form>
			</div>
		</div>
	</div>
</div>