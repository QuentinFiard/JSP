<?php
use utilities\Server;

require_once 'classes/utilities/Server.php';
global $currentPage;
?>
<div class="alert_box_wrapper" id="changePasswordBox">
	<div class="alert_box">
		<div class="title">Changer de mot de passe</div>
		<div class="content_wrapper">
			<div class="content">
				<form action="<?php echo Server::getServerRoot().substr($currentPage->getPath(),1); ?>">
					<input type="hidden" name="changePassword" value="true" />
					<input class="sha" type="hidden" name="sha" value="false" />
					<input class="sha_old" type="hidden" name="sha_old" value="false" />
					<div class="field">
						<label for="old_password">Ancien mot de passe :</label>
						<input id="old_password" type="password" name="old_password" required="required" placeholder="Ancien mot de passe" />
					</div>
					<div class="field">
						<label for="password">Nouveau mot de passe :</label>
						<input id="password" type="password" name="password" required="required" placeholder="Mot de passe" />
					</div>
					<div class="field">
						<label for="passwordConfirm">Confirmez le mot de passe :</label>
						<input id="passwordConfirm" type="password" name="passwordConfirm" required="required" placeholder="Confirmer le mot de passe" />
					</div>
					<input class="warningButton" type="button" value="Annuler" onclick="hideAlertBox();" />
					<input class="primaryButton" type="submit" value="Confirmer" />
				</form>
			</div>
		</div>
	</div>
</div>