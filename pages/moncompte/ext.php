<?php
use pages\EventsPage;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once('classes/pages/EventsPage.php');

global $currentPage;
global $user;
?>
<div class="content ext" id="monCompteContent">
	<div id="wrapper">
		<div class="menu">
			<div class="title">Mon compte</div>
			<div class="content">
				<section id="personalData">
					<div class="title">Données personnelles</div>
					<section id="frankizData">
						<div class="title">Contact</div>
						<div class="content">
							<div class="fieldset">
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Nom</div>
									<input class="value" style="text-align:left;" type="text" name="lastname" value="<?php echo htmlspecialchars($user->getLastName()); ?>" />
									<input class="old_value" type="hidden" name="old_lastname" value="<?php echo htmlspecialchars($user->getLastName()); ?>" />
									<input type="submit" class="button" value="Modifier">
								</form>
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Prénom</div>
									<input class="value" style="text-align:left;" type="text" name="firstname" value="<?php echo htmlspecialchars($user->getFirstName()); ?>" />
									<input class="old_value" type="hidden" name="old_firstname" value="<?php echo htmlspecialchars($user->getFirstName()); ?>" />
									<input type="submit" class="button" value="Modifier">
								</form>
								<div class="field">
									<div class="name">Adresse mail</div>
									<div class="value"><?php echo $user->getEmail(); ?></div>
								</div>
								<div class="field">
									<div class="name">Mot de passe</div>
									<input style="height:8px;line-height:10px" class="primaryButton" type="submit" onclick="showChangePasswordBox();" value="Changer" />
								</div>
							</div>
						</div>
					</section>
					<section id="otherData">
						<div class="title">Autres données</div>
						<div class="content">
							<div class="title">Ces informations sont nécessaires pour louer du matériel pendant les évênements.</div>
							<div class="fieldset">
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Masse</div>
									<input class="value" type="number" name="weight" value="<?php echo htmlspecialchars($user->getWeight()); ?>" />
									<input class="old_value" type="hidden" name="old_weight" value="<?php echo htmlspecialchars($user->getWeight()); ?>" />
									<div class="unit">kg</div>
									<input type="submit" class="button" value="Modifier">
								</form>
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Taille</div>
									<input class="value" type="number" name="height" value="<?php echo htmlspecialchars(100*$user->getHeight()); ?>" />
									<input class="old_value" type="hidden" name="old_height" value="<?php echo htmlspecialchars(100*$user->getHeight()); ?>" />
									<div class="unit">cm</div>
									<input type="submit" class="button" value="Modifier">
								</form>
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Pointure</div>
									<input class="value" type="number" name="size" value="<?php echo htmlspecialchars($user->getSize()); ?>" />
									<input class="old_value" type="hidden" name="old_size" value="<?php echo htmlspecialchars($user->getSize()); ?>" />
									<div class="unit">&nbsp;</div>
									<input type="submit" class="button" value="Modifier">
								</form>
							</div>
						</div>
					</section>
				</section>
				<section id="inscriptions">
					<div class="title">Inscriptions</div>
					<input class="primaryButton" type="submit" onclick="goToPage('<?php echo EventsPage::getPage()->getPath(); ?>');" value="Voir l'état de tes inscriptions" />
				</section>
			</div>
		</div>
	</div>
</div>