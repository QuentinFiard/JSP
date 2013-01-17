<?php
use utilities\Server;

require_once('classes/utilities/Server.php');

global $currentPage;
?>
<?php if(!$currentPage->isCreateNewAccountPage()) {?>
<div class="content exterieursContent full">
<?php } else { ?>
<div class="content exterieursContent">
<?php } ?>
	<div id="wrapper">
		<div class="menu">
			<div class="content">
				<?php if(!$currentPage->isCreateNewAccountPage()) {?>
				<div class="title">Connexion</div>
				<div class="section" id="knownUser">
					<div class="title">Vous avez déjà un compte ?</div>
					<form action="<?php echo Server::getServerRoot(); ?>login/ext" method="POST">
						<input class="type" type="hidden" name="login" value="true" />
						<input class="sha" type="hidden" name="sha" value="false" />
						<fieldset>
							<div class="field">
								<label id="mailKnownUserLable" for="mailKnownUser"><img src="<?php echo Server::getServerRoot(); ?>img/mail_black.png" /></label>
								<input id="mailKnownUser" name="mail" type="email" required="required" placeholder="Adresse mail" />
							</div>
							<div class="field">
								<label id="passwordKnownUserLabel" for="passwordKnownUser"><img src="<?php echo Server::getServerRoot(); ?>img/key_black.png" /></label>
								<input id="passwordKnownUser" name="password" type="password" required="required" placeholder="Mot de passe" />
							</div>
						</fieldset>
						<div class="buttons">
							<input class="primaryButton" type="submit" value="Connexion" />
						</div>
					</form>
				</div>
				<?php } else { ?>
				<div class="title">Créer un compte</div>
				<?php } ?>
				<div class="section" id="newUser">
					<?php if(!$currentPage->isCreateNewAccountPage()) {?>
					<div class="title">Créer un compte</div>
					<?php } else { ?>
					<div class="title">Données personnelles</div>
					<?php } ?>
					<form action="<?php echo Server::getServerRoot(); ?>connexion/exterieurs/creercompte" method="POST">
						<input class="type" type="hidden" name="createAccount" value="true" />
						<input class="sha" type="hidden" name="sha" value="false" />
						<fieldset>
							<div class="field">
								<label id="mailNewUserLabel" for="mailNewUser"><img src="<?php echo Server::getServerRoot(); ?>img/mail_black.png" /></label>
								<input id="mailNewUser" name="mail" type="email" required="required" placeholder="Adresse mail" />
							</div>
							<div class="field">
								<label id="passwordLabel" for="passwordNewUser"><img src="<?php echo Server::getServerRoot(); ?>img/key_black.png" /></label>
								<input id="passwordNewUser" name="password" type="password" required="required" placeholder="Mot de passe" />
							</div>
							<div class="field">
								<label id="confirmPasswordLabel" for="passwordConfirm"><img src="" /></label>
								<input id="passwordConfirm" name="passwordConfirm" required="required" type="password" placeholder="Confirmer le mot de passe" />
							</div>
							<div class="field">
								<label id="firstnameLabel" for="firstnameNewUser"><img src="<?php echo Server::getServerRoot(); ?>img/user_black.png" /></label>
								<input id="firstnameNewUser" name="firstname" type="text" required="required" placeholder="Prénom" />
							</div>
							<div class="field">
								<label id="lastnameLabel" for="lastnameNewUser"><img src="" /></label>
								<input id="lastnameNewUser" name="lastname" type="text" required="required" placeholder="Nom de famille" />
							</div>
							<div class="checkboxField">
								<div class="input_wrapper">
									<input type="checkbox" id="isCadreX" name="isCadreX" type="text" />
								</div>
								<label id="isCadreXLabel" for="isCadreX">Cadre militaire de l'École polytechnique <br/>(validation nécessaire)</label>
							</div>
							<div class="field captcha">
								<img id="captcha" src="<?php echo Server::getServerRoot(); ?>securimage/securimage_show.php" alt="CAPTCHA Image" />
								<input id="captcha_code" type="text" name="captcha_code" required="required" placeholder="Code de sécurité" size="10" maxlength="6" />
								<a id="newCaptcha" href="#" onclick="document.getElementById('captcha').src = '<?php echo Server::getServerRoot(); ?>securimage/securimage_show.php?' + Math.random(); return false">Different Image</a>
							</div>
						</fieldset>
						<div class="buttons">
							<input class="primaryButton" type="submit" required="required" value="Confirmer" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>